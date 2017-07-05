const path = require( 'path' ),
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
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			{
				test: /\.scss$/,
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
			},
			{
				test: /\.css$/,
				use: extractSass.extract( {
					use: 'css-loader'
				} )
			}
		]
	},
	plugins: [
		extractSass
	]
};
