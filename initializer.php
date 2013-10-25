<?php
#controller file;
namespace __zf__;
session_start();

/*
chmod database/ rw
chmod config/ rw
chmod test/ rwx
chmod public/ r
*/

#application root /path/to/zedek/ 
const zroot = "/path/to/zedek/root";
const zweb = "/path/to/web/";

#set include path
$os = strtolower(@$_SERVER['SERVER_SOFTWARE']);
$zedekCorePath = strpos($os, "win") ? ".;"	.zroot."core" : ":.:".zroot."core";
ini_set('include_path', $zedekCorePath);

#Error reporting - On for development and production


#main zedek classes
require_once "zedek.php";
require_once "uri.maper.php";
require_once "model.php";
require_once "orm.php";
require_once "config.php";

Z::importLibs();

$config = new ZConfig;
ini_set('display_errors', $config->get("error"));
?>