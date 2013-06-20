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

	function is_user(){
		return isset($_SESSION['zedek']['user']['role']) && !empty($_SESSION['zedek']['user']['role']) ? true : false;
	}

	function is_admin(){
		return self::is_user() && empty($_SESSION['zedek']['user']['role']) == "1" ? true : false;
	}
}

class Z extends Zedek{
	static function import($module = false){
		try{
			if(file_exists(zroot."core/{$module}.php")){
				require_once zroot."core/{$module}.php";
			} else {
				throw new Exception();
			}
		} catch(Exception $e){
			return false;
		}
	}	
}

?>