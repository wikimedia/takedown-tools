const path = require( 'path' );
const webpack = require( 'webpack' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const UglifyJSPlugin = require( 'uglifyjs-webpack-plugin' );

const extractSass = new ExtractTextPlugin( {
	filename: 'style.css'
} );

let config = {
	entry: './index.js',
	output: {
		filename: 'bundle.js',
		path: path.resolve( __dirname, 'html/bundles' )
	},
	devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'cheap-module-eval-source-map',
	resolve: {
		alias: {
			app: path.resolve( './src' )
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
			APP_ENV: JSON.stringify( process.env.APP_ENV ),
			// @see https://facebook.github.io/react/docs/optimizing-performance.html#webpack
			'process.env': {
				NODE_ENV: JSON.stringify( process.env.NODE_ENV )
			}
		} )
	]
};

if ( process.env.NODE_ENV === 'production' ) {
	config.plugins.push( new UglifyJSPlugin( {
		sourceMap: true,
		// Mangle throws an error with mediawiki-title.
		// @see https://github.com/wikimedia/mediawiki-title/issues/27
		mangle: false
	} ) );
}

module.exports = config;
