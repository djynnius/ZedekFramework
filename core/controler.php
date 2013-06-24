<?php
#controler abstract class/super class
abstract class ZControler extends Zedek implements ZIControler{

	function __construct(){
		$this->_init();	
		$this->importApp();
	}
	
	function _init(){} //replaces construct for all controlers

	function template($arg1=null, $arg2=null){
		require_once "view.php";
		return new ZView($arg1, $arg2);
	}

	function importApp($controler = false){
		$uri = new URIMaper();
		
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
	function _default(){
		echo $this::template("index")->render();
	} 

	function denyGuest(){
		if(isset($_SERVER['HTTP_REFERER'])){
			header("Location: ".$_SERVER['HTTP_REFERER']);
		} else {
			header("Location: /");
		}
	}

	function logicToView(){
		header("Location: ".$_SERVER['HTTP_REFERER']);
	}

}

interface ZIControler{
	function _init();
	function _default();
}

?>