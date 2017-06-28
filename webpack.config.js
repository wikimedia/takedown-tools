const path = require( 'path' ),
	ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

module.exports = {
	entry: './client/index.js',
	output: {
		filename: 'bundle.js',
		path: path.resolve( __dirname, 'web/bundles' )
	},
	devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'cheap-module-eval-source-map',
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			{
				test: /\.css$/,
				use: ExtractTextPlugin.extract( {
					use: 'css-loader'
				} )
			}
		]
	},
	plugins: [
		new ExtractTextPlugin( 'styles.css' )
	]
};
