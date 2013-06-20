<?php
#configuration superclass
class ZConfig extends Zedek{
	
	public $file;
	public $config;

	function __construct(){
		$file = zroot."config/config.json";
		$this->file = $file;
		$config = file_get_contents($file);
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
			//echo $e->getMessage();
		}
	}

	public function set($key, $value){
		$this->config->{$key} = $value;
		self::cast();
	}

	public function remove($key){
		try{
			if(isset($this->config->{$key})){
				unset($this->config->{$key});
				self::cast();
			} else {
				throw new Exception("The configuratiion does not exist.");
			}
		} catch(Exception $e){
			return $e->getMessage();
		}
	}

	private function cast(){
		$config = json_encode($this->config);
		file_put_contents($this->file, $config);		
	}
}
?>