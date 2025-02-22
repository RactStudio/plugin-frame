import fs from 'fs';
import path from 'path';
import glob from 'fast-glob';

// Configuration
const PLUGIN_SLUG = 'plugin-frame';
const TEXT_DOMAIN = 'plugin-frame';
const OUTPUT_FILE = path.join('languages', `${PLUGIN_SLUG}_twig.pot`);
const VIEWS_DIRECTORIES = ['resources/views', 'app/Views'];
const EXTENSIONS = ['twig', 'html'];

// Create POT structure
const potHeader = `

`;

// Regex to match translation functions
const TRANSLATION_REGEX = /{{\s*(__|_e|_n|_x)\(['"](.*?)['"](?:,\s*['"](.*?)['"](?:,\s*['"](.*?)['"])?)?/g;

// Extract translations from a file
function extractTranslations(filePath) {
    const content = fs.readFileSync(filePath, 'utf8');
    const matches = [];
    let match;

    while ((match = TRANSLATION_REGEX.exec(content)) !== null) {
        const [, func, text, contextOrPlural, domainOrNumber] = match;
        const line = (content.substring(0, match.index).match(/\n/g)?.length + 1 || 1);

        // Filter by domain
        const domain = func === '_x' || func === '_n' ? domainOrNumber : contextOrPlural;
        if (domain !== TEXT_DOMAIN && domain !== undefined) continue;

        switch (func) {
            case '__':
            case '_e':
                matches.push({ type: 'single', text, line });
                break;

            case '_x':
                matches.push({ type: 'context', text, context: contextOrPlural, line });
                break;

            case '_n':
                matches.push({ type: 'plural', single: text, plural: contextOrPlural, line });
                break;
        }
    }

    return matches;
}

// Generate POT file content
function generatePotContent(translations) {
    let potContent = potHeader;
    const entriesMap = new Map(); // Track entries with multiple locations

    translations.forEach(({ file, matches }) => {
        matches.forEach(({ type, text, context, single, plural, line }) => {
            const entryKey = `${type}:${text}:${context || ''}:${single || ''}:${plural || ''}`;

            if (!entriesMap.has(entryKey)) {
                entriesMap.set(entryKey, {
                    type,
                    text,
                    context,
                    single,
                    plural,
                    locations: [],
                });
            }

            // Add location to the entry
            entriesMap.get(entryKey).locations.push({ file, line });
        });
    });

    // Generate POT content from the map
    entriesMap.forEach(({ type, text, context, single, plural, locations }) => {
        // Add all locations
        locations.forEach(({ file, line }) => {
            potContent += `#: ${file}:${line}\n`;
        });

        // Add the translation entry
        switch (type) {
            case 'single':
                potContent += `msgid "${text}"\n`;
                potContent += 'msgstr ""\n\n';
                break;

            case 'context':
                potContent += `msgctxt "${context}"\n`;
                potContent += `msgid "${text}"\n`;
                potContent += 'msgstr ""\n\n';
                break;

            case 'plural':
                potContent += `msgid "${single}"\n`;
                potContent += `msgid_plural "${plural}"\n`;
                potContent += 'msgstr[0] ""\n';
                potContent += 'msgstr[1] ""\n\n';
                break;
        }
    });

    return potContent;
}

// Main function
async function main() {
    // Find all Twig files
    const files = await glob(
        VIEWS_DIRECTORIES.map(dir => `${dir}/**/*.{${EXTENSIONS.join(',')}}`)
    );

    // Extract translations
    const translations = files.map(file => ({
        file,
        matches: extractTranslations(file),
    })).filter(({ matches }) => matches.length > 0);

    // Generate POT file
    const potContent = generatePotContent(translations);
    fs.writeFileSync(OUTPUT_FILE, potContent);

    console.log(`Twig translations extracted to ${OUTPUT_FILE}`);

    // Merge with main POT file if exists
    const mainPotFile = path.join('languages', `${PLUGIN_SLUG}.pot`);
    if (fs.existsSync(mainPotFile)) {
        const mainPotContent = fs.readFileSync(mainPotFile, 'utf8');
        const mergedPotContent = mainPotContent + '\n' + potContent;
        fs.writeFileSync(mainPotFile, mergedPotContent);
        console.log(`Merged translations into ${mainPotFile}`);

        // Delete the temporary Twig POT file
        fs.unlinkSync(OUTPUT_FILE);
        console.log(`Deleted temporary file: ${OUTPUT_FILE}`);
    }
}

// Run the script
main().catch(err => {
    console.error('Error extracting translations:', err);
    process.exit(1);
});