/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin');

module.exports = {
  prefix: 'pf-', // Custom prefix
  darkMode: 'class', // Enable dark mode with class strategy
  content: [
    "./resources/views/**/*.{twig,html,php,js}" // Specify content files
  ],
  theme: {
    extend: {},
  },
  plugins: [
    plugin(function({ addVariant, e }) {
      // Add custom dark variants
      addVariant('pf-dark', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => {
          return `[data-mode="pf-dark"] .${e(`pf-dark${separator}${className}`)}`;
        });
      });
      addVariant('pf-dark-variant', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => {
          return `@media (prefers-color-scheme: dark) { .${e(`pf-dark-variant${separator}${className}`)} }`;
        });
      });
    })
  ],
};
