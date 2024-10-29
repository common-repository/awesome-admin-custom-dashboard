const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'assets/js/app.min.js')
    .sass('resources/scss/app.scss', 'assets/css/app.min.css');

mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery', 'jquery']
});
