<?php
/**
* @package Zedek Framework
* @version 6
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

abstract class ZInternal extends Zedek{
	public $orm;
	public $uri;

	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new ZURI;
	}
}