<?php
#controler abstract class/super class

namespace __zf__;

abstract class ZControler extends Zedek implements ZIControler{

	function __construct(){
		$this->_init();	
		$this->_importApp();
	}

	function __call($method, $args){
		if(!method_exists($this, $method)) $this->_default();
	}
	
	/**
		replaces construct for all controlers
	*/
	public function _init(){}

	protected function template($arg1=null, $arg2=null){
		require_once "view.php";
		return new ZView($arg1, $arg2);
	}

	public function _importApp($controler = false){
		$uri = new URIMaper;		
		if($controler == false && strlen($uri->controler) > 0){
			$controler = $uri->controler;
		} elseif(strlen($uri->controler) > 0){
			break;
		} else {
			$controler = "default";
		}
		if(file_exists(zroot."engines/{$controler}/model.php")){
			require_once zroot."engines/{$controler}/model.php";
		} elseif(file_exists(zroot."/engines/default/model.php")){
			require_once zroot."/engines/default/model.php";	
		} else {
			return false;
		}
	}

	#sets default to render index
	public function _default(){
		echo $this::template("index")->render();
	} 

	public function _placeholders(){
		return array();
	}

	public function _bounce($msg=false){
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

interface ZIControler{
	function _init();
	function _default();
	function _placeholders();
}

?>