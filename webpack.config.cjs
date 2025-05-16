// webpack.config.cjs
const Encore = require('@symfony/webpack-encore');

// Paramétrage de base
Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/app.js')
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
;

// Récupère la config générée par Encore
const config = Encore.getWebpackConfig();

// Force Webpack à accepter les imports/exports dans les .js
config.module = config.module || {};
config.module.rules = config.module.rules || [];
config.module.rules.push({
  test: /\.js$/,
  type: 'javascript/auto'
});

module.exports = config;
