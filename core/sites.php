<?php
/**
* @package Zedek Framework
* @subpackage ZSites Zedek Multisite Class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class ZSites{

	private $config = "sites";
	private $c;

	function __construct(){
		$this->c = new ZConfig;
		$this->c->setConfig("sites");
	}

	/**
	* @return string $engine current engine ie controller view bundle requested 
	*/
	function getEngine(){
		switch($_SERVER["SERVER_NAME"]){
			case @property_exists($this->getSites(), $_SERVER["SERVER_NAME"]):  
				//checks if the sites.json contains the pairing
				if(!isset($this->get($_SERVER["SERVER_NAME"])->engine)) return zroot."engines/";
				$site = $this->get($_SERVER["SERVER_NAME"])->engine;
				if(file_exists(zroot."sites/".$site) && is_dir(zroot."sites/".$site)){
					//the following code ensures the folder exists for the subdomain
					$engine = zroot."sites/".$site."/";
					if(!file_exists($engine."default") && !is_dir($engine."default")){
						//ensures the mandatory default folder and controller as well as view exist
						$this->createDefault($engine);
					}
				} else {
					$engine = zroot."engines/";
				}					
				break;
			default:
				$engine = zroot."engines/";
		}
		return $engine;
	}

	/**
	* @param string $site site address
	* @return string 
	*/
	function get($site){
		return $this->c->get($site);
	}

	/**
	* @param string $key config key for site
	* @param string $value config value for site
	*/	
	function set($key, $value){
		$this->c->set($key, $value);
	}

	/**
	* @param string $engine default
	*/
	function createDefault($engine){
		if(!file_exists($engine."default/") && !is_dir($engine."default/")){
			mkdir($engine."default/");
			mkdir($engine."default/view/");
			chmod($engine."default/", 0777);
			chmod($engine."default/view/", 0777);
			$controller = file_get_contents(zroot."templates/default/controller.php");
			file_put_contents($engine."default/controller.php", $controller);
			chmod($engine."default/controller.php", 0777);
			file_put_contents($engine."default/view/index.html", "");	
			chmod($engine."default/view/index.html", 0777);		
		}

	}

	/**
	* @todo fix
	*/
	function getConfig($site){}

	/**
	* @todo fix
	*/
	function getSites(){
		return $this->c->config;
	}

	/**
	* @todo fix
	*/
	function exists($server_name){}

}

$s = new ZSites;
//print_r((array)$s->getSites());