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
	function index(){
		self::display("index@ztheme");
	}

	function about(){
		self::render('about');
	}

	function contact(){
		self::render('contact');
	}
}