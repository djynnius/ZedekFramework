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

	public function getValue($key){
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

	public function setValue($key, $value){
		$this->config->{$key} = $value;
		$config = json_encode($this->config);
		file_put_contents($this->file, $config);
	}
}
?>