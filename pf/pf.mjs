import { exec } from 'child_process';
import path from 'path';
import readline from 'readline';
import fs from 'fs';

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

async function promptInput(question, validator) {
  return new Promise((resolve) => {
    const ask = () => {
      rl.question(question, async (answer) => {
        try {
          const input = answer.trim();
          if (validator) {
            const validated = await validator(input);
            resolve(validated);
          } else {
            resolve(input);
          }
        } catch (error) {
          console.log(`❌ ${error.message}`);
          ask();
        }
      });
    };
    ask();
  });
}

function executeCommand(command, cwd) {
  return new Promise((resolve, reject) => {
    exec(command, {
      maxBuffer: 1024 * 1024 * 10,
      cwd
    }, (error, stdout, stderr) => {
      error ? reject(`${error.message}\n${stderr}`) : resolve(stdout);
    });
  });
}

async function main() {
  try {
    console.log("🚀 Starting plugin production build...");

    // Get or create configuration
    const configPath = path.resolve(process.cwd(), 'plugin-frame.conf');
    let config = await readConfig(configPath);

    if (!config) {
      config = {
        namespace: await promptInput("Namespace (2-50 letters/numbers): ", validateNamespace),
        prefix: await promptInput("Prefix (2-10 lowercase letters): ", validatePrefix),
      };
      await writeConfig(configPath, config);
    }

    console.log(`⚙️  Using configuration:\n${JSON.stringify(config, null, 2)}`);

    // Path resolution
    const phpScriptPath = path.resolve(process.cwd(), 'pf', 'pf.php');
    const distDir = path.resolve(process.cwd(), '.dist', 'plugin-frame');

    // Validate paths
    if (!fs.existsSync(phpScriptPath)) throw new Error(`Missing pf.php at ${phpScriptPath}`);
    if (!fs.existsSync(distDir)) throw new Error(`Missing dist directory at ${distDir}`);

    // Execute PHP processing
    const phpCommand = `php "${phpScriptPath}" "${config.namespace}" "${config.prefix}" "${config.plugin_frame}"`;
    console.log(`🔧 Executing: ${phpCommand}`);
    await executeCommand(phpCommand, process.cwd());

    // Composer operations
    console.log(`🔄 Updating Composer in ${distDir}`);
    await executeCommand('composer dump-autoload', distDir);

    console.log(`🧹 Cleaning composer files`);
    await executeCommand('rm -f composer.lock composer.json', distDir);

    console.log("🎉 Production build completed successfully!");

  } catch (error) {
    console.error(`💥 Critical error: ${error}`);
    process.exit(1);
  } finally {
    rl.close();
  }
}

// Config helpers
async function readConfig(configPath) {
  if (!fs.existsSync(configPath)) return null;

  const content = fs.readFileSync(configPath, 'utf-8');
  const config = {};

  content.split('\n').forEach(line => {
    const [key, value] = line.split('=').map(s => s.trim());
    if (key && value) {
      config[key.toLowerCase()] = value === 'false' ? false : value;
    }
  });

  // Set default if not specified
  if (!('plugin_frame' in config) || (!config.plugin_frame && config.plugin_frame !== false)) {
    config.plugin_frame = 'PluginFrame';
  }

  return config.namespace && config.prefix ? config : null;
}

async function writeConfig(configPath, { namespace, prefix, plugin_frame }) {
  const content = [
    `namespace = ${namespace}`,
    `prefix = ${prefix}`,
    // `plugin_frame = ${plugin_frame || ''}`
  ].join('\n');

  fs.writeFileSync(configPath, content);
}

// Validators
async function validateNamespace(input) {
  const cleaned = input.replace(/[^a-zA-Z0-9]/g, '');
  if (cleaned.length < 2 || cleaned.length > 50) {
    throw new Error('Namespace must be 2-50 alphanumeric characters');
  }
  return cleaned;
}

async function validatePrefix(input) {
  const cleaned = input.toLowerCase().replace(/[^a-z0-9]/g, '');
  if (cleaned.length < 2 || cleaned.length > 10) {
    throw new Error('Prefix must be 2-10 lowercase alphanumeric characters');
  }
  return cleaned;
}

// Start the process
main();