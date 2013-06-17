<?php
#url mapper
class URIMaper extends Zedek{
	public $url;
	public $controler;
	public $method;
	public $gets;
	public $args = array();

	function __construct(){
		$url = $_SERVER['REQUEST_URI'];
		$this->url = $url;

		$mvc = explode("/", $url);
		array_shift($mvc);
		$this->controler = array_shift($mvc);
		$this->method = array_shift($mvc);
		$this->arguments = join("/", $mvc);

		$get = explode("?", $url);
		array_shift($get);
		$this->gets = array_shift($get);

		$args = explode("&", $this->gets);
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