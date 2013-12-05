<?php
namespace __zf__;

class Placeholder {
	static function values($arg = array()){
		$userDetails = self::userDetails();
		$puts = array(
			'username'=>$userDetails->username, 
			'email'=>$userDetails->email, 
			'mobile'=>$userDetails->mobile, 
		);
		$puts = array_merge($puts, $arg);
		return $puts;
	}

	static function orm(){
		return new ZORM;
	}

	static function userDetails(){
		return isset($_SESSION['__z__']['user']['id']) ? self::orm()->table('users')->row($_SESSION['__z__']['user']['id']) : null;
	}

}