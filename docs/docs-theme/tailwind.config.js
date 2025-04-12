module.exports = {
  content: [
    "../public/**/*.html",
    "../templates/**/*.twig"
  ],
  theme: {
    extend: {
      colors: {
        'wp-blue': '#21759b',
        'wp-gray': '#f1f1f1'
      }
    }
  },
  plugins: [
    require('@tailwindcss/typography')
  ]
}