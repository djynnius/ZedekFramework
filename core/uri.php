<?php
/**
* @package Zedek Framework
* @subpackage ZURI Zedek URI Mapping class
* @version 4
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
class ZURI extends Zedek{
	public $http;
	public $https;
	public $port; 
	public $server; // server name eg webapp.com
	public $url; // request uri
	public $controller; // engine name
	public $method; // called method
	public $id; //
	public $arguments; //
	public $gets;
	public $dir;

	function __construct(){
		$sub = zsub == "/" ? "" : zsub;
		$dir = $sub;
		$this->dir = rtrim($dir, "/");
		$subpath = empty($sub) || is_null($sub) ? null : true;	
		$url  = @$_SERVER['REQUEST_URI'];

		//check if using a sub folder
		if(!is_null($subpath)){
			$url = explode(zsub, $url);
			$url = $url[1];
		}
		$this->url = $url;
		$this->mvc($url);
		$get = $this->getRequests();
		$this->serverInfo();
	}

	private function serverInfo(){
		$this->server = @$_SERVER['SERVER_NAME'];
		$this->port = @$_SERVER['SERVER_PORT'];
		$this->http = "http://{$this->server}:{$this->port}";
		$this->https = "https://{$this->server}:{$this->port}";		
	}

	private function mvc($url){
		$mvc = explode("/", $url);
		if(empty($mvc[0]) || is_null($mvc[0])) array_shift($mvc);
		$this->controller = array_shift($mvc);
		$this->method = array_shift($mvc);

		$next = $mvc;
		$this->id = array_shift($next);
		$this->arguments = join("/", $mvc);
	}

	private function getRequests(){
		if(count($_GET) > 0){
			$this->gets = $_GET;
		}
	}

	function __get($attr){
		if(!property_exists($this, $attr)){
			return null;
		} else {
			return;
		}		
	}
}