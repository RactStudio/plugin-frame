/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
      "./resources/**/*.{twig,html,php,js}"
    ],
    prefix: 'pf-',
    darkMode: ['selector', '[data-mode="pf-dark"]'],
    important: '#pf-load',
    theme: {
      extend: {},
    },
    plugins: [],
}