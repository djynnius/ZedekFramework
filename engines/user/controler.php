<?php

class CControler extends ZControler{
	function login(){
		if($this->isUser()){
			echo "user";
		} elseif(isset($_POST['submit']) && !empty($_POST['submit'])) {
			unset($_POST['submit']);
			$user = new User();
			$user->login($_POST);
		} else {
			echo $this->template()->render();
		}
		
	}

	function logout(){}

	function recover(){}

	function register(){
		if($this->isUser()){
			echo "User";
		} else {
			echo $this->template()->render();
		}
	}
}

?>