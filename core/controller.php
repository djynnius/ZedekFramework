<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 4
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
use \Exception as Exception;
abstract class ZController extends Zedek{
	
	public $uri;

	function __construct(){
		$this->uri = new ZURI;
		$this->app = new App;

	}

	function __call($method, $args){
		if(!method_exists($this, $method) || empty($this->uri->method)) $this->index();
	}

	#sets default to render index
	public function index(){
		self::display("404@ztheme", [], "default");
	} 

	public function _error(){
		self::display("404@ztheme");
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
			default:
				return false;
		}
		$controllerFile = zroot."engines/{$name}/controller.php";
		$appFolder = zroot."engines/{$name}";
		$viewFolder = zroot."engines/{$name}/views";
		try{
			if(!file_exists($appFolder)){
				mkdir($appFolder);
				mkdir($viewFolder);
				file_put_contents($controllerFile, $code);
				chmod($appFolder, 0777);
				chmod($viewFolder, 0777);
				chmod($controllerFile, 0777);
			} else {
				throw new ZException("{$name} App exists<br />\r\n");
			}
		} catch(ZException $e){
			return false;
			/*print $e->getMessage();*/
		}		
	}
	
	final protected function template($arg1=null, $arg2=null, $theme=false){
		require_once "view.php";
		return new ZView($arg1, $arg2, $theme);
	}

	#shorter method for rendering
	final protected function render($arg1=null, $arg2=null, $theme=false){
		print self::template($arg1, $arg2, $theme)->render();
	}

	final protected function display($arg1=null, $arg2=null, $theme=false){
		print self::template($arg1, $arg2, $theme)->display();
	}

	final protected function dynamic($arg1=null, $arg2=null, $theme=false, $controller=null){
		return self::template($arg1, $arg2, $theme)->dynamic($controller);
	}
}

