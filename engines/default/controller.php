<?php
/**
* @package Zedek Framework
* @version 5
* @subpackage ZConfig zedek configuration class
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;
class CController extends ZController{
	
	/**
	* uri maps to controller/method/id 
	* the respective strings may be matched by $this->uri->controller, $this->uri->method and $this->uri->id
	* 
	* RENDERING VIEWS
	* The views may be accessed by self::render(), self::zrender(), self::display() or self::dynamiic()
	*
	* self::render('default.index') renders view with Jinja2 like templating using Twig . view htmis accessed with dot notation - engine.view eg default (in engines) and index (pointing to engines/default/views/index.html)
	* self::zrender() returns view with themeing using the zedek templating engine -- it is not advised and may soon be depricated
	* self::display() returns view with no themeing information and uses the zedek templating engine --  it sis not advised and may soon be depricated
	* self::dynamic() renders a dynamic php view
	*
	* REDIRECTS
	* redirects are managed with the self::redirect();  method which may take no argument and redirects to the / route
	* the self::redirect(0);  redirects on the current page with a get request
	* the self::redirect(-1);  redirects to the referring page
	* the self::redirect("controller");  redirects to the the route /controller
	* the self::redirect("controller", "method");  redirects to the the route /controller/method
	* the self::redirect("controller", "method", "id");  redirects to the the route /controller/method/id
	* the self::redirect("?key=val");  redirects to the the route /?key=val
	* similarly the self::redirect("controller", "?key=val");  redirects to the the route /controller/?key=val
	* similarly the self::redirect("controller", "method", "?key=val");  redirects to the the route /controller/method/?key=val
	* similarly the self::redirect("controller", "method", "id/?key=val");  redirects to the the route /controller/method/id/?key=val
	*
	* ROUTING
	* routes are managed with a routes.php file for each engine
	* example: $route['^/clean$'] = "default/unclean/";
	* this allows for the url /clean to access the method unclean of default controller (/engines/default/controller.php)";
	* example: $route['^/search/flights/(?P<origin>[a-z]+)/(?P<destination>[a-z]+)$'] = "default/foo/";
	* example: accepts regular expression the example above allows the function ro receive an array $args with the keys origin and destination which may be accessed within the foo method of the default controller;
	*/

	function index(){
		if(phpversion() < 5.6){
			self::display("index@ztheme");
		} else{
			self::render("default.index", []);	
		}
	}

	/**
	* route managed: 
	* Try accessing /@yourname 
	* Try accessing /foo 
	*/
	function foo($args=[]){
		$args['bar'] = isset($args['bar']) ? $args['bar'] : "Anonymous";
		self::render('default.test', ['name'=>$args['bar']]);
	}

	/**
	* Non route manages
	* view /bar
	*/
	function bar(){
		self::render("default.bar", ['message'=>"It works!"]);
	}

}