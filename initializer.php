<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 4
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
Z::required("sites");

Z::importInternals();
Z::importModels();

$config = new ZConfig;
ini_set('display_errors', $config->get("error"));