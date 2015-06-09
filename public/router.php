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
define("zsub", "");


/*PHYSICAL PATH TO ZEDEK CORE*/
/*----------------------------*/
/*
enter path to anchor file 
on windows the path will have the structure
c:\\path\\to\\anchor.php
*/
$anchor = "/path/to/zedek/anchor.php";

if(file_exists($anchor)){
	require_once $anchor;	
} else {
	print "<style>code{border-radius: 4px; text-shadow: 1px 1px 1px #fefefe; display: block; padding: 4px; margin:4px; color: maroon; border: solid 1px #aaa; width: 400px; background-color: #eee}</style>";
	print "<pre><h1>Zedek Framework</h1>Set a valid path on <i>line 40 of router.php</i> to the <b>anchor.php</b> file in the zedek directory example: <code>/home/my_name/zedekbackend/anchor.php</code> or on a windows machine <code>C:\\\\zedekbackend\\\\anchor.php</code><br>Set values for <b>zsub</b> to your web server folder with a trailing slash eg: <code>/subfolder/</code> on <i>line 30</i> where the your public html contents are in a web sub folder same for windows and unix/unix like machines.<br><br>*Also ensure you have write permissions on the zedek folder.</pre>";
	exit;	
}
