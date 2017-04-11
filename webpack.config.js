var path = require('path');
var webpack = require('webpack');

module.exports = {

  //エントリポイントのJavaScript
  entry: {
      entry: './app/Script/entry.js',
      topic: './app/Script/topic.js'
  },
  output: {
    //出力先のフォルダ
    path: path.resolve(__dirname, 'app/webroot/js'),
    //出力先のファイル名
    filename: '[name].js'
  },
  plugins: [
        //JavaScriptを圧縮する
        new webpack.optimize.UglifyJsPlugin({sourceMap: true})
  ],

    //出力するsource mapのスタイル
  devtool: '#source-map'
};
