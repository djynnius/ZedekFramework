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

class _Image {

	static public function extension($file){
		$ext = explode(".", $file);
		$ext = end($ext);
		$ext = strtolower($ext);
		return $ext;
	}

	static public function ext($file){
		return self::extension($file);
	}

	static public function crop($source, $target, $width=256, $height=256, $ext="auto"){
		list($w, $h) = getimagesize($source);
		$src_w = ($w/2)-($width/2);
		$src_h = ($h/2)-($height/2);
		$ext = strtolower($ext);

		$canvas = imagecreatetruecolor($width, $height);

		switch($ext){
			case "jpeg":
				$nuImg = imagecreatefromjpeg($source);
				break;
			case "png":
				$nuImg = imagecreatefrompng($source);
				break;
			case "gif":
				$nuImg = imagecreatefromgif($source);
				break;
			default:
				$nuImg = imagecreatefromjpeg($source);
		}
		
		imagecopyresampled($canvas, $nuImg, 0, 0, $src_w, $src_h, $width, $height, $width, $height);
		
		switch($ext){
			case "gif":
				imagegif($canvas, $target, 100);
				break;
			case "png":
				imagepng($canvas, $target, 100);
				break;
			default:
				imagejpeg($canvas, $target, 100);		
		}
	}

	static public function resize($source, $target, $newWidth = 512, $newHeight = 512, $mime = "auto"){
		$mime = strtolower($mime);
		$sourceStats = getimagesize($source);
		$sourceRawDimensions = $sourceStats[3];
		$sourceDimensions = explode("\"", $sourceRawDimensions);
		$sourceWidth = $sourceDimensions[1];
		$sourceHeight = $sourceDimensions[3];
		$aspectRatio = ($sourceWidth/$sourceHeight);
	
		if(($newWidth/$newHeight) > $aspectRatio){
			$newHeight = ($newWidth/$aspectRatio);
		} elseif(($newWidth/$newHeight) < $aspectRatio) {
			$newWidth = ($newHeight * $aspectRatio);
		}
	
		$canvas = imagecreatetruecolor($newWidth, $newHeight);
		
		switch($mime){
 			case "png":
				$nuImg = imagecreatefrompng($source);
				break;
			case "gif":
				$nuImg = imagecreatefromgif($source);
				break;
			default:
				$nuImg = imagecreatefromjpeg($source);
		}
		
		imagecopyresampled($canvas, $nuImg, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
		switch($mime){
			case "gif":
				imagegif($canvas, $target, 100);
				break;
			case "png":
				imagepng($canvas, $target, 100);
				break;
			default:
				imagejpeg($canvas, $target, 100);		
		}			
	}
		
}