<?php
namespace __zf__;

class CController extends ZController {

	/**
	*Ensures parent is extendned properly and headers are set
	*/
	function __construct(){

		parent::__construct();
		
		header('Access-Control-Allow-Origin: *'); 
		header("Content-type: text/json");		
	}

	/**
	*Meant to handle all calls and reoute them straight to their respective models
	*/
	function __call($obj, $mthd){
		if(!method_exists($this, $this->uri->method)){
			$mthd = $this->uri->method;
			self::getData($mthd);
		}
	}

	/**
	*@param String $call the API call
	*/
	function getData($call){
		#self::ensurePost();
		$data = method_exists($this->app, $call) ? $this->app->$call($call) : [['response'=>0, 'type'=>"Plain"]];
		print json_encode($data, JSON_PRETTY_PRINT);
	}

	function getDataTablesJSON(){
		$data = [['response'=>0, 'type'=>"DataTables"]];
		print "{";
		print '"data": ';
		print json_encode($data, JSON_PRETTY_PRINT);
		print "}";
	}

	/**
	*Intended to ensure only post requests are responded to
	*@return Boolean False or redirect
	*/
	function ensurePost(){
		return _Form::posted() ? False : self::redirect(-1);
	}

}