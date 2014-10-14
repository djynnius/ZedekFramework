<?php

namespace __zf__;

class xls{

	function __construct(){
		require_once zroot."libs/php/PHPExcel.php";
	}

	public static function write($array, $name='file.xls'){
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename='.$name);
		header('Cache-Control: max-age=0'); 
		require_once zroot."libs/php/PHPExcel.php";
		$xls = new \PHPExcel;
		$xls->getActiveSheet()->fromArray($array, null, 'A1');
		$writer = \PHPExcel_IOFactory::createWriter($xls, "Excel5");  
		$writer->save('php://output');		
	}

	public static function read($file){
		require_once zroot."libs/php/PHPExcel.php";
		try {
		    $inputFileType = \PHPExcel_IOFactory::identify($file);
		    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
		    $xls = $objReader->load($file);
		} catch(Exception $e) {
		    die('Error loading file "'.pathinfo($fiile,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		$sheet = $xls->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 
		$highestColumn = $sheet->getHighestColumn();		

		for ($row = 1; $row <= $highestRow; $row++){ 
		    //  Read a row of data into an array
		    $array = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
		                                    NULL,
		                                    TRUE,
		                                    FALSE);
		    $puts[] = $array;
		    //  Insert row data array into your database of choice here
		}
		return $puts;
	}
}