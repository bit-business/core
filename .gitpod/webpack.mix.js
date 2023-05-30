const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js("resources/js/app.js", "public/js")
  .postCss("resources/css/app.css", "public/css", [
    //
  ]);

// Skijasi
mix
  .js("packages/skijasi/core/src/resources/js/app.js", "public/js/skijasi.js")
  .sass(
    "packages/skijasi/core/src/resources/js/assets/scss/style.scss",
    "public/css/skijasi.css"
  )
  .vue();
