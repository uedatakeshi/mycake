var path = require('path');

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
  }
};
