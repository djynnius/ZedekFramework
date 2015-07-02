<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 3
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
$os = strtolower(@$_SERVER['SERVER_SOFTWARE']);
$zedek_core_path = strpos($os, "win") ? ".;".zroot."core" : ":.:".zroot."core";
ini_set('include_path', $zedek_core_path);

#Error reporting - On for development and production

#main zedek classes
require_once zroot."core/zedek.php";
require_once zroot."core/uri.php";
require_once zroot."core/controller.php";
require_once zroot."core/orm.php";
require_once zroot."core/config.php";
require_once zroot."core/sites.php";

Z::importInternals();
Z::importModels();

$config = new ZConfig;
ini_set('display_errors', $config->get("error"));