<?php

namespace zedekframework;

class Image{

	function resize($source, $target, $newWidth = 200, $newHeight = 160){
		$ext = $this->getExtension($target);
		$acceptedMIME = array('jpg', 'jpeg', 'gif', 'png');
		if(in_array($ext, $acceptedMIME)){
			$newDimension = $this->preserveAspectRatio($source, $newWidth, $newHeight);
			$newWidth = $newDimension['newWidth'];		
			$newHeight = $newDimension['newHeight'];
			$sourceWidth = $newDimension['sourceWidth'];
			$sourceHeight = $newDimension['sourceHeight'];
			$canvas = imagecreatetruecolor($newWidth, $newHeight);			
			switch($ext){
				case strtolower($ext) == "gif":
					$nuImg = imagecreatefromgif($source);
					imagecopyresampled($canvas, $nuImg, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
					imagegif($canvas, $target, 80);			
					break;
				case strtolower($ext) == "png":
					$nuImg = imagecreatefrompng($source);
					imagecopyresampled($canvas, $nuImg, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
					//imagesavealpha($source, true);
					imagepng($canvas, $target);
					break;
				default:
					$nuImg = imagecreatefromjpeg($source);
					imagecopyresampled($canvas, $nuImg, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
					imagejpeg($canvas, $target, 80);								
			}
			chmod($target, 0777);
		} else {
			return false;
		}
	}

	function getExtension($target){
		$ext = pathinfo($target);
		$ext = $ext['extension'];
		return strtolower($ext);		
	}

	function preserveAspectRatio($source, $newWidth, $newHeight){
		$sourceStats = getimagesize($source);
		$sourceRawDimensions = $sourceStats[3];
		$sourceDimensions = explode("\"", $sourceRawDimensions);
		$sourceWidth = $sourceDimensions[1];
		$sourceHeight = $sourceDimensions[3];
		$aspectRatio = ($sourceWidth/$sourceHeight);
		if(($newWidth/$newHeight) > $aspectRatio){
			$newWidth = ($newHeight * $aspectRatio);
		} elseif(($newWidth/$newHeight) < $aspectRatio) {
			$newHeight = ($newWidth/$aspectRatio);
		}
		return array(
			'newWidth'=>$newWidth, 
			'newHeight'=>$newHeight, 
			'sourceWidth'=>$sourceWidth, 
			'sourceHeight'=>$sourceHeight, 
		);
	}

	function batchResize($sourceFolder, $destinationFolder, $newWidth = 200, $newHeight = 160){
		if(!is_dir($destinationFolder)){
			mkdir($destinationFolder);
			chmod($destinationFolder, 0777);
		}
		if(is_dir($sourceFolder) and is_dir($destinationFolder)){
			$handleS = opendir($sourceFolder);
			while($file = readdir($handleS)){
				if(!is_dir($file)){
					$fileStat = @getimagesize($sourceFolder."/".$file);
					self::resize($sourceFolder."/".$file, $destinationFolder."/".$file, $newWidth, $newHeight);
				}
			}
			closedir($handleS);
		} else {
			print "Either your source or destination is not a valid folder.";
		}
	}
}