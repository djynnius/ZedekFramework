<?php
/**
* @package Zedek Framework
* @version 5
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/
namespace __zf__;

//Session management
#session_name('zedekframework'); //set the globlal session name here
//for sub domain session management
#ini_set('session.cookie_domain', '.zedekframework.com'); 
//set path to save session
#ini_set('session.save_path', __dir__."/sessions"); 

#explicitly start session
session_start();

#ini settings

#set include path
define("zroot", __dir__."/");
set_include_path(get_include_path() . PATH_SEPARATOR . zroot);

#Error reporting - On for development and production

#main zedek classes
require_once zroot."core/zedek.php";

Z::required("uri");
Z::required("controller");
Z::required("orm");
Z::required("config");
Z::required("alias");

Z::importInternals();
Z::importModels();

$config = new ZConfig;
ini_set('display_errors', $config->get("error")); //off out the box - preferred for production
ini_set('log_errors', $config->get("log_errors")); //on out the box - may be turned on in development
ini_set('error_log', zroot."errors/errors.log"); //You may wish to specify another path

if($config->get("templating")->engine == "twig"){
	Z::required("twig");
}