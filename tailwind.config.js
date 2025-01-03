const plugin = require('tailwindcss/plugin'); // Import the plugin function

module.exports = {
  prefix: 'pf-', // Custom prefix for Tailwind classes
  darkMode: ['class', '[data-mode="pf-dark"]'], // Enable dark mode with custom attribute
  content: [
    "./resources/views/**/*.{twig,html,php,js}" // Specify content files
  ],
  theme: {
    extend: {
      colors: {
        gray: {
          50: '#f9fafb', // Example light gray
          800: '#1f2937', // Example dark gray
        },
        indigo: {
          400: '#6366f1', // Indigo for dark mode
          600: '#4f46e5', // Indigo for light mode
        },
      },
      transitionProperty: {
        'colors': 'background-color, color, border-color, text-decoration-color, fill, stroke',
      },
    },
  },
  plugins: [
    plugin(function({ addVariant, e }) {
      // Add custom variants for pf-dark and system modes
      addVariant('pf-dark', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => {
          return `[data-mode="pf-dark"] .${e(`pf-dark${separator}${className}`)}`;
        });
      });

      addVariant('pf-light', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => {
          return `[data-mode="pf-light"] .${e(`pf-light${separator}${className}`)}`;
        });
      });

      addVariant('pf-system', ({ modifySelectors, separator }) => {
        modifySelectors(({ className }) => {
          return `[data-mode="pf-system"] .${e(`pf-system${separator}${className}`)}`;
        });
      });
    }),
  ],
};
