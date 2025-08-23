/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        avss78: {
          'primary': '#2e3d84', // bleu foncé
          'accent': '#eee234',  // jaune
          'danger': '#e5293f',  // rouge
          'dark': '#0c0c10',    // noir
          'muted': '#909063',   // gris clair
          'brownish': '#676157' // gris brun
        }
      }
    }
  },
  plugins: [],
}
