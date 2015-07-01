<?php 

namespace __zf__;
use \Sokoro\ORM as ORM;
require_once zroot."libs/php/sokoro/sokoro";
ORM::config(zroot."config/db.conf"); //use current defalt dn config file
ORM::table("users");

class Auth {
	
	static function createDB(){
		ORM::create("users", [
			'email'=>"varchar(50)", 
			'password'=>"text"
		]);
	}

	static function addRecord(){
		ORM::insert([
			'email'=>"admin@zedek.app", 
			'password'=>_Form::encrypt("anything"), 
		]);
	}

	static function login($email, $password){
		$password = trim($password);
		$password = _Form::encrypt($password);

		if(ORM::exists(['email'=>$email, 'password'=>$password])){
			$user = ORM::row('email', $email);
			return $user;
		} else {
			return false;
		}
	}

	static function logout(){
		session_start();
		session_unset();
		session_destroy();
		session_start();
	}
}
