<?php
/**
* @package Zedek Framework
* @subpackage Controller class
*/
namespace __zf__;
class CController extends ZController{
	function _default(){
		$this->render("index");
	}
}

Z::webTest();
use UnitTestCase;
class ZTest extends UnitTestCase {
	function __construct(){
		parent::__construct();
		$this->app = new CController;
	}
}