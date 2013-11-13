<?php
#controller file;
namespace __zf__;
session_start();

#application constants for doc root, web root and sub folder path 
#This is OS specific windows will be "C:\\path\\to\\root\\" 
#"C:\\path\\to\\web\\" and "/subfolder/" for both unix and windows 
#This is OS specific windows will be "C:\\path\\to\\root\\" 
const zroot = "/path/to/zedek/root/";
const zweb = "/path/to/web/";
const zsub = "";

#set include path
$os = strtolower(@$_SERVER['SERVER_SOFTWARE']);
$zedek_core_path = strpos($os, "win") ? ".;"	.zroot."core" : ":.:".zroot."core";
ini_set('include_path', $zedek_core_path);

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
