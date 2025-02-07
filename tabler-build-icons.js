const fs = require('fs');
const path = require('path');

// Path to the icons.json file in the @tabler/icons package
const iconsJsonPath = path.resolve(__dirname, 'node_modules', '@tabler', 'icons', 'icons.json');

// Output file path
const outputFilePath = path.resolve(__dirname, 'resources', 'assets', 'icons', 'tabler-icons.js');

// Ensure the output directory exists
const outputDir = path.dirname(outputFilePath);
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

// Read the icons.json file
fs.readFile(iconsJsonPath, 'utf8', (err, data) => {
  if (err) {
    console.error('Error reading icons.json:', err);
    return;
  }

  try {
    const icons = JSON.parse(data);

    // Start the content for the tabler-icons.js file
    let content = `// Auto-generated Tabler Icons File\n`;
    content += `export const TablerIcons = {\n`;

    // Iterate over each icon and add it to the content
    Object.entries(icons).forEach(([name, svg]) => {
      content += `  "${name}": \`${svg}\`,\n`;
    });

    content += `};\n`;

    // Write the content to the tabler-icons.js file
    fs.writeFileSync(outputFilePath, content, 'utf8');
    console.log(`✅ Tabler Icons generated successfully: ${outputFilePath}`);
  } catch (parseError) {
    console.error('Error parsing icons.json:', parseError);
  }
});
