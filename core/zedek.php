<?php
#the zedek super parent
abstract class Zedek{
	static function import($module = "default"){

		try{
			if(file_exists(zroot."engines/{$module}/controler.php")){
				require_once zroot."engines/{$module}/controler.php";
			} else {
				throw new Exception();
			}
		} catch(Exception $e){
			return false;
		}
	}

	function isUser(){
		return isset($_SESSION['__z__']['user']['role']) && !empty($_SESSION['__z__']['user']['role']) ? true : false;
	}

	function isAdmin(){
		return self::is_user() && empty($_SESSION['__z__']['user']['role']) == "1" ? true : false;
	}
}

class Z extends Zedek{
	static function importLibs($type = false){
		require_once "lib.php";
		$libs = scandir(zroot."libs/");
		foreach($libs as $lib){
			$file = zroot."libs/".$lib;
			if(!is_dir($file)){
				require_once $file;
			}
		}
	}	
}

?>