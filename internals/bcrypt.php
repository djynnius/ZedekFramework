<?php 
/**
* @package Zedek Framework
* @version 5
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class _Bcrypt {

	/**
	* @param string $string password
	* @param int $rounds 
	* @return string hashed password
	*/
	static public function hash($string, $rounds=10){
		$encrypted = _Form::encrypt($string);
		return password_hash($encrypted, PASSWORD_BCRYPT, ['cost'=>$rounds]);
	}

	/**
	* @param string $string password
	* @param string $hash hashed password
	* @return boolean
	*/
	static public function compare($string, $hash){
		$encrypted = _Form::encrypt($string);
		return password_verify($encrypted, $hash);
	}

}