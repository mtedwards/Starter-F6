module.exports = {
  build: {
    src: 'build/production.js',
    dest: 'build/production.min.js'
  },
  rem: {
    src: 'bower_components/REM-unit-polyfill/js/rem.js',
    dest: 'build/rem.min.js'
  },
  modernizr: {
    src: 'bower_components/modernizr/modernizr.js',
    dest: 'build/modernizr.min.js'
  }
}