<?php

namespace __zf__;

use __zf__\FormInput as fi;

class User extends ZControler{

	function _login($username, $password){
		$orm = new ZORM();
		$fi = new fi();
		
		$table = $orm->table("users");
		$userRoles = $orm->table("user_roles");

		$username = $fi->username($username);
		$password = $fi->noSpace($password);

		$user = $table->row($username, "username");
		if($user && ($user->password == $password)){
			$_SESSION['__z__']['user']['id'] = $user->id;	
			$_SESSION['__z__']['user']['role'] = $userRoles->row($user->id, "user_id")->role_id;
			header("Location: " . $_SERVER['HTTP_REFERER']);
			//return true;
		} else {
			header("Location: " . $_SERVER['HTTP_REFERER']);
			//return false;
		}
	}

	function _logout(){
		session_destroy();
		header("Location: " . $_SERVER['HTTP_REFERER']);
	}

	#accepts registration array
	function _register($array){
		$orm = new ZORM();
		$users = $orm->table("users");
		#form preprocessor object
		$fi = new fi(); 
		#prepare form inputs
		$array['username'] = $fi->username($array['username']);
		$array['password'] = $fi->compare($array['password'], $array['cpassword']) ? 
								$fi->noSpace($array['password']) : 
								false;
		$array['mobile'] = $fi->tel($array['mobile']);
		$array['email'] = $fi->email($array['email']);
		$array['created_on'] = time(); 
		$array['created_by'] = $_SESSION['__z__']['user']['id']; 
		unset($array['cpassword']);
		unset($array['submit']);
		#if any problem exists
		if(in_array(false, $array)){
			return false;
		}
		#set user
		$user = $orm->table("users");
		if($user->exists("username", $array['username'])){
			return false;
		} else {
			$users->add($array);
		}
		#set user role
		$user = $orm->table('users')->row($array['username'], 'username');
		$userRole = $orm->table("user_roles");
		if(!$userRole->m2mExists('user_id', $user->id, 'role_id', 2)){
			$userRole->add(array('user_id'=>$user->id, 'role_id'=>2));
		}
	}

	function _recoverPassword($username, $answer, $password, $cpassword){
		$orm = new ZORM();
		$table = $orm->table("users");
		$row = $table->row($username, "username");

		$fi = new fi(); //form preprocessor object

		$username = $fi->username($username);
		$password = $fi->compare($password, $cpassword) ? 
								$fi->noSpace($password) : 
								false;
		$answer = $fi->noSpace($answer);

		try{
			if($row->answer == $answer){
				$row->password = $password;
				$row->commit();
			} else {
				return false;
			}			
		} catch(Exception $e){
			return false;
		}		
	}

	function _dbInit(){
		$orm = new ZORM();

		#users table description
		$usersDesc = array(
			'id'=>"integer primary key", 
			'username'=>"varchar", 
			'email'=>"varchar", 
			'mobile'=>"varchar", 
			'password'=>"text", 
			'secret'=>"text", 
			'answer'=>"varchar", 
			'login_status'=>"tinyint", 
			'last_login'=>"datetime", 
			'created_on'=>"datetime", 
			'updated_on'=>"datetime", 
			'created_by'=>"int", 
			'updated_by'=>"int", 
		);

		#roles table description
		$rolesDesc = array(
			'id'=>"integer primary key", 
			'role'=>"varchar", 
			'description'=>"text", 
		);

		#user_roles table description - a many to many relationships table
		$userRolesDesc = array(
			'id'=>"integer primary key", 
			'user_id'=>"int", 
			'role_id'=>"int", 
		);

		#create the tables
		$orm->table("users", $usersDesc);
		$orm->table("roles", $rolesDesc);
		$orm->table("user_roles", $userRolesDesc);

		#create basic roles
		$roles = $orm->table("roles");
		if(!$roles->exists("role", "Administrator")){
			$roles->add(array('role'=>"Administrator", 'description'=>"Application administrator"));
		}

		if(!$roles->exists("role", "User")){
			$roles->add(array('role'=>"User", 'description'=>"Application registered user"));
		}
		
		#set admin (first user) role to admin (first role)
		$userRoles = $orm->table("user_roles");
		if(!$userRoles->m2mExists('user_id', '1', 'role_id', '1')){
			$userRoles->add(array('user_id'=>"1", 'role_id'=>"1"));	
		}
	}

	function _changePassword(){
	}

	function _profile(){}

}

?>