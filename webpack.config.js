require( 'dotenv' ).config();

const path = require( 'path' ),
	webpack = require( 'webpack' ),
	ExtractTextPlugin = require( 'extract-text-webpack-plugin' ),
	extractSass = new ExtractTextPlugin( {
		filename: 'style.css',
		disable: process.env.NODE_ENV !== 'production'
	} );

module.exports = {
	entry: './client/index.js',
	output: {
		filename: 'bundle.js',
		path: path.resolve( __dirname, 'public/bundles' )
	},
	devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'cheap-module-eval-source-map',
	resolve: {
		alias: {
			app: path.resolve( './client' )
		}
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			{
				test: /\.scss$|\.css$/,
				use: extractSass.extract( {
					use: [
						{
							loader: 'css-loader'
						},
						{
							loader: 'sass-loader'
						}
					],
					// use style-loader in development
					fallback: 'style-loader'
				} )
			}
		]
	},
	plugins: [
		extractSass,
		new webpack.DefinePlugin( {
			APP_ENV: JSON.stringify( process.env.APP_ENV )
		} )
	]
};
