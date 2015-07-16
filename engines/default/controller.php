<?php
/**
* @package Zedek Framework
* @subpackage ZConfig zedek configuration class
* @version 3
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
	* The views may be accessed by self::render(), self::display() or self::dynamiic()
	*
	* self::render() renders view with themeing information
	* self::display() returns view with no themeing information
	* self::dynamic() renders a dynamic php view with themeing information
	*
	* rendering methods take arguments of view, templating array and theme, the view may also be view@controller
	* Example 1: self::render("index", ['foo'=>"bar"]);
	* Example 2: self::render("index@other_engine", ['foo'=>"bar"]);
	* Example 3: self::render(['foo'=>"bar"], "index@other_engine"); the order of the first 2 arguments may be reversed
	* Example 3: self::render(['foo'=>"bar"], "index@other_engine", "admin"); to change the themeing for a particular page presentation
	*
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
	*/

	function index(){
		self::display("index@ztheme");
	}
}