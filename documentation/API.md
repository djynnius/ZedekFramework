Zedek API

==Zedek==
abstract class from which all other clases extend

===Methods===
import($module = "default") static function will require

isUser() checks if zedek user role is set

isAdmin() checks if zedek user role is set to admin

==Z==
===Methods===
importLibs($type = false) static function requires all files in /zedek/libs/ folder


==ZConfig==
Manipulates the /zedek/config/config.json file

===Attributes===
$file public attribute

$config public attribute

===Methods===
get($key) public function returns the config value

set($key, $value) public function sets the config value

remove($key) public function destroys the config value


==URIMaper==

===Attributes===

$url public 
$controler controler portion of url
$method method portio of url
$gets any part of a url after a ? 
$args = array() returns array of get request


==ZControler==

abstract class extends Zedek implements ZIControler

===Methods===
_init() replaces construct for all controlers

template($arg1=null, $arg2=null) takes 2 arguments in no specific order - view file, and placeholder array

importApp($controler = false) allows access to models other than those of the current controler

_default() sets default view

denyGuest() redirects guests from secure/user pages

==ZView==

$template 
$view 
$theme current theme
$header header file in /zedek/themes/current_theme/
$footer footer file in /zedek/themes/current_theme/
$style style file in /zedek/themes/current_theme/
$script script file in /zedek/themes/current_theme/

===Methods===

template() appliation wide placeholders

render() renders the view

styleAndScripts() sets placeholders for styles and scripts

getExternalScript($file) returns external scripts (js files) housed within the /zedek/libs/external_packages/ folder

getTheme() returns theme set in /zedek/config/config.json

setTheme($theme) sets theme in /zedek/config/config.json


==ZORM==
Object Relaional Mapper

===Attriutes===

$dbo returns PDO
$host host set in /zedek/config/d.json
$user username set in /zedek/config/d.json
$pass password set in /zedek/config/d.json
$engine database engine set in /zedek/config/d.json
$db database name set in /zedek/config/d.json
$prefix table prefix set in /zedek/config/d.json


===Methods===
table($table=false, $attrs=false) returns ZORMTable Object

fetch($q) returns all from the select query passed as an array

write($q) for updates and inserts

delete($q, $table=false) for deletes


==ZORMTable==

===Attributes===
$dbo returns PDO
$table table name

===Methods===

create($table, $attrs) creates a new table and takes argument of an array for description of table

fetch() returns all entries in the table

add($a) adds new entry to table

remove($val, $col="id") removes entry from table

update($a=array(), $val='*', $col="id") updates table

row($val, $col='id') returns ZORMRow object

drop() drops table

size() returns number of rows in table



==ZORMRow==

===Attributes===

$dbo returns PDO
$table table name
$column column
$value value


===Methods===

commit() writes row changes to database

remove() removes this row


==ZApp== 
abstract class extends ZORM implements ZIApp{

_init()