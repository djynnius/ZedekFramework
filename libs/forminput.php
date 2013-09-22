<?php

namespace __zf__;

#form input pre procesor
class FormInput extends Zlibs{
	function compare($a, $b){
		return $this->noSpace($a) == $this->noSpace($b) ? true : false;
	}

	function minLength($a, $len){
		$a = $this->noSpace($a);
		return strlen($a) >= $len ? $a : false; 
	}

	function maxLength($a, $len){
		$a = $this->noSpace($a);
		return strlen($a) <= $len ? $a : false;
	}

	function number($a){
		$a = $this->noSpace($a);
		$re = "/^[0-9]+$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	function tel($a){
		$a = $this->noSpace($a);
		$re = "/^(\+)?[0-9\s]{11,16}$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	function email($a){
		$a = $this->noSpace($a);
		$re = "/^[a-zA-Z0-9.-_]+@[a-zA-Z0-9.-_]+\.[a-z]{2,4}+$/";
		return preg_match($re, $a) == 1 ? $a : false;		
	}

	function alnum($a){
		$a = $this->noSpace($a);
		$re = "/^[a-zA-Z0-9]$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

	function noSpace($a){
		return trim($a);
	}

	function username($a){
		$a = $this->noSpace($a);
		$re = "/^[a-zA-Z]+[a-zA-Z0-9.-_]+$/";
		return preg_match($re, $a) == 1 ? $a : false;
	}

}

?>