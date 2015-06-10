<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 3.0
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class App extends ZModel{

	function __construct(){
		parent::__construct();
	}

	function tmp(){
		$tmp = array();
		return $tmp;
	}

}
