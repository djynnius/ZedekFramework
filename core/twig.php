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
use \Twig_Loader_Filesystem as JinijaTemplateLoader;
use \Twig_Environment as JinjaCache;

require_once zroot."core/twig/autoload.php";
require_once zroot."core/view.php";
$loader = new \Twig_Loader_Filesystem(zroot."templates");

class ZTwig extends Zedek{

	static function setTemplatePath(){
		$config = new ZConfig;
		$path = $config->get("templating")->path;
		$path = rtrim($path, "/");
		$path = zroot.$path;
		return new JinijaTemplateLoader($path);
	}

	static function jinja(){
		$jinjaTemplates = self::setTemplatePath();
		$twig = new JinjaCache($jinjaTemplates, [
		    'cache' => zroot.'cache',
		    'auto_reload' => true, 
		]);
		return $twig;
	}

	static function render($arg1=false, $arg2=[]){
		$args = self::checkValidJinia($arg1, $arg2);

		$html = isset($args[0]) ? $args[0] : "404.html";
		$html = self::replace404($html);
		
		$dict = isset($args[1]) ? $args[1] : [];
		$tmp = new ZView();
		$dict = array_merge($dict, $tmp->template());
		
		$tmp = new ZConfig("tpl");
		$tmp = (array)$tmp->config;
		$dict = array_merge($dict, $tmp);

		return count($args) == 0 ? false : self::jinja()->render($html, $dict);
	}

	static private function checkValidJinia($arg1, $arg2){
		if(gettype($arg1) == "string" && gettype($arg2) == "array"){
			$html = $arg1;
			$dict = $arg2;
			return [$html, $dict];
		} elseif(gettype($arg1) == "array" && gettype($arg12) == "string"){
			$html = $arg2;
			$dict = $arg1;
			return [$html, $dict];
		} else {
			return [];
		}
	}

	static private function replace404($html){
		$config = new ZConfig;
		$path = $config->get("templating")->path;
		$path = rtrim($path, "/");
		return is_file(zroot.$path."/".$html) ? $html : "404.html";
	}
}

