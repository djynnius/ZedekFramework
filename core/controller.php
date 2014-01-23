<?php
#class abstract class/super class

namespace __zf__;
use \Exception as Exception;
abstract class ZController extends Zedek implements ZIController{
	public $orm;
	public $uri;
	static private $scaffold_file_names = array(
		'read', 
		'view', 
		'update', 
	);

	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new URIMaper;
		$this->_init();	
	}

	function __call($method, $args){
		if(!method_exists($this, $method)) $this->_default();
	}

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

	/**
		replaces construct for all classs
	*/
	public function _init(){}

	final protected function template($arg1=null, $arg2=null){
		require_once "view.php";
		return new ZView($arg1, $arg2);
	}

	#shorter method for rendering
	final protected function render($arg1=null, $arg2=null){
		print $this->template($arg1, $arg2)->render();
	}

	final protected function display($arg1=null, $arg2=null){
		print $this->template($arg1, $arg2)->display();
	}

	#sets default to render index
	public function _default(){
		$this->render("index");
	} 

	public function _placeholders(){
		return array();
	}

	final public function _bounce($msg=false){
		$msg = $msg == false ? "" : "msg={$msg}";
		if(isset($_SERVER['HTTP_REFERER'])){
			$url = $_SERVER['HTTP_REFERER'];
			$url = explode("?msg=", $url);
			$url = $url[0];
			$url = strpos($url, "?") != false ? $url."&{$msg}" : rtrim($url, "/")."/?{$msg}";
			header("Location: {$url}");
		} else {
			header("Location: /"."{$msg}");
		}
	}

	final function paginate($array, $page=1, $count=10){
		$array_size = count($array);
		$pages = ceil($array_size/$count);
		$a = array();
		$start = ($page - 1);
		for($i=($start*$count); $i<($page*$count); $i++){
			if(isset($array[$i])) $a[] = $array[$i];
		}
		$puts = array(
			'data'=>$puts, 
			'pages'=>$pages
		);
		return $puts;
	}	
}

interface ZIController{
	function _init();
	function _default();
	function _placeholders();
}