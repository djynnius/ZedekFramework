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

#application constants for doc root, web root and sub folder path 
#This is OS specific windows will be "C:\\path\\to\\root\\" 
#"C:\\path\\to\\web\\" and "/subfolder/" for both unix and windows 
#This is OS specific windows will be "C:\\path\\to\\root\\" 

$global_conf = __dir__."/config/global.conf";
$global_conf = file_get_contents($global_conf);
$global_conf = json_decode($global_conf);

$foo = $bar = 1;
$style = "<style>code{display: block; padding: 4px; margin:4px; color: maroon; border: solid 1px #aaa; width: 400px; background-color: #eee}</style>"."<pre><h1>Zedek Framework</h1>";

switch($foo){
	case !is_dir($global_conf->web_document_root):
		print $style;
		print "1.) Set <i>config/global.conf</i> values for <b>web_document_root</b> to your web server folder with trailing slash eg: <code>/var/www/</code> or on windows <code>C:\\\\wamp\\\\www\\\\</code>\r\n\r\n";
		$bar++;
	case !file_exists($global_conf->web_sub_folder."themes/common/z.chk"):
			if($bar == 1){
				print $style;
			}
			print "{$bar}.) Set <i>config/global.conf</i> values for <b>web_sub_folder</b> to your web server folder with a trailing slash eg: <code>/subfolder/</code> where the your public html contents are in a web sub folder same for windows and unix/unix like machines.";
			print "</pre>";
			exit;			
		break;
	default:
		null;
}

define("zweb", $global_conf->web_document_root);

/*if installing zedek in a web sub directory
ensure you set the path starting with a slash and ending in a trailing slash 
eg: "/sub/folder/""
*/
define("zsub", $global_conf->web_sub_folder); 

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