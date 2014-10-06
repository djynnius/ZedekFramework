<?php
/**
* @package Zedek Framework
* @version 3
* @subpackage ZConfig zedek configuration class
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
use \Exception as Exception;

class ZConfig extends Zedek{
	
	public $config;
	public $configFile;

	function __construct(){
		$this->configFile = zroot."config/config.json";;
		$config = file_get_contents($this->configFile);
		$this->config = json_decode($config);
	}

	/**
	* @param string $config configuration file without extension from config folder
	*/
	public function setConfig($config){
		$this->configFile = zroot."config/{$config}.json";;
		$config = file_get_contents($this->configFile);
		$this->config = json_decode($config);
	}

	/**
	* @param string $key 
	*/
	public function get($key){
		try{
			if(isset($this->config->{$key})){
				return $this->config->{$key};
			} else {
				throw new Exception("No config value for {$key}");
				return false;
			}
		} catch(Exception $e){
			#print $e->getMessage();
		}
	}


	/**
	* @param string $key simple string as key
	* @param string $value may be a json string or plain string
	*/
	public function set($key, $value){
		$this->config->{$key} = $value;
		$this->cast();
	}

	/**
	* @param string $key 
	*/
	public function remove($key){
		try{
			if(isset($this->config->{$key})){
				unset($this->config->{$key});
				$this->cast();
			} else {
				throw new Exception("The configuratiion does not exist.");
			}
		} catch(Exception $e){
			return $e->getMessage();
		}
	}

	private function cast(){
		$config = json_encode($this->config);
		file_put_contents($this->configFile, $config);		
	}
}