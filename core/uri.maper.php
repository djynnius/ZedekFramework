<?php
#url mapper
namespace __zf__;
class URIMaper extends Zedek{
	public $url;
	public $controler;
	public $method;
	public $arguments;
	public $gets;
	public $args = array();

	function __construct($subpath=false){
		$url = $_SERVER['REQUEST_URI'];
		$this->url = $url;
		$this->mvc($subpath, $url);
		$get = $this->getRequests($url);
		$this->getArguments($get);
	}

	private function mvc($subpath, $url){
		$mvc = $subpath == false ? 
			explode("/", $url) : 
			explode("{$subpath}/", $url);
		array_shift($mvc);
		$this->controler = array_shift($mvc);
		$this->method = array_shift($mvc);
		$this->arguments = join("/", $mvc);

	}

	private function getRequests($url){
		$get = explode("?", $url);
		array_shift($get);
		$this->gets = array_shift($get);		
		return $this->gets;
	}

	private function getArguments($gets){
		$args = explode("&", $gets);
		$a = array();
		foreach($args as $arg){
			if(strpos($arg, "=") != false){
				$item = explode("=", $arg);
				$k = $item[0];
				$a[$k] = $item[1];
			} else {
				continue;
			}
		}
		$this->args = $a;		
	}

	function __get($attr){
		if(!property_exists($this, $attr)){
			return null;
		} else {
			return;
		}
		
	}
}
?>