<?php
namespace __zf__;
require_once "initializer.php";

#instantiate uri maper 
$uri = new URIMaper;
if(file_exists(zroot."engines/{$uri->class}/model.php")){
	$uri->import($uri->class);
} else {
	$uri->import();
}

#instantiating model class
$class = new CModel;
$method = $uri->method;

#using class as method for default in cases where there is no method url mapping
$class_method = $uri->class; 

if(method_exists($class, $method)){
	$class->$method();
} elseif(method_exists($class, $class_method)){
	$class->$class_method();
} else {	
	try{
		$class->method();
	}catch(Exception $e){
		$class->_default();
	}
}
?>