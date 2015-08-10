<?php 

namespace __zf__;

class _Auth {
	
	static public $handle = "email";
	static public $table = "users";
	static public $tableColumns = [];
	static public $user_id;

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

		ZORM::create(ZORM::table(self::$table), $cols);
		ZORM::create("roles", ['role'=>"varchar(30)", 'description'=>"text"]);
		ZORM::create("users_roles", ['role_id'=>"int", 'user_id'=>"int"]);

		ZORM::table("roles");
		ZORM::insert(['role'=>"Admin", 'description'=>"App manager"]);

		ZORM::table("users_roles");
		ZORM::insert(['role_id'=>1, 'user_id'=>1]);

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
		if(ZORM::exists(['id'=>$id, 'password'=>_Form::encrypt($oldPwd)])){
			$user = ZORM::record($id);
			$user->password = _Form::encrypt($newPwd);
			$user->commit();
		} else {
			return false;
		}
	}	

	static function resetPassword($id, $password){
		$id = (integer)$id;
		ZORM::table(self::$table);

		if(ZORM::exists(['id'=>$id, 'password'=>_Form::encrypt($oldPwd)])){
			$user = ZORM::record($id);
			$user->password = _Form::encrypt($password);
			$user->commit();
		} else {
			return false;
		}
	}	

	static function login($handle, $password){
		$handle = _Form::prepare($handle);
		$password = trim($password);
		$password = _Form::encrypt($password);

		ZORM::table(self::$table);
		if(ZORM::exists([self::$handle=>$handle, 'password'=>$password])){
			$user = ZORM::row(self::$handle, $handle);
			self::$user_id = $user->id;
			return $user;
		} else {
			return false;
		}
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
}
