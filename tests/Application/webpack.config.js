const path = require('path');
const Encore = require('@symfony/webpack-encore');

const SyliusAdmin = require('@sylius-ui/admin');
const SyliusShop = require('@sylius-ui/shop');

const [bitbagWishlistShop, bitbagWishlistAdmin] = require('../../webpack.config.js')

// Admin config
const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));

// Shop config
const shopConfig = SyliusShop.getWebpackConfig(path.resolve(__dirname));

// Shop config
Encore
    .setOutputPath('public/build/app/shop')
    .setPublicPath('/build/app/shop')
    .enableStimulusBridge('./assets/controllers.json')
    .addEntry('app-shop-entry', './assets/shop/entry.js')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()

const appShopConfig = Encore.getWebpackConfig();

appShopConfig.externals = Object.assign({}, appShopConfig.externals, { window: 'window', document: 'document' });
appShopConfig.name = 'app.shop';

Encore.reset();

// App admin config
Encore
    .setOutputPath('public/build/app/admin')
    .setPublicPath('/build/app/admin')
    .enableStimulusBridge('./assets/controllers.json')
    .addEntry('app-admin-entry', './assets/admin/entry.js')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader();

const appAdminConfig = Encore.getWebpackConfig();

appAdminConfig.externals = Object.assign({}, appAdminConfig.externals, { window: 'window', document: 'document' });
appAdminConfig.name = 'app.admin';

module.exports = [shopConfig, adminConfig, appShopConfig, appAdminConfig, bitbagWishlistShop, bitbagWishlistAdmin];
