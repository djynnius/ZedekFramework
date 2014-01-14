<?php

namespace __zf__;
use PHPUnit_Framework_TestCase;
	
class ZTest{
	function __construct(){
		$this->getZCore();
		$this->requireInit();
		$this->requireCore();
		$this->requireModels();
	}

	function getZCore(){
		$dir = __dir__;
		$dir = explode('/', $dir);
		array_pop($dir);
		$this->rootDir = join('/', $dir);		
	}

	function requireInit(){
		require_once "{$this->rootDir}/initializer.php";		
	}

	function requireCore(){
		require_once "zedek.php";
		require_once "uri.maper.php";
		require_once "orm.php";
		require_once "controller.php";		
	}

}

class ZUnit extends PHPUnit_Framework_TestCase {
	function setUp(){
		parent::setUp();
		$this->app = new CController;
	}	
}

$foo = new ZTest;