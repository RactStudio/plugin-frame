import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import gettextParser from 'gettext-parser';

// Convert import.meta.url to __dirname for CommonJS-like behavior
const PLUGIN_SLUG = 'plugin-frame';
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Languages directory (relative to the script's location)
const LANG_DIR = path.join(__dirname, '../languages');

// console.log('Current working directory:', process.cwd());
// console.log('Resolved languages directory:', LANG_DIR);

/**
 * Deletes existing .po and .mo files before generating new ones.
 */
function cleanOldFiles() {
    const files = fs.readdirSync(LANG_DIR).filter(file => file.endsWith('.po') || file.endsWith('.mo'));
    files.forEach((file) => {
        try {
            fs.unlinkSync(path.join(LANG_DIR, file));
            console.log(`✖ Deleted old file: languages/${file}`);
        } catch (error) {
            console.error(`⛔ Error deleting file languages/${file}:`, error);
        }
    });
}

/**
 * Sanitizes the .pot content by fixing invalid escape sequences.
 * @param {string} content - The content of the .pot file.
 * @returns {string} - The sanitized content.
 */
function sanitizePotContent(content) {
    // Fix invalid escape sequences (e.g., "Here\" -> "Here\\")
    return content.replace(/([^\\])\\/g, '$1\\\\');
}

/**
 * Converts .po to .mo files.
 * @param {string} poPath - The path to the .po file.
 * @param {string} moPath - The path to save the .mo file.
 */
function convertPoToMo(poPath, moPath) {
    try {
        if (fs.existsSync(poPath)) {
            const baseName = path.basename(poPath, '.po');
            const poData = fs.readFileSync(poPath, 'utf8');
            const moData = gettextParser.mo.compile(gettextParser.po.parse(poData));
            fs.writeFileSync(moPath, moData);
            console.log(`✚ Generated .mo file: languages/${baseName}.mo`);
        } else {
            console.error(`⚠️ .po file not found: ${poPath}`);
        }
    } catch (error) {
        console.error(`⛔ Error converting .po to .mo for ${poPath}:`, error);
    }
}

/**
 * Generates .po and .mo files for each .pot file.
 */
async function generatePoMoFiles() {
    console.log('🔎 Searching for .pot files...');

    // Find all .pot files in the languages directory
    const potFiles = fs.readdirSync(LANG_DIR).filter(file => file.endsWith('.pot'));
    console.log('🟢 Found .pot files:', potFiles);

    if (potFiles.length === 0) {
        console.log('⚠️ No .pot files found!');
        return;
    }

    // Remove old .po and .mo files
    cleanOldFiles();

    // Process each .pot file
    potFiles.forEach((potFile) => {
        try {
            const baseName = path.basename(potFile, '.pot');
            const poPath = path.join(LANG_DIR, `${baseName}.po`);
            const moPath = path.join(LANG_DIR, `${baseName}.mo`);

            // Read .pot content and sanitize it
            const potContent = fs.readFileSync(path.join(LANG_DIR, potFile), 'utf8');
            const sanitizedPotContent = sanitizePotContent(potContent);

            // Write to .po file
            fs.writeFileSync(poPath, sanitizedPotContent);
            console.log(`✚ Created .po file: languages/${baseName}.po`);

            // Convert .po to .mo
            convertPoToMo(poPath, moPath);
        } catch (error) {
            console.error(`✖ Error processing .pot file ${potFile}:`, error);
        }
    });

    console.log('🎉 All translations generated successfully!');
}

// Run the process
generatePoMoFiles().catch((error) => {
    console.error('⛔ An error occurred during the process:', error);
});
