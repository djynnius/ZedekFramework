<?php

namespace __zf__;
use PHPUnit_Framework_TestCase;
	
class Foo{
	function __construct(){
		$this->getZCore();
		$this->requireInit();
		$this->requireCore();
		$this->requireLibs();
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
		require_once "{$this->rootDir}/core/zedek.php";
		require_once "{$this->rootDir}/core/uri.maper.php";
		require_once "{$this->rootDir}/core/orm.php";
		require_once "{$this->rootDir}/core/model.php";		
	}

	function requireLibs(){
		$plugins = scandir("{$this->rootDir}/libs/");
		foreach($plugins as $plugin){
			if(!is_dir("{$this->rootDir}/libs/{$plugin}")){
				require_once "{$this->rootDir}/libs/{$plugin}";				
			}
		}
	}	

}

class ZUnit extends PHPUnit_Framework_TestCase {
	function setUp(){
		parent::setUp();
		$this->app = new CModel;
	}	
}

$foo = new Foo;

?>