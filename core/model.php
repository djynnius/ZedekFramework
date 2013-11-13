<?php
#class abstract class/super class

namespace __zf__;
use \Exception as Exception;
abstract class ZModel extends Zedek implements ZIModel{
	public $orm;
	public $uri;
	static private $scaffold_file_names = array(
		'read', 
		'view', 
		'update', 
	);

	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new URIMaper;
		$this->_init();	
	}

	function __call($method, $args){
		if(!method_exists($this, $method)) $this->_default();
	}

	static public function create($name, $bool=0, $table=null){
		$args = func_num_args();
		$args = count($args);
		switch($args){
			case 1:
				$code = file_get_contents(zroot."templates/model.tmp");				
				break;
			case 3:
				$code = file_get_contents(zroot."templates/scaffold_model.tmp");
				$code = str_replace("{{table}}", $table, $code);
				$code = str_replace("{{app_name}}", $name, $code);				
				break;				
			default:
				return false;
		}
		$modelFile = zroot."engines/{$name}/model.php";
		$appFolder = zroot."engines/{$name}";
		$viewFolder = zroot."engines/{$name}/view";
		try{
			if(!file_exists($appFolder)){
				mkdir($appFolder);
				mkdir($viewFolder);
				file_put_contents($modelFile, $code);
				chmod($appFolder, 0777);
				chmod($viewFolder, 0777);
				chmod($modelFile, 0777);
				self::insertScaffoldViewFiles($name, $args);
			} else {
				throw new ZException("{$name} App exists<br />\r\n");
			}
		} catch(ZException $e){
			return false;
			#print $e->getMessage();
		}		
	}
	
	static private function insertScaffoldViewFiles($name, $args=0){
		if($args != 3) return false;
		$enumerate = self::$scaffold_file_names;
		foreach($enumerate as $item){
			$code = file_get_contents(zroot."templates/scaffold_view_{$item}.tmp");
			$code = str_replace("{{table}}", $table, $code);
			$code = str_replace("{{app_name}}", $name, $code);		
			$file = zroot."engines/{$name}/view/{$item}.html";
			file_put_contents($file, $code);
			chmod($file, 0777);
		}
	}

	/**
		replaces construct for all classs
	*/
	public function _init(){}

	final protected function template($arg1=null, $arg2=null){
		require_once "view.php";
		return new ZView($arg1, $arg2);
	}

	#sets default to render index
	public function _default(){
		print $this::template("index")->render();
	} 

	public function _placeholders(){
		return array();
	}

	final public function _bounce($msg=false){
		$msg = $msg == false ? "" : "msg={$msg}";
		if(isset($_SERVER['HTTP_REFERER'])){
			$url = $_SERVER['HTTP_REFERER'];
			$url = explode("?msg=", $url);
			$url = $url[0];
			$url = strpos($url, "?") != false ? $url."&{$msg}" : rtrim($url, "/")."/?{$msg}";
			header("Location: {$url}");
		} else {
			header("Location: /"."{$msg}");
		}
	}
}

interface ZIModel{
	function _init();
	function _default();
	function _placeholders();
}