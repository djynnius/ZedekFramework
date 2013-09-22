<?php
#anchor file;
namespace __zf__;
session_start();

#application root /path/to/zedek/ 
const zroot = "/var/djynnius/projects/zedek2.0.1/";
const zweb = "/var/www/";
const zsubpath = "work";

#set include path
$os = strtolower($_SERVER['SERVER_SOFTWARE']);
$zedekCorePath = strpos($os, "win32") ? ".;"	.zroot."core" : ":.:".zroot."core";
ini_set('include_path', $zedekCorePath);

#Error reporting - On for development and production
#ini_set('display_errors', "Off");

#main zedek controler
require_once "zedek.php";
require_once "uri.maper.php";
require_once "controler.php";
require_once "orm.php";
require_once "config.php";

Z::importLibs();
#instantiate uri maper 
$uri = new URIMaper;

if(file_exists(zroot."engines/{$uri->controler}/controler.php")){
	$uri->import($uri->controler);
} else {
	$uri->import();
}

#instantiating controler
$controler = new CControler;
$method = $uri->method;

#using controler as method for default in cases where there is no method url mapping
$controler_method = $uri->controler; 

if(method_exists($controler, $method)){
	$controler->$method();
} elseif(method_exists($controler, $controler_method)){
	$controler->$controler_method();
} else {	
	try{
		$controler->method();
	}catch(Exception $e){
		$controler->_default();
	}
}

?>
