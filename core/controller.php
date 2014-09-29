<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
use \Exception as Exception;
abstract class ZController extends Zedek{
	
	public $orm;
	public $uri;

	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new ZURI;
		$this->app = new App;
	}

	function __call($method, $args){
		if(!method_exists($this, $method)) $this->_default();
	}

	#sets default to render index
	public function _default(){
		$this->display("404");
	} 

	#controllar class templating array
	/**
	* @return array
	*/
	public function _tmp(){
		return $this->app->tmp();
	}

	public function _error(){
		$this->display("404");
	}

	/**
	* @param string $name of controller to be created
	* @param
	* @param
	*/
	final static public function create($name, $bool=0, $table=null){
		$args = func_num_args();
		$args = count($args);
		switch($args){
			case 1:
				$code = file_get_contents(zroot."templates/controller.tmp");				
				break;
			case 3:
				$code = file_get_contents(zroot."templates/scaffold_controller.tmp");
				$code = str_replace("{{table}}", $table, $code);
				$code = str_replace("{{app_name}}", $name, $code);				
				break;				
			default:
				return false;
		}
		$controllerFile = zroot."engines/{$name}/controller.php";
		$appFolder = zroot."engines/{$name}";
		$viewFolder = zroot."engines/{$name}/view";
		try{
			if(!file_exists($appFolder)){
				mkdir($appFolder);
				mkdir($viewFolder);
				file_put_contents($controllerFile, $code);
				chmod($appFolder, 0777);
				chmod($viewFolder, 0777);
				chmod($controllerFile, 0777);
				self::insertScaffoldViewFiles($name, $args);
			} else {
				throw new ZException("{$name} App exists<br />\r\n");
			}
		} catch(ZException $e){
			return false;
			#print $e->getMessage();
		}		
	}
	
	final static private function insertScaffoldViewFiles($name, $args=0){
		if($args != 3) return false;
		$enumerate = self::$scaffold_file_names;
		foreach($enumerate as $item){
			$code = file_get_contents(zroot."templates/scaffold_view_{$item}.tmp");
			$code = str_replace("{{table}}", $table, $code);
			$code = str_replace("{{app_name}}", $name, $code);		
			$file = zroot."engines/{$name}/view/{$item}.html";
			file_put_contents($file, $code);
			chmod($file, 0777);
		}
	}

	final protected function template($arg1=null, $arg2=null, $theme=false){
		require_once "view.php";
		return new ZView($arg1, $arg2, $theme);
	}

	#shorter method for rendering
	final protected function render($arg1=null, $arg2=null, $theme=false){
		print $this->template($arg1, $arg2, $theme)->render();
	}

	final protected function display($arg1=null, $arg2=null, $theme=false){
		print $this->template($arg1, $arg2, $theme)->display();
	}

	final protected function displayIndex($arg1=null, $arg2=null, $theme=false){
		print $this->template($arg1, $arg2, $theme)->displayIndex();
	}

	final protected function display404($arg1=null, $arg2=null, $theme=false){
		print $this->template($arg1, $arg2, $theme)->display404();
	}

	final protected function dynamic($arg1=null, $arg2=null, $theme=false, $controller=null){
		return $this->template($arg1, $arg2, $theme)->dynamic($controller);
	}
}