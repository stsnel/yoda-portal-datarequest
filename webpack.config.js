var webpack = require('webpack');
var path = require('path');

var BUILD_DIR = path.resolve(__dirname, 'public/static/js');
var APP_DIR = path.resolve(__dirname, 'app');

var config = {
    entry: {'datarequest/add': APP_DIR + '/datarequest/add.js',
            'datarequest/preliminaryreview': APP_DIR + '/datarequest/preliminaryreview.js',
            'datarequest/view': APP_DIR + '/datarequest/view.js',
            'datarequest/review': APP_DIR + '/datarequest/review.js',
            'datarequest/evaluate': APP_DIR + '/datarequest/evaluate.js'},
    output: {
        path: BUILD_DIR,
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.js?/,
                include : APP_DIR,
                use: {
                    loader: 'babel-loader'
                }
            }
        ]
    }
};

module.exports = config;
