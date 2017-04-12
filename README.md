# mycake

まずはcomposer.jsonを作ってインストールします。

```bash
mkdir cakephp
cd cakephp
vim composer.json
```

```json:composer.json
{
    "name": "myapp",
    "repositories": [
        {
            "type": "pear",
            "url": "http://pear.cakephp.org"
        }
    ],
    "require": {
        "cakephp/cakephp": ">=2.9.0,<3.0.0",
        "cakedc/migrations": "~2.4.0",
        "phpoffice/phpexcel": "*",
        "smottt/wideimage": "~1.1.3",
        "josegonzalez/cakephp-upload": "2.x-dev",
        "tecnickcom/tcpdf": "~6.2.12"
    },
    "require-dev": {
        "phpunit/phpunit": "3.7.*",
        "cakephp/debug_kit" : "^2.2.0",
        "cakephp/cakephp-codesniffer": "^1.0.0"
    },
    "config": {
        "vendor-dir": "Vendor/",
        "secure-http": false
    }
}
```

```bash
sudo composer install
```

macの場合はここでパーミッション設定。


```bash
sudo chown -R mymac:staff ../cakephp/
```

codesnifferのパスを通しておく。

```bash
Vendor/bin/phpcs --config-set installed_paths Vendor/cakephp/cakephp-codesniffer
```


CakePHPのbakeコマンドでアプリケーションを作ります。


```
Vendor/bin/cake bake project app
```

git対策で空のファイルを作っておく。


```bash
touch app/tmp/cache/models/empty
touch app/tmp/cache/persistent/empty
touch app/tmp/cache/views/empty
touch app/tmp/logs/empty
touch app/tmp/sessions/empty
touch app/tmp/tests/empty
touch app/webroot/files/empty

```

.gitignoreを作る。


```bash
vim .gitignore
```


```
#/app/tmp/*
#/app/webroot/files
#/app/Config/core.php
/app/Config/database.php
/Plugin/*
/Vendor/*

/nbproject
.idea
/.project
/.buildpath
/.settings/
*.mo
*.un~
*.bak

.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
Icon?
ehthumbs.db
Thumbs.db
```


ここで一旦コミット。

```bash
git init
git add .
git commit -m "initialize repository"
```

コミットし終わったら、先ほどの.gitignoreのコメントアウトを外しておく。


今時はインデントは半角スペースだが、cake2はタブがstandardなので、vimの設定をしておく。


```vim
:set noexpandtab
```




で、設置したディレクトリでそのまま動かしたいので、直下にindex.phpを置く。

```
vim index.php
```

中身はこんな感じで。


```php:index.php
<?php
/**
 * Requests collector.
 *
 *  This file collects requests if:
 *	- no mod_rewrite is available or .htaccess files are not supported
 *  - requires App.baseUrl to be uncommented in app/Config/core.php
 *	- app/webroot is not set as a document root.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 *  Get CakePHP's root directory
 */
define('APP_DIR', 'app');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);

/**
 * This only needs to be changed if the "cake" directory is located
 * outside of the distributed structure.
 * Full path to the directory containing "cake". Do not add trailing directory separator
 */
if (!defined('CAKE_CORE_INCLUDE_PATH')) {
	define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');
}

require APP_DIR . DS . WEBROOT_DIR . DS . 'index.php';
```

一緒にhtaccessも置く。

```
vim .htaccess
```

.htaccessにはあとで使うかもしれないのでphpのupload_max_filesizeなんかも書いておく。

```:.htaccess
php_value memory_limit 1024M
php_value upload_max_filesize 20M
php_value post_max_size 20M

<IfModule mod_rewrite.c>
	RewriteEngine on
	# Uncomment if you have a .well-known directory in the root folder, e.g. for the Let's Encrypt challenge
	# https://tools.ietf.org/html/rfc5785
	#RewriteRule ^(\.well-known/.*)$ $1 [L]
	RewriteRule ^$ app/webroot/ [L]
	RewriteRule (.*) app/webroot/$1 [L]
</IfModule>
```

