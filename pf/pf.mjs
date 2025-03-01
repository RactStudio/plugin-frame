import { exec } from "child_process";
import path from "path";
import readline from "readline";
import fs from "fs";
import dotenv from "dotenv";

// Load .env file if it exists
dotenv.config();

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

function askQuestion(query) {
  return new Promise(resolve => rl.question(query, resolve));
}

// Get values from .env or ask user
async function getNamespaceAndPrefix() {
  let NEW_NAMESPACE = process.env.NEW_NAMESPACE || "";
  let NEW_PREFIX = process.env.NEW_PREFIX || "";

  if (!NEW_NAMESPACE) {
    NEW_NAMESPACE = await askQuestion("Enter NEW NAMESPACE: ");
  }
  if (!NEW_PREFIX) {
    NEW_PREFIX = await askQuestion("Enter PLUGIN PREFIX: ");
  }

  rl.close();
  return { NEW_NAMESPACE, NEW_PREFIX };
}

// Execute shell commands with increased buffer size
function execWithBuffer(command) {
  return new Promise((resolve, reject) => {
    exec(command, { maxBuffer: 1024 * 1024 }, (error, stdout, stderr) => {
      if (error) {
        reject(`Error: ${error.message}\n${stderr}`);
        return;
      }
      resolve(stdout);
    });
  });
}

// Main execution
(async () => {
  console.log("Starting plugin build process...");

  const { NEW_NAMESPACE, NEW_PREFIX } = await getNamespaceAndPrefix();

  console.log(`Using NEW_NAMESPACE: ${NEW_NAMESPACE}, NEW_PREFIX: ${NEW_PREFIX}`);

  // Resolve the pf.php script path (cross-platform)
  const phpScriptPath = path.resolve(process.cwd(), "pf", "pf.php");

  if (!fs.existsSync(phpScriptPath)) {
    console.error(`ERROR: Could not find pf.php at ${phpScriptPath}`);
    process.exit(1);
  }

  // Run the PHP script with arguments
  const command = `php "${phpScriptPath}" "${NEW_NAMESPACE}" "${NEW_PREFIX}"`;
  console.log(`Invoking PHP scoper script: ${command}`);

  try {
    const output = await execWithBuffer(command);
    console.log(output);
    console.log("✅ Plugin build completed successfully.");
  } catch (error) {
    console.error(`❌ PHP scoping process failed:\n${error}`);
    process.exit(1);
  }
})();
