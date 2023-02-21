const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('addEconomyEntry', './core/Resources/assets/js/addEconomyEntry.js')
    .addEntry('cars', './core/Resources/assets/js/cars.js')
    .addEntry('companies', './core/Resources/assets/js/companies.js')
    .addEntry('core', './core/Resources/assets/js/core.js')
    .addEntry('dashboard', './core/Resources/assets/js/dashboard.js')
    .addEntry('export', './core/Resources/assets/js/export.js')
    .addEntry('globalSearch', './core/Resources/assets/js/globalSearch.js')
    .addEntry('inputCPR', './core/Resources/assets/js/inputCPR.js')
    .addEntry('journalEntryPreventDoubleSubmission', './core/Resources/assets/js/journalEntryPreventDoubleSubmission.js')
    .addEntry('journalQuickview', './core/Resources/assets/js/journalQuickview.js')
    .addEntry('journalRevisionToggle', './core/Resources/assets/js/journalRevisionToggle.js')
    .addEntry('login', './core/Resources/assets/js/login.js')
    .addEntry('people', './core/Resources/assets/js/people.js')
    .addEntry('processFilters', './core/Resources/assets/js/processFilters.js')
    .addEntry('processForm', './core/Resources/assets/js/processForm.js')
    .addEntry('processFormCprSearch', './core/Resources/assets/js/processFormCprSearch.js')
    .addEntry('processFormCvrSearch', './core/Resources/assets/js/processFormCvrSearch.js')
    .addEntry('processGroupForm', './core/Resources/assets/js/processGroupForm.js')
    .addEntry('processReport', './core/Resources/assets/js/processReport.js')
    .addEntry('processStatusChangeToggle', './core/Resources/assets/js/processStatusChangeToggle.js')
    .addEntry('processStatusForm', './core/Resources/assets/js/processStatusForm.js')
    .addEntry('reminderLatest', './core/Resources/assets/js/reminderLatest.js')
    .addEntry('revenueForm', './core/Resources/assets/js/revenueForm.js')
    .addEntry('searchPage', './core/Resources/assets/js/searchPage.js')
    .addEntry('sortSubmit', './core/Resources/assets/js/sortSubmit.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

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

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

let config = Encore.getWebpackConfig();

config.resolve.alias = {
    jquery: __dirname + '/node_modules/jquery/dist/jquery'
};

module.exports = config;
