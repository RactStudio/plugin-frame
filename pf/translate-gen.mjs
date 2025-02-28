import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Convert import.meta.url to __dirname for CommonJS-like behavior
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Configuration
const PLUGIN_SLUG = 'plugin-frame';
const TEXT_DOMAIN = 'plugin-frame';
const MAIN_POT_FILE = path.join(__dirname, '../languages', `${PLUGIN_SLUG}.pot`);
const OUTPUT_PO_FILE = path.join(__dirname, '../languages', `${PLUGIN_SLUG}.po`);
const OUTPUT_EN_US_POT_FILE = path.join(__dirname, '../languages', `${PLUGIN_SLUG}_en_US.pot`);
const OUTPUT_EN_US_PO_FILE = path.join(__dirname, '../languages', `${PLUGIN_SLUG}_en_US.po`);

// Main function
async function main() {
    // Check if the main POT file exists
    if (!fs.existsSync(MAIN_POT_FILE)) {
        console.error(`Main POT file not found: ${MAIN_POT_FILE}`);
        process.exit(1);
    }

    // Read the main POT file
    const potContent = fs.readFileSync(MAIN_POT_FILE, 'utf8');

    // 1. Duplicate .pot to .po with the same name
    fs.writeFileSync(OUTPUT_PO_FILE, potContent);
    console.log(`✚ PO file generated: languages/${PLUGIN_SLUG}.po`);

    // 2. Create a duplicate of the main .pot file with _en_US.pot suffix
    fs.writeFileSync(OUTPUT_EN_US_POT_FILE, potContent);
    console.log(`✚ _en_US POT file generated: languages/${PLUGIN_SLUG}_en_US.pot`);

    // 3. Create a duplicate of the _en_US.pot file and convert it to .po with _en_US suffix
    fs.writeFileSync(OUTPUT_EN_US_PO_FILE, potContent);
    console.log(`✚ _en_US PO file generated: languages/${PLUGIN_SLUG}_en_US.po`);
}

// Run the script
main().catch(err => {
    console.error('⛔ Error generating translation files:', err);
    process.exit(1);
});
