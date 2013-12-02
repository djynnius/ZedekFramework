<?php

namespace __zf__;

#form input pre procesor
class Form extends Zlibs{
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

	static function encrypt($password){
		$len = strlen($password);
		$md5 = md5(crypt($password, 'Dan2:22'));
		$sha1 = sha1(crypt($password, 'Dan2:47'));
		$len = crypt(sha1(md5($len)), 'Ps91:1');
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

	static function field($type, $params, $selected, $multiple){
		switch($type){
			case 'input':
			case 'textarea':
			case 'button':
				$puts = self::$type($params);
				break;
			case 'select':
				$puts = self::select($params, $selected, $multiple);
				break;
			default:
				return false;
		}
		return $puts;
	}

	static function input($params = array('name'=>null, 'type'=>null, 'value'=>null, 'placeholder'=>null, 'required'=>null, 'autocomplete'=>null, 'autofocus'=>null, 'min'=>null, 'max'=>null, 'step'=>null)){
		switch($type){
			case 'text':
			case 'email':
			case 'tel':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' placeholder={$placeholder} required autocomplete autofocus />";
				break;
			case 'range':
			case 'num':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' placeholder={$placeholder} min='{$min}' max='{$min}' step='{$min}' required autocomplete autofocus />";
				break;
			case 'radio':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' />";
				break;
			case 'checkbox':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' checked='checked' />";
				break;
			case 'file':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' />";
				break;
			case 'hidden':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' />";
				break;
			default:
				return false;
		}
		return $puts;
	}

	static function textarea($params = array('name'=>null, 'value'=>null, 'placeholder'=>null)){
		return $puts = "<textarea name='{$name}' placeholder={$placeholder}> $value </textarea>";
	}

	static function select($params = array('name'=>null, 'options'=>array()), $selected=null, $multiple = null){
		switch($multiple){
			case 1:
				$puts = "<select name='{$name}[]' multiple>";
				foreach($options as $k=>$v){
					$puts .= "<option value='{$k}' selected='selected'>{$v}</option>";
				}
				$puts .= "</select>";
				break;
			default:
				$puts = "<select name='{$name}'>";
				foreach($options as $k=>$v){
					$puts .= "<option value='{$k}' selected='selected'>{$v}</option>";
				}
				$puts .= "</select>";
		}
		return $puts;
	}

	static function button($params = array('name'=>null, 'type'=>null, 'value'=>null)){
		switch($type){
			case 'submit':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' />";
				break;
			case 'button':
				$puts = "<input type='{$type}' name='{$name}' value='{$value}' />";
				break;
			default:
				return false;
		}
		return $puts;
	}

	static function dateTime(){}

	static function date(){}

}