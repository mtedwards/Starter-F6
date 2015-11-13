module.exports = {
  dist: {
    options: {
      // cssmin will minify later
      style: 'expanded'
    },
    files: {
      'build/style.css': 'scss/style.scss'
    }
  }
}
