<?php

namespace __zf__;

#form input pre procesor
class FormInput extends Zlibs{
	static function compare($a, $b){
		return self::noSpace($a) == $this->noSpace($b) ? true : false;
	}

	/**
		alias for compare
	*/
	static public function same($a, $b){ 
		return self::noSpace($a) == $this->noSpace($b) ? true : false;
	}

	static function minLength($a, $len){
		$a = self::noSpace($a);
		return strlen($a) >= $len ? $a : false; 
	}

	static function maxLength($a, $len){
		$a = self::noSpace($a);
		return strlen($a) <= $len ? $a : false;
	}

	static function number($a){
		$a = self::noSpace($a);
		$re = "/^[0-9]+$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	static function tel($a){
		$a = self::noSpace($a);
		$re = "/^(\+)?[0-9\s]{11,16}$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	static function email($a){
		$a = self::noSpace($a);
		$re = "/^[a-zA-Z0-9.-_]+@[a-zA-Z0-9.-_]+\.[a-z]{2,4}+$/";
		return preg_match($re, $a) == 1 ? $a : false;		
	}

	static function alnum($a){
		$a = self::noSpace($a);
		$re = "/^[a-zA-Z0-9]$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	static function noSpace($a){
		return trim($a);
	}

	function username($a){
		$a = $this->noSpace($a);
		$re = "/^[a-zA-Z]+[a-zA-Z0-9.-_]+$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	function encrypt(){
		$len = strlen($this->password);
		$md5 = md5($this->password);
		$sha1 = sha1($this->password);
		return $len.$md5.$sha1;
	}

	static function shortEncrypt(){
		$time = time();
		$args = func_get_args();
		$count = func_num_args();
		switch($args){
			case 1:
				$code = sha1($args[0]).md5($args[0]).sha1($time);
				break;
			case 2:
				$code = sha1($args[0]).md5($args[1]).sha1($time);
				break;
			case 3:
				$code = sha1($args[0]).md5($args[1]).sha1($args[2]).sha1($time);
				break;
			default:
				$arg = join($args, "-");
				$code = sha1($arg).md5($arg).sha1($time); 
		}
		return crypt($code, "Psalm91:1");
	}	

}

?>