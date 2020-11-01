const mix = require('laravel-mix');

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

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js')
        }
    }
})

mix.js('resources/js/app.js', 'public/js/app.js');
mix.js('resources/js/onboard.js', 'public/js/onboard.js');
mix.sass('resources/sass/app.scss', 'public/css', [

]);
mix.sass('resources/sass/onboard.scss', 'public/css', [

]);

mix.copyDirectory('resources/images', 'public/images');

if (mix.inProduction()) {
    mix.version();
}
