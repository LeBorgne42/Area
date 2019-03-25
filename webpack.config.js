var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .cleanupOutputBeforeBuild()
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
     })

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
    .addStyleEntry('global', './assets/global.scss')
    .addEntry('main', './assets/main.js')
    .addEntry('flight', './assets/js/flight.js')
    .addEntry('salon', './assets/js/salon.js')
    .addEntry('spatial', './assets/js/spatial.js')
    .addEntry('planet', './assets/js/planet.js')
    .addEntry('fleet_list', './assets/js/fleet_list.js')
    .addEntry('caserne', './assets/js/caserne.js')
    .enableForkedTypeScriptTypesChecking()
;

module.exports = Encore.getWebpackConfig();
