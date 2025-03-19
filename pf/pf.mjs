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
        name: await promptInput("Name (2-50 characters): ", validateName),
        version: await promptInput("Version (e.g., 1.0.0): ", validateVersion),
        slug: await promptInput("Slug (2-50 characters): ", validateSlug)
      };
      await writeConfig(configPath, config);
    }

    if (!('rand_num' in config) || (config.rand_num && config.rand_num === '')) {
      const now = new Date();
      const month = now.toLocaleString('en-US', { month: 'long' }).toLowerCase();
      const day = String(now.getDate()).padStart(2, '0');
      const year = now.getFullYear();
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');
      config.rand_num = `${month}-${day}-${year}-${hours}-${minutes}-${seconds}`;
    }

    console.log(`⚙️  Using configuration:\n${JSON.stringify(config, null, 2)}`);

    if (!('plugin_frame' in config) || (config.plugin_frame && config.plugin_frame === '') || (config.plugin_frame && config.plugin_frame !== false)) {
      config.plugin_frame = 'PluginFrame';
    }

    // **Path resolution**
    const phpScriptPath = path.resolve(process.cwd(), 'pf', 'pf.php');
    const baseDistDir = path.resolve(process.cwd(), '.dist', 'plugin-frame');
    const distDir = `.dist/${config.slug}-${config.rand_num}`;
    const baseFileName = path.resolve(process.cwd(), distDir, 'plugin-frame.php');
    const renameFileName = path.resolve(process.cwd(), distDir, `${config.slug}.php`.toLowerCase());

    // Rename the directory
    if (fs.existsSync(baseDistDir)) {
      console.log(`🔄 Renaming ${baseDistDir} to ${distDir}`);

      let attempt = 0;
      const maxAttempts = 10;
      const retryDelay = 500; // 500ms delay between retries

      while (attempt < maxAttempts) {
        try {
          fs.accessSync(baseDistDir, fs.constants.W_OK);
          fs.renameSync(baseDistDir, distDir);
          break; // Success, exit loop
        } catch (err) {
          attempt++;
          console.warn(`⚠️ Attempt ${attempt}: Failed to rename. Retrying in ${retryDelay}ms...`);
          if (attempt >= maxAttempts) {
            throw new Error(`Failed to rename directory after ${maxAttempts} attempts. Check file locks or permissions.`);
          }
          await new Promise(res => setTimeout(res, retryDelay));
        }
      }
    } else {
      throw new Error(`Missing dist directory at ${baseDistDir}`);
    }

    // **Execute PHP processing**
    const phpCommand = `php "${phpScriptPath}" "${config.namespace}" "${config.prefix}" "${config.name}" "${config.version}" "${config.slug}" "${config.rand_num}" "${config.plugin_frame}"`;
    console.log(`🔧 Executing PHP Scoper: ${phpCommand}`);
    await executeCommand(phpCommand, process.cwd());

    // Rename the plugin main php file (if exists)
    if (fs.existsSync(baseFileName)) {
      console.log(`🔄 Renaming ${baseFileName} to ${renameFileName}`);

      let attempt = 0;
      const maxAttempts = 9;
      const retryDelay = 300;

      while (attempt < maxAttempts) {
        try {
          fs.accessSync(baseFileName, fs.constants.W_OK);
          fs.renameSync(baseFileName, renameFileName);
          break;
        } catch (err) {
          attempt++;
          console.warn(`⚠️ Attempt ${attempt}: Failed to rename plugin main file. Retrying in ${retryDelay}ms...`);
          if (attempt >= maxAttempts) {
            console.error(`❌ Failed to rename plugin main file after ${maxAttempts} attempts. Continuing without rename.`);
            break;
          }
          await new Promise(res => setTimeout(res, retryDelay));
        }
      }
    } else {
      console.log(`ℹ️  Plugin main file not found at ${baseFileName}, continuing without rename`);
    }

    // **Composer operations**
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

// **Config helpers**
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

  if (!('rand_num' in config) || (config.rand_num && config.rand_num === '')) {
    const now = new Date();
    const month = now.toLocaleString('en-US', { month: 'long' }).toLowerCase();
    const day = String(now.getDate()).padStart(2, '0');
    const year = now.getFullYear();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    config.rand_num = `${month}-${day}-${year}-${hours}-${minutes}-${seconds}`;
  }
  if (!('plugin_frame' in config) || (config.plugin_frame && config.plugin_frame === '') || (config.plugin_frame && config.plugin_frame !== false)) {
    config.plugin_frame = 'PluginFrame';
  }

  return config.namespace && config.prefix ? config : null;
}

async function writeConfig(configPath, { namespace, prefix, name, version, slug }) {
  const content = [
    `namespace = ${namespace}`,
    `prefix = ${prefix}`,
    `name = ${name}`,
    `version = ${version}`,
    `slug = ${slug}`
  ].join('\n');

  fs.writeFileSync(configPath, content);
}

// **Validators**
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

async function validateName(input) {
  const trimmed = input.trim();
  if (trimmed.length < 2 || trimmed.length > 50) {
    throw new Error('Name must be 2-50 characters');
  }
  return trimmed;
}

async function validateVersion(input) {
  const trimmed = input.trim();
  if (!/^\d+\.\d+\.\d+$/.test(trimmed)) {
    throw new Error('Version must be in format x.x.x (e.g., 1.0.0)');
  }
  return trimmed;
}

async function validateSlug(input) {
  const cleaned = input.trim().toLowerCase().replace(/[^a-z0-9-]/g, '');
  if (cleaned.length < 2 || cleaned.length > 50) {
    throw new Error('Slug must be 2-50 characters (lowercase letters, numbers, hyphens)');
  }
  return cleaned;
}

// **Start the process**
main();