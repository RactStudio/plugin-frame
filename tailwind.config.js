/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')
module.exports = {
  prefix: 'pf-',
  darkMode: [
    'selector',
    '[data-mode="pf-dark"]',
    'variant', [
    '@media (pf-color-scheme: dark) { &:not(.light *) }',
    '&:is(.dark *)'
    ]
  ],
  content: [
    "./resources/**/*.{twig,html,php,js}"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}

