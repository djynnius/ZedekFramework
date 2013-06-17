<?php
#ORM for zedek
class ZModel extends Zedek{
	
	public $dbo;

	function __construct(){
		$this->dbo = new PDO("sqlite:/var/www/info.db");
	}
}
?>