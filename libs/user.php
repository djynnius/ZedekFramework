<?php

namespace __zf__;

use __zf__\FormInput as fi, \Exception as Exception;

class User extends ZModel{
	public $role;

	function _init(){
		if($this->isAdmin()) $this->role = "admin";
		if($this->isUser()) $this->role = "user";
		if(!$this->isUser()) $this->role = "guest";
	}

	function _default(){
		$orm = $this->orm;
		$placeholders = array(
			'users'=>$orm->table('users')->fetch(), 
		);
		switch($this->role){
			case 'admin': 
				echo $this->template("admin", $placeholders)->render();
				break;
			case 'user':
				echo $this->template("user", $placeholders)->render();
				break;
			case 'guest':
				echo $this->template("guest", $placeholders)->render();
				break;
			default:
				echo $this->template("index", $placeholders)->render();
		}
	}

	function _login($username, $password){
		$orm = $this->orm;
		$table = $orm->table("users");
		$userRoles = $orm->table("user_roles");
		$username = fi::username($username); //clean username input
		$password = fi::noSpace($password); //remove spaces from password
		$user = $table->row($username, "username"); //check to ensure user exists in database with password
		if($user && ($user->password == fi::encrypt($password))){
			$_SESSION['__z__']['user']['id'] = $user->id;	
			$_SESSION['__z__']['user']['role'] = $userRoles->row($user->id, "user_id")->role_id;
			$this->_bounce();
		} else {
			$this->_bounce("username-or-password-incorrect");
		}
	}

	function _logout(){
		session_destroy();
		$this->_bounce();
	}

	#accepts registration array
	function _register($array){
		$orm = $this->orm;
		$users = $orm->table("users");
		$array['username'] = fi::username($array['username']);
		$array['password'] = fi::compare($array['password'], $array['cpassword']) ? 
								fi::noSpace($array['password']) : 
								false;
		$array['mobile'] = fi::tel($array['mobile']);
		$array['email'] = fi::email($array['email']);
		$array['created_on'] = time(); 
		$array['created_by'] = $_SESSION['__z__']['user']['id']; 
		unset($array['cpassword']);
		unset($array['submit']);
		if(in_array(false, $array)) return false; #if any problem exists
		$user = $orm->table("users");
		if($user->exists("username", $array['username'])){
			return false;
		} else {
			$users->add($array);
		}
		$this->_setRole($array, $orm);
	}

	function _setRole($array, $orm){
		$user = $orm->table('users')->row($array['username'], 'username');
		$userRole = $orm->table("user_roles");
		if(!$userRole->m2mExists('user_id', $user->id, 'role_id', 2)){
			$userRole->add(array('user_id'=>$user->id, 'role_id'=>2));
		}		
	}

	/**
	* @depends _registerByEmail
	*/
	function _emailRegistration(){
		$placeholders = array(
			'msg'=>Z::message(),
		);
		if(isset($_POST['submit'])){
			unset($_POST['submit']);
			$this->_registerByEmail($_POST);
		} else {
			echo $this->template($placeholders)->render();	
		}		
	}

	private function _registerByEmail($a){
		if(fi::same(trim($a['password']), trim($a['cpassword']))){
			unset($a['cpassword']);
			$a['password'] = fi::noSpace($a['password']);
			$a['created_on'] = strftime("%Y-%m-%d %H:%M:%S", time());
			$a['status'] = 1;
			$a['code'] = fi::shortEncrypt($a['email'], $a['password']);
			$a['password'] = fi::encrypt($a['password']);
			$orm = new ZORM;
			$user = $orm->table('registrations');
			$user->add($a);
			$this->_sendConfirmationMail($a['email'], $a['code']);		
		} else {
			$this->_bounce("password-mismatch");
		}
	}	


	/**
	* @depends _registerByEmail
	* @depends _emailRegistration
	*/
	private function _sendConfirmationMail($email, $code){
		$uri = new URIMaper;
		$config = new ZConfig;
		$message = "
			Click on the link to complete your registration:
			{$uri->http}/user/confirm/{$code}
		";
		mail($email, "Welcome to {$config->get(appName)}", $message);
		header("Location: /user/complete/{$email}");
	}

	/**
		alias for _completeMailRegistration
	*/
	function complete(){
		$this->_completeMailRegistration();
	}

	private function _completeMailRegistration($email=false){
		$email = $this->currentIndex();
		$placeholders = array(
			'email'=>$email
		);
		echo $this->template($placeholders)->render();
	}

	/**
		alias for _confirmEmailRegistration
	*/
	function confirm(){
		$this->_confirmEmailRegistration();
	}

	private function _confirmEmailRegistration(){
		$code = $this->currentIndex();
		$orm = $this->orm;
		if($orm->table('registrations')->exists('code', $code)){
			$reg = $orm->table('registrations')->row($code, 'code');
			$user = array(
				'phonenumber'=>$reg->phonenumber, 
				'email'=>$reg->email, 
				'password'=>$reg->password, 
				'created_on'=>$reg->created_on, 
			);
			$orm->table('users')->add($user);
			$reg->status = 2;
			$reg->commit();
			header("Location: /");			
		} else {
			header("Location: /user/unconfirmed");
		}
	}

	/**
		alias for _unconfirmedEmailRegistration
	*/
	function unconfirmed(){
		$this->_unconfirmedEmailRegistration();
	}

	private function _unconfirmedEmailRegistration(){
		echo $this->template()->render();
	}	

