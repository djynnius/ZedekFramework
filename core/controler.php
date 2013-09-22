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

	public function _bounce(){
		if(isset($_SERVER['HTTP_REFERER'])){
			header("Location: ".$_SERVER['HTTP_REFERER']);
		} else {
			header("Location: /");
		}
	}
}

interface ZIControler{
	function _init();
	function _default();
	function _placeholders();
}

?>