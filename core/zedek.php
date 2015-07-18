<?php
/**
* @package Zedek Framework
* @version 4
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
use \Exception as Exception;

abstract class Zedek{

	public $configFile;

	/**
	* @param string $controller
	*/
	static function import($controller = "default"){
		$s = new ZSites;
		$engine = $s->getEngine($_SERVER["SERVER_NAME"]);
		try{
			if(file_exists($engine."/{$controller}/controller.php")){
				require_once $engine."{$controller}/controller.php";
			} else {
				throw new Exception();
			}
		} catch(Exception $e){
			return false;
		}
	}

	/**
	* @param string $controller 
	* @param string $method 
	* @param string $arguments 
	*/
	function redirect($controller=false, $method=false, $arguments=false){
		$args = func_num_args();
		$dir = zsub;
		$dir = ltrim(zsub, "/");
		switch($args){
			case 1:
				if($controller == '-1' || strtolower($controller) == 'back'){
					if(isset($_SERVER['HTTP_REFERER'])) {
						header("Location: " . $_SERVER['HTTP_REFERER']);	
					} else {
						header("Location: /" . $dir);		
					}
				} elseif($controller == '0' || strtolower($controller) == 'self'){
					header("Location: " . $_SERVER['REQUEST_URI']);
				} else {
					header("Location: /". $dir .$controller);	
				}
				break;
			case 2:
				header("Location: /". $dir .$controller."/".$method);
				break;
			case 3:
				header("Location: /". $dir .$controller."/". $method."/".$arguments);
				break;
			default:
				header("Location: /" . $dir);		
		}
		
	}
}

/**
* @subpackage Z zedek core implemented
*/
class Z extends Zedek{
	
	/**
	* internal classes are pulled in to make a few things simpler
	*/
	static function importInternals(){
		require_once "internals.php";
		$internals = scandir(zroot."internals/");
		foreach($internals as $internal){
			$file = zroot."internals/".$internal;
			if(!is_dir($file) && strpos($file, ".php") != false){
				require_once $file;
			}
		}
	}

	/**
	* pulls in all models makes them avalilable globally
	*/
	static function importModels($type = false){
		require_once "model.php";
		$models = scandir(zroot."models/");
		foreach($models as $model){
			$file = zroot."models/".$model;
			if(!is_dir($file) && strpos($file, ".php") != false){
				require_once $file;
			}
		}
	}

	/**
	* Simpletest implementation for unit testing
	*/
	static function webTest(){
		$config = new ZConfig;
		if($config->get("webUnitTest") == "Off"){
			require_once zroot."libs/php/nowebtest.php";
		} else {
			require_once zroot."libs/php/simpletest/autorun.php";	
		}
		
	}
}


/**
* @subpackage ZException zedek exception class
*/
class ZException extends Exception{}