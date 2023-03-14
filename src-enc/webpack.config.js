const path = require('path');
const webpack = require('webpack');

module.exports = {
    plugins: [],
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
        },
    },
};