
'use strict';

import plugins  from 'gulp-load-plugins';
import yargs    from 'yargs';
import gulp     from 'gulp';
import yaml     from 'js-yaml';
import fs       from 'fs';


// Load all Gulp plugins into one variable
const $ = plugins();

// Check for --production flag
const PRODUCTION = !!(yargs.argv.production);

// Load settings from config.yml
const { PATHS } = loadConfig();

function loadConfig() {
  let ymlFile = fs.readFileSync('config.yml', 'utf8');
  return yaml.load(ymlFile);
}

// Build the JavaScript
gulp.task('build', javascript);

// Build the site, run the server, and watch for file changes
gulp.task('default', watch);

// In production, the file is minified
function javascript() {
  return gulp.src(PATHS.javascript)
    // .pipe($.sourcemaps.init())
    // .pipe($.babel({ignore: ['what-input.js']}))
    .pipe($.concat('ubc-press-js.js'))
    .pipe($.if(PRODUCTION, $.uglify()
      .on('error', e => { console.log(e); })
    ))
    // .pipe($.if(!PRODUCTION, $.sourcemaps.write()))
    .pipe(gulp.dest('js'));
}

// Watch for changes to js in src/UBC/Press/Theme/assets/js/*.js
function watch() {
  gulp.watch('src/UBC/Press/Theme/assets/js/ubc-press-ajax.js' ).on('all', gulp.series(javascript));
}
