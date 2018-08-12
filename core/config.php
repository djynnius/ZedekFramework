<?php
/**
* @package Zedek Framework
* @version 4
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

	function __construct($config="global"){
		$this->configFile = zroot."config/".$config.".conf";
		$config = file_get_contents($this->configFile);
		$this->config = json_decode($config);
	}

	/**
	 * Allows for setting which of the .conf files to use defaults to global
	 * @param string $config configuration file name without extension from config folder
	 */
	public function setConfig($config){
		$this->configFile = zroot."config/{$config}.conf";;
		$config = file_get_contents($this->configFile);
		$this->config = json_decode($config);
	}

	/**
	 * gets configuration value
	 * @param  string $key the json key on the config file
	 * @return string the corresponding json value
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
	 * Creates new JSON pair in config file
	 * @param string $key sets json key
	 * @param string $value sets json value
	 */
	public function set($key, $value){
		$this->config->{$key} = $value;
		$this->cast();
	}

	/**
	 * Deletes a json key value pair from the config file
	 * @param  string $key [description]
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


	/**
	 * private method that writes to file changes from self::set()
	 */
	private function cast(){
		$config = json_encode($this->config);
		file_put_contents($this->configFile, $config);		
	}
}