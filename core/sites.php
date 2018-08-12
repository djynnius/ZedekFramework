<?php
/**
* @package Zedek Framework
* @subpackage ZSites Zedek Multisite Class
* @version 4
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
				//checks if the sites.conf contains the pairing
				if(!isset($this->get($_SERVER["SERVER_NAME"])->engine)) return zroot."engines/";
				$site = $this->get($_SERVER["SERVER_NAME"])->engine;
				if(file_exists(zroot."sites/".$site) && is_dir(zroot."sites/".$site)){
					//the following code ensures the folder exists for the subdomain
					$engine = zroot."sites/".$site."/";
					if(!file_exists($engine."default") && !is_dir($engine."default")){
						//ensures the mandatory default folder and controller as well as view exist
						echo 1;
						$this->createDefault($engine);
					}
				} else {
					mkdir(zroot."sites/".$site);
					chmod(zroot."sites/".$site, 0777);
					$engine = zroot."sites/".$site."/";
					$this->createDefault($engine);
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
		$site = $engine;
		$site = trim($site, "/");
		$site = explode("sites/", $site);
		$site = end($site);
		if(!file_exists($engine."default/") && !is_dir($engine."default/")){
			mkdir($engine."default/");
			mkdir($engine."default/views/");
			chmod($engine."default/", 0777);
			chmod($engine."default/views/", 0777);
			$controller = file_get_contents(zroot."templates/default/controller.php");
			file_put_contents($engine."default/controller.php", $controller);
			chmod($engine."default/controller.php", 0777);
			file_put_contents($engine."default/views/index.html", "<h1>The <span style='text-decoration: underline'>{$site}</span> site was successfully created.</h1>");	
			chmod($engine."default/views/index.html", 0777);		
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