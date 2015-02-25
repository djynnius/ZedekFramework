<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 3.0
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class App extends ZModel{

	const view = "view";
	const edit = "edit";
	const delete = "delete";

	function __construct(){
		parent::__construct();
	}

	function tmp(){
		$tmp = array();
		return $tmp;
	}

	/*User methods*/
	/*-------------------------------------------------------------------------------------------------*/
	function login($email, $password){
		$this->orm->dbo = new \PDO("sqlite:".zroot."databases/zedek.db");
		$email = trim($email);
		$password = trim($password);
		$password = _Form::encrypt($password);
		$this->table = "users";
		if($this->pairExists($email, "email", $password, "password")){
			$user = $this->findOne($email, "email");
			$_SESSION["__zf__"]["user"]["id"] = $user->id;
			return true;
		} else {
			return false;
		}
	}

	function logout(){
		session_unset();
		session_destroy();
		session_start();		
	}

	function addUser($username, $password, $cpassword){
		$this->table = "users";

		$username = trim($username);
		$password = trim($password);
		$cpassword = trim($cpassword);

		if(_Form::compare($password, $cpassword) && !$this->exists($username, "email")){
			$a["username"] = $username;
			$a["password"] = _Form::encrypt($password);
			$this->add($a);
		} else {
			return false;
		}		
	}

	function getRoles($id=false){
		$id = $id == false ? $_SESSION['__pg__']['user']['id'] : $id;
		$sql = "	SELECT role_id FROM users_roles WHERE user_id='{$id}'";
		$init_roles = $this->orm->fetch($sql);
		$roles = array();
		foreach($init_roles as $role){
			$roles[] = $role["role_id"];
		}
		return $roles;
	}

	function setRole($user_id, $role_id){
		if(!$this->orm->table("users_roles")->m2mExists("user_id", $user_id, "role_id", $role_id)){
			$user = $this->orm->table("users_roles");
			$user->add(array("user_id"=>$user_id, "role_id"=>$role_id));
		}
	}	
}
