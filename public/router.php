<?php
/*
enter path to anchor file 
on windows the path will have the structure
c:\\path\\to\\anchor.php
*/

$anchor = "/path/to/zedek/anchor.php";

if(file_exists($anchor)){
	require_once $anchor;	
} else {
	print "<style>code{display: block; padding: 4px; margin:4px; color: maroon; border: solid 1px #aaa; width: 400px; background-color: #eee}</style>";
	print "<pre>Set a valid path to the <b>anchor.php</b> file in the zedek directory eg: <code>/home/my_name/zedekbackend/anchor.php</code> or on a windows machine <code>C:\\\\zedekbackend\\\\anchor.php</code></pre>";
	exit;	
}

