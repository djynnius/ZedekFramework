<?php
/**
* @package Zedek Framework
* @subpackage ZConfig zedek configuration class
* @version 3
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class CController extends ZController{
	function __construct(){
		parent::__construct();
		if(!isset($_SESSION["__zf__"]["user"]["id"])){
			$this->redirect("login");
		}
	}

	function _default(){
		$tmp = $this->tmp();
		self::render($tmp, "index", "admin");
	}

	function logout(){
		$app = new App;
		$app->logout();
		self::redirect();
	}

	function tmp(){
		$app = new App;
		$tmp = $app->tmp();

		$this->orm->dbo = new \PDO("sqlite:".zroot."databases/zedek.db");
		$user = $this->orm->table("users")->row($_SESSION["__zf__"]["user"]["id"]);

		$tmp["username"] = $user->email;
		return $tmp;
	}
}