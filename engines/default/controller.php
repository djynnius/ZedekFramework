<?php
/**
* @package Zedek Framework
* @subpackage ZConfig zedek configuration class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
class CController extends ZController{
	function _default(){
		$this->display("index");
	}

	function about(){
		$this->render('about');
	}

	function contact(){
		$this->render('contact');
	}

}