<?php

namespace djynnius\zedekframework;

use ZORM;
use djynnius\zedekframework\FormInput as fi;

class User extends Zlibs{

	function login($username, $password){
		$orm = new ZORM();
		$fi = new fi();
		
		$table = $orm->table($orm->prefix."users");
		$userRoles = $orm->table($orm->prefix."user_roles");

		$username = $fi->username($username);
		$password = $fi->noSpace($password);

		$user = $table->row($username, "username");
		if($user && ($user->password == $password)){
			$_SESSION['__z__']['user']['id'] = $user->id;	
			$_SESSION['__z__']['user']['role'] = $userRoles->row($user->id, "user_id")->role_id;
			return true;
		} else {
			return false;
		}
	}

	function logout(){
		session_destroy();
	}

	#accepts registration array
	function register($array){
		$orm = new ZORM();
		$users = $orm->table($orm->prefix."users");

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
		$array['created_by'] = "1"; 

		unset($array['cpassword']);
		unset($array['submit']);

		#if any problem exists
		if(in_array(false, $array)){
			return false;
		}

		#set user
		$table = $orm->table($orm->prefix."users");
		if($table->exists("username", $array['username'])){
			return false;
		} else {
			$users->add($array);
		}

		#set user role
		$userRole = $orm->table($orm->prefix."user_roles");
		if(!$userRoles->m2mExists('user_id', $user->id, $role_id, 2)){
			$userRoles->add(array('user_id'=>$user->id, 'role_id'=>2));
		}
	}

	function recoverPassword($username, $answer, $password, $cpassword){
		$orm = new ZORM();
		$table = $orm->table($orm->prefix."users");
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
		$orm->table($orm->prefix."users", $usersDesc);
		$orm->table($orm->prefix."roles", $rolesDesc);
		$orm->table($orm->prefix."user_roles", $userRolesDesc);

		#create basic roles
		$roles = $orm->table($orm->prefix."roles");
		if(!$roles->exists("role", "Administrator")){
			$roles->add(array('role'=>"Administrator", 'description'=>"Application administrator"));
		}

		if(!$roles->exists("role", "User")){
			$roles->add(array('role'=>"User", 'description'=>"Application registered user"));
		}
		
		#set admin (first user) role to admin (first role)
		$userRoles = $orm->table($orm->prefix."user_roles");
		if(!$userRoles->m2mExists('user_id', '1', 'role_id', '1')){
			$userRoles->add(array('user_id'=>"1", 'role_id'=>"1"));	
		}
	}

}

/*
SNIPPETS

CONTROLER
use djynnius\zedekframework\User as libUser;

class CControler extends ZControler{
	function register(){
		if(!$this->isUser() && !isset($_POST['submit'])){
			echo $this->template("register")->render();
		} elseif($_POST['submit']){
			$libUser = new libUser();
			$libUser->register($_POST);
		} else {
			header("Location: /");
		}
	}

	function reset(){
		if(!$this->isUser() && !isset($_POST['submit'])){
			echo $this->template("recover")->render();
		} elseif($_POST['submit']){
			$libUser = new libUser();
			$libUser->recoverPassword($_POST['username'], $_POST['answer'], $_POST['password'], $_POST['cpassword']);
			header("Location: /");
		} else {
			header("Location: /");
		}
	}

	function login(){
		if(!$this->isUser() && !isset($_POST['submit'])){
			echo $this->template("login")->render();
		} elseif(isset($_POST['submit'])){
			$libUser = new libUser();
			$libUser->login($_POST['username'], $_POST['password']);
			header("Location: /");
		} else {
			echo $this->template("member", $this->_placeholders())->render();
		}
	}

	function logout(){
		$libUser = new libUser();
		$libUser->logout();
		header("Location: /");
	}

}

VIEW-RECOVER PASSWORD
<form method=post action=/reset>
	<p>Username</p>
	<input type=text name=username>
	<p>Answer to Secret Question</p>
	<input type=password name=answer>
	<p>New Password</p>
	<input type=password name=password>
	<p>Confirm New Password</p>
	<input type=password name=cpassword>
	<p></p>
	<input type=submit name=submit value='Reset Password'>
</form>

VIEW-REGISTER
<form method=post action=/register>
	<p>Username</p>
	<input type=text name=username >
	<p>email</p>
	<input type=email name=email >
	<p>Mobile</p>
	<input type=tel name=mobile >
	<p>Password</p>
	<input type=password name=password >
	<p>Confirm Password</p>
	<input type=password name=cpassword >
	<p></p>
	<input type=submit name=submit value=Register >
</form>

VIEW-LOGIN
<form action=/ method=post>
	<p>Username</p>
	<input name=username type=text>
	<p>Password</p>
	<input name=password type=password>
	<p></p>
	<input name=submit type=submit value=Login>
</form>

VIEW-USER
Welcome {{username}} <a href=/logout>Logout</a>

*/

?>