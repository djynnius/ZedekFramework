<?php
#configuration superclass

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

	public function get($key){
		try{
			if(isset($this->config->{$key})){
				return $this->config->{$key};
			} else {
				throw new Exception("No config value for {$key}");
				return false;
			}
		} catch(Exception $e){
			//print $e->getMessage();
		}
	}

	public function set($key, $value){
		$this->config->{$key} = $value;
		$this->cast();
	}

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