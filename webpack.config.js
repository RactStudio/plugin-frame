const path = require('path');

module.exports = {
    entry: {
        admin: './resources/assets/js/admin.js',
        frontend: './resources/assets/js/frontend.js'
    },
    output: {
        filename: '[name].bundle.js',
        path: path.resolve('./resources/assets/js')
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            }
        ]
    },
    mode: 'production'
};