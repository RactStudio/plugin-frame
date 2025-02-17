module.exports = {
    plugins: [
        require('@tailwindcss/postcss')('./tailwind.config.js'),  // Ensure Tailwind loads the correct config
        require('autoprefixer'),
        require('postcss-prefixwrap')('#pf-load', {
            whitelist: ['body', 'html'], // Optional: Prevents wrapping of these selectors
        })
    ]
};
