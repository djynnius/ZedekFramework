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