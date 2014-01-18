<?php
#the zedek super parent
namespace __zf__;
use \Exception as Exception;
use \PHPUnit_Framework_TestCase as PHPUnit;

abstract class Zedek{

	public $configFile;

	static function import($module = "default"){
		try{
			if(file_exists(zroot."engines/{$module}/controller.php")){
				require_once zroot."engines/{$module}/controller.php";
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
		return 
			isset($_SESSION['__z__']['user']['roles']) && 
			count($_SESSION['__z__']['user']['roles']) != 0 ? 
				true : 
				false;
	}

	function isAdmin(){
		return 
			$this->isUser() && 
			in_array('1', $_SESSION['__z__']['user']['roles']) ? 
				true : 
				false;
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
			//print $e->getMessage();
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

	function redirect($uri=Zedek::controller, $method=Zedek::method, $arguments=Zedek::arguments){
		$args = func_num_args();
		switch($args){
			case 1:
				header("Location: ".$uri);
				break;
			case 2:
				header("Location: ".$uri."/".$method);
				break;
			case 3:
				header("Location: ".$uri."/". $method."/".$arguments);
				break;
			default:
				header("Location: /");		
		}
		
	}
}

class Z extends Zedek{
	static function importModels($type = false){
		require_once "model.php";
		$models = scandir(zroot."models/");
		foreach($models as $model){
			$file = zroot."models/".$model;
			if(!is_dir($file)){
				require_once $file;
			}
		}
	}

	static function message(){
		return isset($_GET['msg']) ? $_GET['msg']: "";
	}

	static function createTest($name){
		$file = zroot."test/{$name}.php";
		try{
			if(!file_exists($file = zroot."test/{$name}.php")){
				$code = file_get_contents(zroot."templates/test.tmp");
				file_put_contents($file, $code);
				chmod($file, 0777);				
			} else {
				throw new ZException("{$name} Test exists<br />\r\n");
			}
		} catch(ZException $e){
			print $e->getMessage();
		}

	}

	static function webTest(){
		$config = new ZConfig;
		if($config->get("webUnitTest") == "Off"){
			require_once zroot."libs/php/nowebtest.php";
		} else {
			require_once zroot."libs/php/simpletest/autorun.php";	
		}
		
	}
}

class ZException extends Exception{}