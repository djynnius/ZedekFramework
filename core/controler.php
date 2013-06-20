<?php
#controler abstract class/super class
abstract class ZControler extends Zedek implements ZIControler{

	function __construct(){
		$this->_init();	
		$this->importModel();
	}
	
	function _init(){} //replaces construct for all controlers

	function template($arg1=null, $arg2=null){
		Z::import("view");
		return new ZView($arg1, $arg2);
	}

	function importModel($controler = false){
		$uri = new URIMaper();
		$controler = $controler == false ? $uri->controler : $controler;

		if(file_exists(zroot."engines/{$controler}/model.php")){
			require_once zroot."engines/{$controler}/model.php";
		} elseif(file_exists(zroot."/engines/default/model.php")){
			require_once zroot."/engines/default/model.php";	
		} else {
			return false;
		}
	}

	function _default(){self::template("index")->render();} //sets default to render index

}

interface ZIControler{
	function _init();
	function _default();
}



?>