<?php
namespace __zf__;
class CController extends ZController{
	function _default(){
		$this->display('index');
	}

	function about(){
		$this->render('about');
	}

	function contact(){
		$this->render('contact');
	}

	function feedback(){
		print "Mesaage received :-)";
	}
}