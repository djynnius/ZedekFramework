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

class _Auth {
	
	static public $handle = "email";
	static public $pwd = "password";
	static public $table = "users";
	static public $tableColumns = [];
	static public $user_id;

	static function handle($handle){
		self::$handle = $handle;
	}

	static function pwd($pwd){
		self::$pwd = $pwd;
	}

	static function table($table){
		self::$table = $table;
	}	

	static function tableColumns($c=[]){
		self::$tableColumns = $c;
	}

	static function authorized($sess_var=null){
		return isset($sess_var) ? true : false;
	}

	static function restricted($sess_var=null, $redirect=-1){
		if(!isset($sess_var)){
			Z::redirect($redirect);
			return false;
		}		
	}


	static function getRoles(){}

	static function setUserRole(){}

	static function getUserRoles(){}

	static function addUser($rec=[]){
		ZORM::table(self::$table);
		ZORM::insert($rec);
	}

	static function delUser($id){
		ZORM::table(self::$table);
		ZORM::remove($id);
	}

	static function updateUser($id, $rec){
		ZORM::table(self::$table);
		ZORM::remove($id, $rec);
	}

	static function updateUsers($val, $col, $rec){
		ZORM::table(self::$table);
		ZORM::remove($val, $col, $rec);
	}

	static function changePassword($oldPwd, $newPwd, $confirmPwd, $id=false){
		$oldPwd = trim($oldPwd);
		$newPwd = trim($newPwd);
		$confirmPwd = trim($confirmPwd);

		$id = $id == false ? 0 : $id;
		if(!_Form::same($newPwd, $confirmPwd)){return false;}

		ZORM::table(self::$table);
		if(ZORM::exists($id)){
			$user = ZORM::record($id);
			$pwd = self::$pwd;
			if(_Bcrypt::compare($oldPwd, $user->$pwd)){
				$user->$pwd = _Bcrypt::hash($newPwd);
				$user->commit();			
			}
		}
		return false;
	}	

	static function resetPassword($id, $password){
		$id = (integer)$id;
		ZORM::table(self::$table);

		if(ZORM::exists($id)){
			$pwd = self::$pwd;
			$user = ZORM::record($id);
			$user->$pwd = _Bcrypt::hash($password);
			$user->commit();
		} else {
			return false;
		}
	}	

	static function login($handle, $password){
		$handle = _Form::prepare($handle);
		$password = trim($password);

		ZORM::table(self::$table);
		if(ZORM::exists(self::$handle, $handle)){
			$user = ZORM::record(self::$handle, $handle);
			$pwd = self::$pwd;
			if(_Bcrypt::compare($password, $user->$pwd)){
				self::$user_id = $user->id;
				return $user;				
			}
		}
		return false;
	}

	static function setLastLogin(){
		$user = ZORM::record(self::$user_id);
		$user->last_login = _Form::now();
		$user->commit();
	}	

	static function logout(){
		session_unset();
		session_destroy();
		session_start();
	}

	function __init__($username="admin", $password="zedek", $email='admin@app.io', $mobile='+1234567890'){
		ZORM::create("zf_users", [
			'username'=>"varchar(30)",
			'email'=>"varchar(30)",
			'mobile'=>"varchar(30)",
			'password'=>"text",
			'role'=>"int",
			'last_login'=>"datetime",
		]);
		ZORM::table("zf_users");
		if(ZORM::count() == 0){
			ZORM::add([
				'username'=>_Form::prepare($username),
				'password'=>_Bcrypt::hash($password),
				'email'=>_Form::prepare($email),
				'mobile'=>_Form::prepare($mobile),
				'role'=>1,
				'created_by'=>0
			]);			
		}

	}

}