	function _recoverPassword($username, $answer, $password, $cpassword){
		$orm = $this->orm;
		$table = $orm->table("users");
		$row = $table->row($username, "username");
		$username = fi::username($username);
		$password = fi::compare($password, $cpassword) ? fi::noSpace($password) : false;
		$answer = fi::noSpace($answer);
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

	/**
	* @depends _sendRecoveryMessage
	* @depends recoverpassword
	*/
	function _recoverPasswordByEmail(){
		$orm = $this->orm;
		if(isset($_POST['submit'])){
			$this->_sendRecoveryMessage($_POST['email'], $orm);
		} else {
			echo $this->template()->render();	
		}
	}

	/**
	* @depends recoverpassword
	*/
	private function _sendRecoveryMessage($email, $orm){
		$uri = new URIMaper;
		if($orm->table('users')->exists('email', $email)){
			$user = $orm->table('users')->row($email, 'email');
			$code = fi::shortEncrypt($user->password, $user->email, $user->phonenumber);
			$user->password = $code;
			$user->commit();
			$message = "
				Click on the link below to change your password:\r\n
				{$uri->http}/user/passwordreset/{$code}
			";
			mail($email, "Password recovery from phonebook", $message);
			header("Location: /user/recoverpassword");			
		} else {
			$this->_bounce("bad-email");
		}
	}

	/**
	* @depends _sendRecoveryMessage
	*/
	function recoverpassword(){
		echo $this->template()->render(); 
	}

	/**
		alias for _passwordResetFromEmail
	*/
	function passwordreset(){
		$this->_passwordResetFromEmail();
	}

	function _passwordResetFromEmail(){
		$orm = $this->orm;
		$placeholders = array(
			'code'=>$this->currentIndex(), 
		);
		if(isset($_POST['submit'])){
			$this->_compareAndResetPassword($_POST, $orm);
		} else {
			echo $this->template($placeholders)->render();
		}
	}

	private function _compareAndResetPassword($a, $orm){		
		$user = $orm->table('users')->row($a['code'], 'password');
		if(fi::compare($a['password'], $a['cpassword'])){
			$password = trim($a['password']);
			$password = fi::encrypt($password);
			$user->password = $password;
			$user->commit();	
			header("Location: /");					
		} else {
			$this->_bounce("inconsistent-password");
		}
	}

	function _dbInit(){
		$orm = $this->orm;

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

		#registration table description
		$registerDesc = array(
			'id'=>"integer primary key", 
			'username'=>"varchar", 
			'email'=>"varchar", 
			'mobile'=>"varchar", 
			'password'=>"text", 
			'code'=>"text", 
			'status'=>"int", 
			'created_on'=>"datetime", 
			'updated_on'=>"timestamp", 
		);

		#registration status table description
		$registerStatusDesc = array(
			'id'=>"integer primary key", 
			'status'=>"varchar", 
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
		$orm->table("register", $registerDesc);
		$orm->table("registration_status", $registerStatusDesc);

		#create basic roles
		$roles = $orm->table("roles");
		if(!$roles->exists("role", "Administrator")){
			$roles->add(array('role'=>"Administrator", 'description'=>"Application administrator"));
			$roles->add(array('role'=>"User", 'description'=>"Application registered user"));
		}
		
		#set admin (first user) role to admin (first role)
		$userRoles = $orm->table("user_roles");
		if(!$userRoles->m2mExists('user_id', '1', 'role_id', '1')){
			$userRoles->add(array('user_id'=>"1", 'role_id'=>"1"));	
		}

		#create basic registration status
		$roles = $orm->table("roles");
		if(!$roles->exists("status", "pending")){
			$roles->add(array('status'=>"pending"));
			$roles->add(array('status'=>"confirmed"));
			$roles->add(array('status'=>"expired"));
		}
	}

	function profile(){
		if(!$this->isUser()) header("Location: /");
		echo $this->template($this->_placeholders())->render();
	}	

	function update(){
		if(!$this->isUser()) header("Location: /");
		$orm = $this->orm;
		if(isset($_POST['submit'])){
			unset($_POST['submit']);
			$id = isset($_POST['id']) ? $_POST['id'] : $_SESSION['__z__']['user']['id'];
			unset($_POST['id']);
			$_POST['user_id'] = $id;
			if($orm->table('profiles')->exists('user_id', $_POST['id'])){
				$orm->table('profiles')->update($id, $_POST, 'user_id');	
			} else {
				$orm->table('profiles')->add($_POST);
			}
			header("Location: /user/profile");
		} else {
			echo $this->template($this->_placeholders())->render();	
		}
	}

	function remove(){
		if(!$this->isAdmin()) header("Location: /");
		$orm = $this->orm;
		$orm->table('users')->remove($this->currentIndex());
		$orm->table('profiles')->remove($this->currentIndex(), 'user_id');
		$this->_bounce();
	}

	private function idSetter(){
		if($this->isAdmin()){
			$uri  = new URIMaper;
			$id = empty($uri->arguments) ? 
				$_SESSION['__z__']['user']['id'] : 
				$this->currentIndex();
		} else{
			$id = $_SESSION['__z__']['user']['id'];
		}
		return $id;		
	}

	function _placeholders(){
		$id = $this->idSetter();
		$orm = $this->orm;
		$user = $orm->table('users')->row($id);
		$profile = $orm->table('profiles')->row($id, 'user_id');
		return array(
		);
	}
}

?>	
