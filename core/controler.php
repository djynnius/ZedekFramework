<?php
#controler abstract class/super class
abstract class ZControler extends Zedek{
	function _default(){
	}

	function template($arg1=null, $arg2=null){
		Z::import("view");
		return new ZView($arg1, $arg2);
	}
}

interface ZIControler{}

?>