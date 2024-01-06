const mix = require("laravel-mix");
const fs = require('fs');
const wpPot = require("wp-pot");

mix.options({
    autoprefixer: {
        remove: false,
    },
    processCssUrls: false,
    terser: {
        terserOptions: {
            keep_fnames: true
        }
    }
});

mix.webpackConfig({
    target: "web",
    externals: {
        jquery: "window.jQuery",
        $: "window.jQuery",
        wp: "window.wp",
        _copy_to_clipboard: "window._copy_to_clipboard",
    },
});

mix.sourceMaps(false, 'source-map');

// Disable notification on dev mode
if (process.env.NODE_ENV.trim() !== "production") mix.disableNotifications();

if (process.env.NODE_ENV.trim() === 'production') {

    // Language pot file generator
    wpPot({
        destFile: "languages/copy-to-clipboard.pot",
        domain: "copy-to-clipboard",
        package: "copy-to-clipboard",
        src: "**/*.php",
    });
}

// SCSS to CSS
mix.sass("dev/scss/sdk.scss", "assets/css/copy-to-clipboard-sdk.min.css");
mix.sass("dev/scss/survey.scss", "assets/css/copy-to-clipboard-survey.css");


// mix.sass("dev/scss/admin-settings.scss", "assets/css/copy-to-clipboard-admin-settings.min.css");
// mix.sass("dev/scss/premium/copy-to-clipboard-pro-styles.scss", "Pro/assets/css/copy-to-clipboard-pro.min.css");

// Scripts to js - regular
// mix.scripts( 'dev/js/copy-to-clipboard.js', 'assets/js/copy-to-clipboard.js' );


// Third Party Plugins Support
// fs.readdirSync('dev/scss/plugins').forEach(
//     file => {
//         mix.sass('dev/scss/plugins/' + file, 'assets/css/plugins/' + file.substring(1).replace('.scss', '.min.css'));
//     }
// );

// fs.readdirSync('dev/scss/premium/plugins/').forEach(
//     file => {
//         mix.sass('dev/scss/premium/plugins/' + file, 'Pro/assets/css/plugins/' + file.substring(1).replace('.scss', '.min.css'));
//     }
// );
