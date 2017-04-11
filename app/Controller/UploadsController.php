<?php
App::uses('AppController', 'Controller');

/**
 * Uploads Controller
 *
 */
class UploadsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

/**
 * add method
 *
 * @param string $dir ファイル保存先のディレクトリ名
 * @return string ファイル情報の配列をJSON形式で出力
 */
	public function add($dir = "bimage") {
		$this->autoRender = false;
		$myFile = $this->params['form']['file'];

		if ($myFile['error']) {
			$this->errorReturn($myFile, "このファイルはアップロードできません。");
		}

		$filetype = $myFile['type'];
		$name = $myFile['name'];
		$filesize = $myFile['size'];
		$tmpName = $myFile['tmp_name'];

		// ファイルタイプのチェック
		$this->checkFileType($myFile, $filetype);

		// 拡張子の取得
		$extention = $this->getExtention($myFile, $name);

		// ファイルサイズのチェック
		if ($filesize > 10000000) {// 10MB
			$this->errorReturn($myFile, "ファイルが大きすぎます。");
		}
		if (!$fd = fopen($tmpName, "r")) {
			$this->errorReturn($myFile, "ファイルが開けません。");
		}

		// アップロードファイルを読み込む
		$filedata = fread($fd, $filesize);
		fclose($fd);

		$myFile['img_name'] = time() . "_" . md5($filedata) . "." . $extention;
		$myFile['img_dir'] = $dir . "/" . date("Ym");
		$myFile['temp_img'] = WWW_ROOT . "files/tempimage/" . $myFile['img_name'];
		$myFile['temp_img_m'] = WWW_ROOT . "files/tempimage/m_" . $myFile['img_name'];
		$myFile['temp_img_s'] = WWW_ROOT . "files/tempimage/s_" . $myFile['img_name'];

		// テンポラリーに書き出し
		$this->saveTempImage($myFile, $filedata);

		echo json_encode($myFile);
	}

/**
 * checkFileType method
 * アップロードされたファイルの形式をチェックする。
 * jpg、png、gifであればtrueを返す
 *
 * @param array $myFile ファイル情報
 * @param string $type ファイル形式
 * @return bool
 */
	public function checkFileType($myFile, $type) {
		if (! preg_match('/jpg|jpeg|png|gif/', $type)) {
			$this->errorReturn($myFile, "ファイルの種類が違います。");
			exit;
		}

		return true;
	}

/**
 * getExtention method
 *
 * @param array $myFile ファイル情報
 * @param string $name オリジナルのファイル名
 * @return string $extention 拡張子
 */
	public function getExtention($myFile, $name) {
		$extention = "";
		if (preg_match("/jpeg|jpg|jpe|pjpeg/i", $name)) {
			$extention = "jpg";
		} elseif (preg_match("/gif/i", $name)) {
			$extention = "gif";
		} elseif (preg_match("/png/i", $name)) {
			$extention = "png";
		} else {
			$this->errorReturn($myFile, "ファイルの種類が違います。");
			exit;
		}

		return $extention;
	}

/**
 * fixImage method
 *
 * @param string $data ファイル情報
 * @return void
 */
	public function fixImage($data) {
		foreach ($data as $k => $v) {
			if (!file_exists(WWW_ROOT . "files/" . $v['img_dir'])) {
				mkdir(WWW_ROOT . "files/" . $v['img_dir']);
			}
			if ($v['temp_img'] && file_exists($v['temp_img'])) {
				rename($v['temp_img'], WWW_ROOT . "files/" . $v['img_dir'] . "/" . $v['img_name']);
			}
			if ($v['temp_img_m'] && file_exists($v['temp_img_m'])) {
				rename($v['temp_img_m'], WWW_ROOT . "files/" . $v['img_dir'] . "/m_" . $v['img_name']);
			}
			if ($v['temp_img_s'] && file_exists($v['temp_img_s'])) {
				rename($v['temp_img_s'], WWW_ROOT . "files/" . $v['img_dir'] . "/s_" . $v['img_name']);
			}
		}
	}

/**
 * rmImage method
 *
 * @param string $data ファイル情報
 * @return void
 */
	public function rmImage($data) {
		foreach ($data as $k => $v) {
			if (isset($v['org_name']) && $v['org_name']) {
				if ($v['img_name'] !== $v['org_name']) {
					if (file_exists(WWW_ROOT . "files/" . $v['org_dir'] . "/" . $v['org_name'])) {
						unlink(WWW_ROOT . "files/" . $v['org_dir'] . "/" . $v['org_name']);
					}
					if (file_exists(WWW_ROOT . "files/" . $v['org_dir'] . "/m_" . $v['org_name'])) {
						unlink(WWW_ROOT . "files/" . $v['org_dir'] . "/m_" . $v['org_name']);
					}
					if (file_exists(WWW_ROOT . "files/" . $v['org_dir'] . "/s_" . $v['org_name'])) {
						unlink(WWW_ROOT . "files/" . $v['org_dir'] . "/s_" . $v['org_name']);
					}
				}
			}
		}
	}

/**
 * saveTempImage method
 *
 * @param array $myFile ファイル情報
 * @param string $filedata ファイルデータ
 * @return void
 */
	public function saveTempImage($myFile, $filedata) {

        $image = \WideImage\WideImage::load($filedata);

        // オリジナル
		$image->saveToFile($myFile['temp_img']);

		// サムネイル中
		$resized = $image->resize(190, 135, 'inside');
		$resized->saveToFile($myFile['temp_img_m']);

		// サムネイル小
		$resized = $image->resize(120, 80, 'inside');
		$resized->saveToFile($myFile['temp_img_s']);
	}

/**
 * saveImage method
 * どこで使ってる？
 *
 * @param array $myFile ファイル情報
 * @param string $filedata ファイルデータ
 * @return void
 */
	public function saveImage($myFile, $filedata) {
		if (!file_exists(WWW_ROOT . "files/" . $myFile['img_dir'])) {
			mkdir(WWW_ROOT . "files/" . $myFile['img_dir']);
		}

		$imgFileFullPath = WWW_ROOT . "files/" . $myFile['img_dir'] . "/" . $myFile['img_name'];

		$res = fopen($imgFileFullPath, 'w');
		fwrite($res, $filedata);
		fclose($res);

		$image = \WideImage\WideImage::load($imgFileFullPath);
		$resized = $image->resize(190, 135, 'inside');
		$resized->saveToFile(WWW_ROOT . "files/" . $myFile['img_dir'] . "/s_" . $myFile['img_name']);
	}

/**
 * errorReturn method
 *
 * @param array $myFile ファイル情報
 * @param string $emes error message
 * @return string エラーメッセージをJSON形式で出力
 */
	public function errorReturn($myFile, $emes) {
		$myFile['error'] = $emes;
		echo json_encode($myFile);
		exit;
	}
}

