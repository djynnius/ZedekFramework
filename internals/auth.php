<?php 

namespace __zf__;

class _Auth {
	
	static public $handle = "email";
	static public $table = "users";
	static public $tableColumns = [];

	static function handle($handle){
		self::$handle = $handle;
	}

	static function table($table){
		self::$table = $table;
	}

	static function tableColumns($c=[]){
		self::$tableColumns = $c;
	}

	static function createDB(){
		$cols = self::$tableColumns;
		$cols["password"] = "text";

		_ORM::create(_ORM::table(self::$table), $cols);
		_ORM::create("roles", ['role'=>"varchar(30)", 'description'=>"text"]);
		_ORM::create("users_roles", ['role_id'=>"int", 'user_id'=>"int"]);

		_ORM::table("roles");
		_ORM::insert(['role'=>"Admin", 'description'=>"App manager"]);

		_ORM::table("users_roles");
		_ORM::insert(['role_id'=>1, 'user_id'=>1]);

	}

	static function getRoles(){}

	static function setUserRole(){}

	static function getUserRoles(){}

	static function addUser($rec=[]){
		_ORM::table(self::$table);
		_ORM::insert($rec);
	}

	static function delUser($id){
		_ORM::table(self::$table);
		_ORM::remove($id);
	}

	static function updateUser($id, $rec){
		_ORM::table(self::$table);
		_ORM::remove($id, $rec);
	}

	static function updateUsers($val, $col, $rec){
		_ORM::table(self::$table);
		_ORM::remove($val, $col, $rec);
	}

	static function changePassword($oldPwd, $newPwd, $confirmPwd, $id=false){
		$oldPwd = trim($oldPwd);
		$newPwd = trim($newPwd);
		$confirmPwd = trim($confirmPwd);

		$id = $id == false ? $_SESSION["__zf__"]["user"]["id"] : $id;
		if(!_Form::same($newPwd, $confirmPwd)){return false;}

		_ORM::table(self::$table);
		if(_ORM::exists(['id'=>$id, 'password'=>_Form::encrypt($oldPwd)])){
			$user = _ORM::record($id);
			$user->password = _Form::encrypt($newPwd);
			$user->commit();
		} else {
			return false;
		}
	}	

	static function login($handle, $password){
		$handle = _Form::prepare($handle);
		$password = trim($password);
		$password = _Form::encrypt($password);

		_ORM::table(self::$table);
		if(_ORM::exists([self::$handle=>$handle, 'password'=>$password])){
			$user = _ORM::row(self::$handle, $handle);
			return $user;
		} else {
			return false;
		}
	}

	static function logout(){
		session_unset();
		session_destroy();
		session_start();
	}
}
