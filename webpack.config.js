var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath(Encore.isProduction() ? 'public/prod' : 'public/build')
    // public path used by the web server to access the output path
    .setPublicPath(Encore.isProduction() ? '/prod' : '/build')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('core', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/core.js')
    .addEntry('login', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/login.js')
    .addEntry('inputCPR', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/inputCPR.js')
    .addEntry('reminderLatest', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/reminderLatest.js')
    .addEntry('sortSubmit', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/sortSubmit.js')
    .addEntry('journalQuickview', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/journalQuickview.js')
    .addEntry('processFilters', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/processFilters.js')
    .addEntry('processForm', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/processForm.js')
    .addEntry('processStatusForm', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/processStatusForm.js')
    .addEntry('globalSearch', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/globalSearch.js')
    .addEntry('searchPage', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/searchPage.js')
    .addEntry('journalRevisionToggle', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/journalRevisionToggle.js')
    .addEntry('cars', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/cars.js')
    .addEntry('processReport', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/processReport.js')
    .addEntry('export', './bundles/kontrolgruppen-core-bundle/Resources/assets/js/export.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .enableSassLoader()
    .enableIntegrityHashes()
    .autoProvidejQuery()
;

let config = Encore.getWebpackConfig();

config.resolve.alias = {
    jquery: __dirname + '/node_modules/jquery/dist/jquery'
};

module.exports = config;
