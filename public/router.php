<?php

/**
* @package Zedek Framework
* @version 3.0
* @author djyninus <psilent@gmail.com>
*/


/*WEB  PHYSICAL PATH*/
/*-----------------------*/
/*
application constants for doc root, web root and sub folder path 
This is OS specific windows will be "C:\\path\\to\\root\\" 
"C:\\path\\to\\web\\" and "/subfolder/" for both unix and windows 
This is OS specific windows will be "C:\\path\\to\\root\\"
*/ 
define("zweb", __dir__."/");




/*WEB  SUB FOLDER*/
/*-----------------------*/
/*
if installing zedek in a web sub directory
ensure you set the path starting with a slash and ending in a trailing slash 
eg: "/sub/folder/""
*/

$uri = explode("/", $_SERVER["REQUEST_URI"]);
$base = trim($uri[1], "/");
if(empty($base)){
	$subdir = "";
} else {
	$sub = explode($base, __file__);
	$sub = str_replace("router.php", "", $sub[1]);	
	$subdir[] = $base;
	$subdir[] = trim($sub, "/");
	$subdir = join("/", $subdir);
	$subdir =  "/".$subdir."/";	
}


define("zsub", $subdir); /*You may override the subfolder by explicitly setting it*/


/*PHYSICAL PATH TO ZEDEK CORE*/
/*----------------------------*/
/*
enter path to anchor file 
on windows the path will have the structure
c:\\path\\to\\anchor.php
*/
$anchor = "zedek/anchor.php";

if(file_exists($anchor)){
	require_once $anchor;	
} else {
	print "<style>code{border-radius: 4px; text-shadow: 1px 1px 1px #fefefe; display: block; padding: 4px; margin:4px; color: maroon; border: solid 1px #aaa; width: 400px; background-color: #eee}</style>";
	print "<pre><h1>Zedek Framework</h1>Set a valid path on <i>line 40 of router.php</i> to the <b>anchor.php</b> file in the zedek directory example: <code>/home/my_name/zedekbackend/anchor.php</code> or on a windows machine <code>C:\\\\zedekbackend\\\\anchor.php</code><br>*Also ensure you have write permissions on the zedek folder.</pre>";
	exit;	
}
