<?php

session_start();

# application root constant which must be set to the app non web path
const zroot = "/media/ubuntu/zedek/";

#main zedek controler
require_once zroot."core/zedek.php";
Z::import("uri.maper");
Z::import("controler"); //ensures that the included controler class extends the core controler
Z::import("orm"); //ensures the orm class is included

#instantiate uri maper 
$uri = new URIMaper();

try{
	if(file_exists(zroot."engines/{$uri->controler}/controler.php")){
		$uri->import($uri->controler);
	} else {
		$uri->import();
		throw new Exception("Engine does not exist");
	}
} catch(Exception $e){//echo $e->getMessage();
}
#instantiating controler

$controler = @new CControler();

#seting method
$method = $uri->method;

#running method
$controler_method = $uri->controler; //using contolrer as method for default in cases where there is no method url mapping
try{
	if(method_exists($controler, $method)){
		$controler->$method();
	} elseif(method_exists($controler, $controler_method)){
		$controler->$controler_method();
	} else {	
		$controler->_default();
		throw new Exception("The method does not exist for the class {$uri->controler}");
	}
} catch(Exception $e){//echo $e->getMessage();
}


?>