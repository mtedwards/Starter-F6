module.exports = {
  dynamic: {
    options: {
      optimizationLevel: 3,
    },
    files: [{
        expand: true,
        cwd: 'img/',
        src: ['*.{png,jpg,gif}'],
        dest: 'img/build/'
    }]
  }
}