最後にwebrootの下のindex.phpを編集。

```bash
vim app/webroot/index.php
```

`define('CAKE_CORE_INCLUDE_PATH'...` の部分をコメントアウトして、`$vendorPath = ROOT . DS....`の部分を以下のように書き換える。

```php:app/webroot/index.php
/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 * Un-comment this line to specify a fixed path to CakePHP.
 * This should point at the directory containing `Cake`.
 *
 * For ease of development CakePHP uses PHP's include_path. If you
 * cannot modify your include_path set this value.
 *
 * Leaving this constant undefined will result in it being defined in Cake/bootstrap.php
 *
 * The following line differs from its sibling
 * /app/webroot/index.php
 */
//define('CAKE_CORE_INCLUDE_PATH',  ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'cakephp' . DS . 'cakephp' . DS . 'lib');

/**
 * This auto-detects CakePHP as a composer installed library.
 * You may remove this if you are not planning to use composer (not recommended, though).
 */
$vendorPath = ROOT . DS . 'Vendor' . DS . 'cakephp' . DS . 'cakephp' . DS . 'lib';
$dispatcher = 'Cake' . DS . 'Console' . DS . 'ShellDispatcher.php';
if (!defined('CAKE_CORE_INCLUDE_PATH') && file_exists($vendorPath . DS . $dispatcher)) {
	define('CAKE_CORE_INCLUDE_PATH', $vendorPath);
}

```


ここで一回ブラウザから開いてみるとよい。

test.phpも全く同じに編集する。


```bash
vim app/webroot/test.php
```

DebugKitを有効にするので、

```bash
vim app/Config/bootstrap.php
```

プラグインの部分を以下のように書く。


```php:app/Config/bootstrap.php
App::build(array(
    'Vendor' => array(ROOT . DS . 'Vendor' . DS),
    'Plugin' => array(ROOT . DS . 'Plugin' . DS)
));
CakePlugin::loadAll(); // Loads all plugins at once
```


さらにcomposerを使ってライブラリを読み込むのでbootstrap.phpに以下追加。

```php:app/Config/bootstrap.php
// Composer の autoload を読み込み
require ROOT . DS . 'Vendor' . DS . 'autoload.php';

// CakePHP のオートローダーをいったん削除し、Composer より先に評価されるように先頭に追加する
// http://goo.gl/kKVJO7 を参照
spl_autoload_unregister(array('App', 'load'));
spl_autoload_register(array('App', 'load'), true, true);
```

ついでにcore.phpにセッションの時間設定を入れておく。デフォルトの1440というのは24分ということなので、おそらく後からもっと長くしてくれ等々の要求がある。

```php:app/Config/core.php
	Configure::write('Session', array(
        'defaults' => 'php',
        'timeout' => 1440
	));
```

AppController.phpに追記。


```php:app/Controller/AppController.php
class AppController extends Controller {
    var $components = array( 'DebugKit.Toolbar');
}
```


あとはdatabase.php.defaultからdatabase.phpを作って、DB接続確認。

認証用のusersテーブルを仮で作っておく。

```sql
CREATE TABLE users (
	id INT AUTO_INCREMENT,
	username VARCHAR(255),
	password VARCHAR(255),
	name VARCHAR(255),
	email VARCHAR(255),
	photo VARCHAR(255),
	photo_dir VARCHAR(255),
	role CHAR(1) DEFAULT '1',
	status CHAR(1) DEFAULT '0',
	created DATETIME,
	modified DATETIME,
	primary key(id)
);
```

初期マイグレーションを作成しておく。


```bash
cd app
Console/cake Migrations.migration run all -p
Console/cake Migrations.migration generate -f
```


bakeを使って、usersのモデル、コントローラ、ビューを作っておく。


