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
	print "<style>code{border-radius: 4px; text-shadow: 1px 1px 1px #fefefe; display: block; padding: 4px; margin:4px; color: maroon; border: solid 1px #aaa; width: 400px; background-color: #eee}</style>";
	print "<pre><h1>Zedek Framework</h1>Set a valid path on <i>line 8 of router.php</i> to the <b>anchor.php</b> file in the zedek directory example: <code>/home/my_name/zedekbackend/anchor.php</code> or on a windows machine <code>C:\\\\zedekbackend\\\\anchor.php</code>Also ensure you have write permissions on the zedek folder.</pre>";
	exit;	
}
