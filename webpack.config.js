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
    .addEntry('core', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/core.js')
    .addEntry('login', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/login.js')
    .addEntry('inputCPR', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/inputCPR.js')
    .addEntry('reminderLatest', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/reminderLatest.js')
    .addEntry('sortSubmit', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/sortSubmit.js')
    .addEntry('journalQuickview', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/journalQuickview.js')
    .addEntry('processFilters', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/processFilters.js')
    .addEntry('processForm', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/processForm.js')
    .addEntry('processStatusForm', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/processStatusForm.js')
    .addEntry('globalSearch', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/globalSearch.js')
    .addEntry('searchPage', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/searchPage.js')
    .addEntry('journalRevisionToggle', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/journalRevisionToggle.js')
    .addEntry('cars', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/cars.js')
    .addEntry('processReport', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/processReport.js')
    .addEntry('export', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/export.js')
    .addEntry('processStatusChangeToggle', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/processStatusChangeToggle.js')
    .addEntry('addEconomyEntry', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/addEconomyEntry.js')
    .addEntry('revenueForm', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/revenueForm.js')
    .addEntry('dashboard', './vendor/kontrolgruppen/core-bundle/Resources/assets/js/dashboard.js')

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
