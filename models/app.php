<?php
/**
* @package Zedek Framework
* @version 5
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
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
