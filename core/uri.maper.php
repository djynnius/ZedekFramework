<?php
#url mapper
namespace __zf__;
class URIMaper extends Zedek{
	public $http;
	public $https;
	public $port;
	public $server; // server name eg webapp.com
	public $url; // request uri
	public $controller; // engine name
	public $method; // called method
	public $arguments; //
	public $gets;
	public $args = array();
	public $get_args = array();
	public $dir;

	function __construct(){
		$sub = zsub;
		$dir = $sub;
		$dir = trim($dir); 

		$this->dir = zsub;
		$subpath = empty($sub) || is_null($sub) ? null : true;	
		$url  = @$_SERVER['REQUEST_URI'];

		//check if using a sub folder
		if(!is_null($subpath)){
			$url = explode(zsub, $url);
			$url = @$url[1];
		}
		$this->url = $url;
		$this->mvc($url);
		$get = $this->getRequests($url);
		$this->getArguments($get);
		$this->serverInfo();
	}

	private function serverInfo(){
		$this->server = @$_SERVER['SERVER_NAME'];
		$this->port = @	$_SERVER['SERVER_PORT'];
		$this->http = "http://{$this->server}:{$this->port}";
		$this->https = "https://{$this->server}:{$this->port}";		
	}

	private function mvc($url){
		$mvc = explode("/", $url);
		if(empty($mvc[0]) || is_null($mvc[0])) array_shift($mvc);
		$this->controller = array_shift($mvc);
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
		$this->get_args = $a;		
	}

	function __get($attr){
		if(!property_exists($this, $attr)){
			return null;
		} else {
			return;
		}		
	}
}