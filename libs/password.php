<?php

namespace __zf__;

#form input pre procesor
class Password extends Zlibs{
	
	function __construct($password){
		$this->password = $password;
	}

	function encrypt(){
		$len = strlen($this->password);
		$md5 = md5($this->password);
		$sha1 = sha1($this->password);
		return $len.$md5.$sha1;
	}
}

?>