```bash
Console/cake bake

Welcome to CakePHP v2.9.6 Console
---------------------------------------------------------------
App : app
Path: /Users/mymac/cakepack2/app/
---------------------------------------------------------------
Interactive Bake Shell
---------------------------------------------------------------
[D]atabase Configuration
[M]odel
[V]iew
[C]ontroller
[P]roject
[F]ixture
[T]est case
[Q]uit
What would you like to Bake? (D/M/V/C/P/F/T/Q)
```


add.ctpについて、画像アップロードを使うので、type=fileを追加

```
<?php echo $this->Form->create('User', array('type' => 'file')); ?>
```

js用にwebrootをhiddenで追加。

```php
        echo $this->Form->hidden('webroot', array('value' => $this->webroot));
```



アップロードプラグイン対応でフォト用のフィールドを追加しておく。

```php:app/View/users/add.ctp
        echo $this->Form->input('User.photo', array('type' => 'file'));
        echo $this->Form->input('User.photo_dir', array('type' => 'hidden'));
```

モデルでアップロードプラグインの設定。
ファイル名をリネームして保存したいので、nameCallbackを設定する。


```php:app/Model/User.php
	public $actsAs = array(
		'Upload.Upload' => array(
			'photo' => array(
				'thumbnailMethod' => 'php',
				'deleteOnUpdate' => true,
				'deleteFolderOnDelete' => true,
				'thumbnailSizes' => array(
					'thumb' => '80x80'
				),
				'fields' => array(
					'dir' => 'photo_dir'
				),
				'nameCallback' => 'fileRename',
			)
		)
	);

	public function fileRename($field, $currentName, $data, $options) {
		$fileName = "";
		if (preg_match("/\.(pdf|jpg|jpeg|png)$/", $currentName, $regs)) {
			$ext = "." . $regs[1];
			$fileName = date("ymdHis") . $ext;
		}

		return $fileName;
	}

	
```

一応画像のリンクはこんな感じ。


```app/View/users/view.ctp
echo $this->Html->link('../files/user/photo/' . $user['User']['photo_dir'] . '/' . $user['User']['photo']);
```

あとpackage.jsonを作って、

```bash
npm init
vim package.json
```

codesnifferの実行コマンドを追加しておく。


```json:package.json
  "scripts": {
    "test": "echo \"Error: no test specified\" && exit 1",
    "cs": "Vendor/bin/phpcs --standard=CakePHP app/Controller/ app/Model/"
  },
```

```
run cs
```

で実行。

javascript用の環境も用意しておく。
まずnpmで必要なものをインストール。

```bash
npm install jquery --save-dev
npm install webpack --save-dev
```

コンフィグファイルを作っておいて、

```js:webpack.config.js
var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: {
      entry: './app/Script/entry.js',
      form: './app/Script/form.js'
  },
  output: {
    path: path.resolve(__dirname, 'app/webroot/js'),
    filename: '[name].js'
  },
  plugins: [
        new webpack.optimize.UglifyJsPlugin({sourceMap: true})
  ],
  devtool: '#source-map'
};
```

サンプルのスクリプトを設置。


```app/Script/entry.js
(function() {

  var $ = require("jquery");

  var hello = require('./hello.js');

  hello();

})();
```

```app/Script/hello.js
module.exports = function() {
    console.log('Hello Webpack!!');
};
```

ビルドコマンドをpackage.jsonに追記。


```json:package.json
  "scripts": {
    "build": "webpack"
  },
```

実行してみる。

```bash
npm run build
```

jsファイルが生成されればOK。


二カ所目で開発始める時は、git clone 後、

```bash
cd cakephp
sudo composer install
sudo chown -R mymac:staff ../cakephp/
chmod 0777 app/tmp/
chmod 0777 app/tmp/cache/
chmod 0777 app/tmp/cache/models/
chmod 0777 app/tmp/cache/persistent/
chmod 0777 app/tmp/cache/views/
chmod 0777 app/tmp/logs/
chmod 0777 app/tmp/sessions/
chmod 0777 app/tmp/tests/
chmod 0777 app/webroot/files/
```


