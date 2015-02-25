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
	function _default(){
		$this->displayIndex();
	}

	function about(){
		$this->render('about');
	}

	function contact(){
		$this->render('contact');
	}

	/*Admin*/
	function login(){
		if(_Form::submitted()){
			$app = new App;
			if(empty($_POST["email"]) || empty($_POST["password"])) self::redirect("login", "?error=empty_fields");
			unset($_POST["submit"]);
			if($app->login($_POST["email"], $_POST["password"]) != false){
				self::redirect("admin");
			} else {
				self::redirect("?error=credentials_mismatch");
			}
			return false;
		}
		$this->display("login");
	}
}