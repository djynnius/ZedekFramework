<?php

namespace __zf__;

class _Image {

	function extension($file){
		$ext = explode(".", $file);
		return end($ext);
	}

	function resize($source, $target, $mime = "jpeg", $newWidth = 200, $newHeight = 160){
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
	
		$canvas = imagecreatetruecolor($newWidth, $newHeight);
		
		switch($mime){
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
		
		imagecopyresampled($canvas, $nuImg, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
		imagejpeg($canvas, $target, 100);	
	}
		
	function batchResize($sourceFolder, $destinationFolder){
		self::progressBar($sourceFolder, $destinationFolder);
		if(is_dir($sourceFolder) and is_dir($destinationFolder)){
			$handleS = opendir($sourceFolder);
			while($file = readdir($handleS)){
				if(!is_dir($file)){
					$fileStat = @getimagesize($sourceFolder."/".$file);
					if($fileStat['mime'] == "image/jpeg"){
						$transition = new Image();
						$transition->resize($sourceFolder."/".$file, $destinationFolder."/".$file);
					}
				}
			}
			closedir($handleS);
		} else {
			echo "Either your source or destination is not a valid folder.";
		}
	}
	
	function progress($sourceDir, $destinationDir){
		$handleS = opendir($sourceDir);
		while($file = readdir($handleS)){
			$s[] = $file;
		}
		$sourceCount = count($s);
		closedir($handleS);
		
		$handleD = opendir($destinationDir);
		while($file = readdir($handleD)){
			$d[] = $file;
		}
		$destinationCount = count($d);
		closedir($handleD);
		
		echo "
			<div id='container' style='padding:4px; margin: 4px; border: solid 1px #ddd;'><div class='inner' style='height: 40px; border: solid 1px #ccc; padding: 4px; margin: 2px; background-color: green; color: white; width: ".round((($destinationCount/$sourceCount)*100))."%'>".round((($destinationCount/$sourceCount)*100))."%"."</div></div>";
	}
	
	function progressBar($sourceDir, $destinationDir){
		$handleS = opendir($sourceDir);
		while($file = readdir($handleS)){
			$s[] = $file;
		}
		$sourceCount = count($s);
		closedir($handleS);
		
		$handleD = opendir($destinationDir);
		while($file = readdir($handleD)){
			$d[] = $file;
		}
		$destinationCount = count($d);
		closedir($handleD);
		
		echo "
			<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
			<html xmlns=\"http://www.w3.org/1999/xhtml\">
			<head>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
			<title>PHP Image resizer</title>
			<script type='application/javascript' src='jQuery.js'></script>
			<script type='application/javascript'>
				jQuery(document).ready(function($){
					setInterval(function(){
						$('div.container').load('progress.php?source=".(str_replace("/", "%2F", $_GET['source']))."&destination=".(str_replace("/", "%2F", $_GET['destination']))."');
					}, 100)
				});
			</script>
			</head>
			<body>
			
			<div id='container'><div class='inner'></div></div>
			</body>
			</html>
		";
	}
}