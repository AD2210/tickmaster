const Encore = require('@symfony/webpack-encore');
 Encore
   .setOutputPath('public/build/')
   .setPublicPath('/build')
   .addEntry('app', './assets/app.js')
   .splitEntryChunks()
   .enableSingleRuntimeChunk()
   .configureBabel((config) => {
     config.sourceType = 'unambiguous';
     return config;
   });
 module.exports = Encore.getWebpackConfig();