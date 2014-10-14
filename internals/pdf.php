<?php

namespace __zf__;
use mPDF as zpdf;
require_once zroot."libs/php/mpdf/mpdf.php";

class pdf{

	private function pdfo(){
		$mpdf = new zpdf();
		$mpdf->SetTitle("Some");
		$mpdf->SetAuthor("PG Admin");
		$mpdf->SetDisplayMode('fullpage');
		return $mpdf;
	}

	public static function open($html,  $styles=array()){
		$pdf = self::pdfo();
		$i = 1;
		foreach($styles as $style){
			$pdf->WriteHTML($style, $i);
			$i++;
		}
		$pdf->WriteHTML($html, $i);
		$pdf->Output();
	}

	public static function write($html, $styles=array()){
		$pdf = self::pdfo();
		$i = 1;
		foreach($styles as $style){
			$pdf->WriteHTML($style, $i);
			$i++;
		}
		$pdf->WriteHTML($html, $i);
		$pdf->Output();
	}

	public static function save($html, $destination, $styles=array()){
		$pdf = self::pdfo();
		$i = 1;
		foreach($styles as $style){
			$pdf->WriteHTML($style, $i);
			$i++;
		}
		$pdf->WriteHTML($html, $i);
		$pdf->Output($destination, "F");
	}

}