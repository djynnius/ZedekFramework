<?php

class User extends ZApp{
	function login($array){
		print_r($array);
		$orm = new ZORM();
	}

	function createUsersTable($orm){
		$array = array(
			'id'=>"integer primary key", 
			'username'=>"varchar", 
			'email'=>"varchar", 
			'mobile'=>"varchar", 
			'password'=>"text", 
			'created_by'=>"int", 
			'created_on'=>"datetime", 
			'upated_by'=>"int", 
			'upated_on'=>"timestamp", 
			'last_login'=>"datetime", 
		);
		$user = $orm->table("users", $array);
	}

	function createRolesTable($orm){
		$array = array(
			'id'=>"integer primary key", 
			'role'=>"varchar", 
			'description'=>"text", 
		);
		$user = $orm->table("roles", $array);		
	}

	function createUserRolesTable($orm){
		$array = array(
			'id'=>"integer primary key", 
			'role'=>"int", 
			'user_id'=>"int", 
		);
		$user = $orm->table("user_roles", $array);		
	}
}

?>