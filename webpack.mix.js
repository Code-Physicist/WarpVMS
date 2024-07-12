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

mix.js("resources/js/app.js", "public/js").postCss(
    "resources/css/app.css",
    "public/css",
    [
        //
    ]
);

//Copy JS files
mix.copy("resources/js/jquery.min.js", "public/js/jquery.min.js");
mix.copy("resources/js/vue.global.prod.js", "public/js/vue.global.prod.js");
mix.copy("resources/js/custom.js", "public/js/custom.js");
mix.copy("resources/js/validator.min.js", "public/js/validator.min.js");
mix.copy("resources/js/utils.js", "public/js/utils.js");
mix.copy("resources/js/select-search.js", "public/js/select-search.js");

//Copy CSS files
mix.copy("resources/css/main.min.css", "public/css/main.min.css");

//Copy font files
mix.copyDirectory("resources/fonts", "public/fonts");

//Copy image files
mix.copyDirectory("resources/images", "public/images");
