<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * ActAs Upload File
 *
 * @var array
 */
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
				'nameCallback' => 'renameFile',
			)
		)
	);

/**
 * renameFile method
 *
 * @param string $field フィールド
 * @param string $currentName 元のファイル名
 * @param array $data ファイル情報
 * @param array $options オプション
 * @return string $fileName
 */
	public function renameFile($field, $currentName, $data, $options) {
		$fileName = "";
		if (preg_match("/\.(pdf|jpg|jpeg|png)$/", $currentName, $regs)) {
			$ext = "." . $regs[1];
			$fileName = date("ymdHis") . $ext;
		}

		return $fileName;
	}
}
