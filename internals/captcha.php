<?php
/**
* @package Zedek Framework
* @version 5
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class _Captcha extends Zedek{
		
	private static function dictionary(){

		$chr = array(
			$rndcap = rand(65, 90), 
			$rndlow = rand(97, 122), 
			$rndnum = rand(49, 57),
			$rndcap = rand(65, 90), 
			$rndlow = rand(97, 122), 
			$rndnum = rand(49, 57),
			$rndcap = rand(65, 90), 
			$rndlow = rand(97, 122), 
			$rndnum = rand(49, 57),
			$rndcap = rand(65, 90), 
			$rndlow = rand(97, 122), 
			$rndnum = rand(49, 57)
		);

		$text = "";
		for($i=0; $i<5; $i++){
			$text .= chr($chr[rand(0, 11)]);
		}
		
		$dictionary = array(
			$text
		);
		return $dictionary;
	}
	
	public static function implement($dictionary=false, $font=false, $textColor=false){
		$dictionary = $dictionary == false ? self::dictionary() : $dictionary;
		$count = sizeof($dictionary);
		$i = rand(0, $count-1);
		$value = $dictionary[$i];
		
		header("Content-Type: image/png");
		
		$bg = imagecreatetruecolor(500,120);
		
		$green = imagecolorallocate($bg, 0, 120, 0);
		$white = imagecolorallocate($bg, 255, 255, 255);
		$black = imagecolorallocate($bg, 0, 0, 0);
		$blue = imagecolorallocate($bg, 0, 186, 255);

		switch($textColor){
			case "green": $textColor = $green; break;
			case "white": $textColor = $white; break;
			case "black": $textColor = $black; break;
			case "blue": $textColor = $blue; break;
			default: $textColor = $blue;
		}

		$font = zroot."libs/fonts/Montez-Regular.ttf";
		$text = gettype($dictionary) == 'array' ? $value : $dictionary;
		
		imagefilledrectangle($bg, 0, 0, 500, 120, $white);
		imagettftext($bg, 58, 0, 20, 87, $textColor, $font, $text);
		
		
		imagepng($bg);
		imagedestroy($bg);
		
		$_SESSION['captcha'] = $value;
	}
	
}

?>
