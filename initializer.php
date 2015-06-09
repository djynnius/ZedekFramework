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
#ini_set('session.save_path', "/path/to/zedek/sessions"); 

#explicitly start session
session_start();


#set include path
define("zroot", __dir__."/");
$os = strtolower(@$_SERVER['SERVER_SOFTWARE']);
$zedek_core_path = strpos($os, "win") ? ".;".zroot."core" : ":.:".zroot."core";
ini_set('include_path', $zedek_core_path);

#Error reporting - On for development and production

#main zedek classes
require_once "zedek.php";
require_once "uri.php";
require_once "controller.php";
require_once "orm.php";
require_once "config.php";
require_once "sites.php";

Z::importInternals();
Z::importModels();

$config = new ZConfig;
ini_set('display_errors', $config->get("error"));