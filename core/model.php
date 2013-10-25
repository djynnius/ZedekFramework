<?php
#class abstract class/super class

namespace __zf__;
use \Exception as Exception;
abstract class ZModel extends Zedek implements ZIModel{
	public $orm;
	public $uri;

	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new URIMaper;
		$this->_init();	
	}

	function __call($method, $args){
		if(!method_exists($this, $method)) $this->_default();
	}

	static function create($name){
		$code = file_get_contents(zroot."templates/model.tmp");
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
			} else {
				throw new ZException("{$name} App exists<br />\r\n");
			}
		} catch(ZException $e){
			echo $e->getMessage();
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
		echo $this::template("index")->render();
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

?>