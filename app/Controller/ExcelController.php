<?php
App::uses('AppController', 'Controller');

/**
 * Excel Controller
 *
 */
class ExcelController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

/**
 * index method
 * サンプルメソッド
 * excel2007にするにはphp−zipの有効化が必要
 *
 * @return void
 */
	public function index() {
		$this->autoRender = false;
		$excel = new PHPExcel();
		// Create a new worksheet called “My Data
		$myWorkSheet = new PHPExcel_Worksheet($excel, 'My Data');
		// // Attach the “My Data” worksheet as the fist worksheet in the PHPExcel object
		$excel->addSheet($myWorkSheet, 0);
		$excel->setActiveSheetIndex(0);
		$sheet = $excel->getActiveSheet();//有効になっているシートを取得

		$sheet->setCellValue('A1', 'こんにちは');
		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$writer->save(WWW_ROOT . 'files/output.xls');
	}

/**
 * sample5 method
 *
 * 読み込みサンプル
 *
 * @return void
 */
	public function sample5() {
		$this->autoRender = false;
		//ひな形エクセルファイルの読込
		$file = WWW_ROOT . 'files/sample.xls';
		$obj = PHPExcel_IOFactory::createReader('Excel5');
		$book = $obj->load($file);

		//シートを設定する
		$book->setActiveSheetIndex(0);//一番最初のシートを選択
		$sheet = $book->getActiveSheet();//選択シートにアクセスを開始
		$sheet->setTitle('sheet1'); //シート名を設定する

		//セルにデータをセット
		$sheet->setCellValue('D5', 'テスト');//D5セルに「テスト」と書き込む
		$sheet->setCellValueByColumnAndRow(1, 2, 'hoge');

		//Excel2007形式で出力する準備
		//「vnd.ms-excel」だとブラウザによってはそのまま開いたりするのでこの方が良いかと
		header('Content-Type: application/octet-stream');
		//ダウンロードするファイル名を設定
		header('Content-Disposition: attachment;filename="download_test5.xls"');

		$writer = PHPExcel_IOFactory::createWriter($book, "Excel5");//EXCEL2007形式
		$writer->save('php://output');//出力開始
	}

/**
 * sample method
 *
 * 読み込みサンプルExcel2007
 *
 * @return void
 */
	public function sample() {
		$this->autoRender = false;
		//ひな形エクセルファイルの読込
		$file = WWW_ROOT . 'files/sample.xlsx';
		$obj = PHPExcel_IOFactory::createReader('Excel2007');
		$book = $obj->load($file);

		//シートを設定する
		$book->setActiveSheetIndex(0);//一番最初のシートを選択
		$sheet = $book->getActiveSheet();//選択シートにアクセスを開始
		$sheet->setTitle('sheet1'); //シート名を設定する

		//セルにデータをセット
		$sheet->setCellValue('D5', 'テスト');//D5セルに「テスト」と書き込む

		//Excel2007形式で出力する準備
		//「vnd.ms-excel」だとブラウザによってはそのまま開いたりするのでこの方が良いかと
		header('Content-Type: application/octet-stream');
		//ダウンロードするファイル名を設定
		header('Content-Disposition: attachment;filename="download_test.xlsx"');

		$writer = PHPExcel_IOFactory::createWriter($book, "Excel2007");//EXCEL2007形式
		$writer->save('php://output');//出力開始
	}

/**
 * copyRows method
 * 行コピー 
 *
 * @param string $srcRow 複製元行番号
 * @param string $dstRow 複製先行番号
 * @param string $height 複製行数
 * @param string $width 複製カラム数
 * @param string $sheet シート
 * @return void
 */
	public function copyRows($srcRow, $dstRow, $height, $width, $sheet) {
		for ($row = 0; $row < $height; $row++) {
			// セルの書式と値の複製
			for ($col = 0; $col < $width; $col++) {
				$cell = $sheet->getCellByColumnAndRow($col, $srcRow + $row);
				$style = $sheet->getStyleByColumnAndRow($col, $srcRow + $row);

				$dstCell = PHPExcel_Cell::stringFromColumnIndex($col) . (string)($dstRow + $row);
				$sheet->setCellValue($dstCell, $cell->getValue());
				$sheet->duplicateStyle($style, $dstCell);
			}

			// 行の高さ複製。
			$h = $sheet->getRowDimension($srcRow + $row)->getRowHeight();
			$sheet->getRowDimension($dstRow + $row)->setRowHeight($h);
		}

		// セル結合の複製
		// - $mergeCell="AB12:AC15" 複製範囲の物だけ行を加算して復元。
		// - $merge="AB16:AC19"
		foreach ($sheet->getMergeCells() as $mergeCell) {
			$mc = explode(":", $mergeCell);
			$colS = preg_replace("/[0-9]*/", "", $mc[0]);
			$colE = preg_replace("/[0-9]*/", "", $mc[1]);
			$rowS = ((int)preg_replace("/[A-Z]*/", "", $mc[0])) - $srcRow;
			$rowE = ((int)preg_replace("/[A-Z]*/", "", $mc[1])) - $srcRow;

			// 複製先の行範囲なら。
			if (0 <= $rowS && $rowS < $height) {
				$merge = $colS . (string)($dstRow + $rowS) . ":" . $colE . (string)($dstRow + $rowE);
				$sheet->mergeCells($merge);
			}
		}
	}
}
