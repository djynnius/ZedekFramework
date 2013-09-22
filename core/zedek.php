<?php
#the zedek super parent
namespace __zf__;
use \Exception as Exception;

abstract class Zedek{

	public $configFile;

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

	function currentIndex(){
		$uri = new URIMAper;
		$id = explode("/", $uri->arguments);
		$id = end($id);
		return $id;
	}

	function isUser(){
		return isset($_SESSION['__z__']['user']['role']) && !empty($_SESSION['__z__']['user']['role']) ? true : false;
	}

	function isAdmin(){
		return $this->isUser() && $_SESSION['__z__']['user']['role'] == "1" ? true : false;
	}

	protected function jsonConfig(){
		try{
			if(!empty($this->configFile)){
				$config = file_get_contents($this->configFile);
			} else {
				$config = false;
			}
		} catch(Exception $e){
			//exception
		}		
		return json_decode($config);
	}

	public function getVar($key){
		$config = $this->jsonConfig($this->configFile);		
		try{
			if(isset($config->{$key})){
				return $config->{$key};
			} else {
				throw new Exception("No config value for {$key}");
				return false;
			}
		} catch(Exception $e){
			//echo $e->getMessage();
		}
	}

	public function setVar($key, $value){
		$config = $this->jsonConfig($this->configFile);
		$config->{$key} = $value;
		$this->castVar($config);
	}

	public function removeVar($key){
		$config = $this->jsonConfig($this->configFile);
		try{
			if(isset($config->{$key})){
				unset($config->{$key});
				$this->castVar($config);
			} else {
				throw new Exception("The configuratiion does not exist.");
			}
		} catch(Exception $e){
			return $e->getMessage();
		}
	}

	protected function castVar($config){
		$config = json_encode($config);
		file_put_contents($this->configFile, $config);		
	}

	function postedForm(){
		return isset($_POST['submit']) ? true : false;
	}

	function forUsers(){
		if(!$this->isUser()){header("Location: ". $_SERVER['HTTP_REFERER']);}
	}

	function forAdmin(){
		if(!$this->isAdmin()){header("Location: ". $_SERVER['HTTP_REFERER']);}
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

class ZFException extends Exception{}

?>