const InitialConfig = require('@wordpress/scripts/config/webpack.config');
const autoprefixer = require("autoprefixer");
const webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");


module.exports = {
	...InitialConfig,
	entry: {
		'guten-latest': './blocks/guten-latest/',


		'guten-user': [
			"./blocks/guten-latest/EditPart/css/user/User.scss"
		],
	},
};
