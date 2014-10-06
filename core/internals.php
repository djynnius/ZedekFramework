<?php
/**
* @package Zedek Framework
* @subpackage ZInternals zedek internal plugins class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

abstract class ZInternal extends Zedek{
	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new ZURI;
	}
}