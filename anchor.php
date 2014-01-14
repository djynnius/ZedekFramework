<?php
namespace __zf__;
require_once "initializer.php";

#instantiate uri maper 
$uri = new URIMaper;
if(file_exists(zroot."engines/{$uri->controller}/controller.php")){
	Z::import($uri->controller);
} else {
	Z::import();
}

#instantiating model class
$controller = new CController;
$method = $uri->method;

#using class as method for default in cases where there is no method url mapping
$class_method = $uri->controller; 

if(method_exists($controller, $method)){
	$controller->$method();
} elseif(method_exists($controller, $class_method)){
	$controller->$class_method();
} else {	
	try{
		$controller->method();
	}catch(Exception $e){
		$controller->_default();
	}
}