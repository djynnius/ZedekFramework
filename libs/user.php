<?php

namespace djynnius\zedekframework;

class User extends Zlibs{
	
	function login($array){
		print_r($array);
		$username = $array['username'];
	}

	function logout(){}

	function createTable($array){
		$$this->orm->table("", $array);
	}

	function add($array){
	}

	function update(){}

	function remove(){}
}

?>