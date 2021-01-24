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

require_once phpversion() < 7 ? zroot."core/twig/v1.34.4/autoload.php" : zroot."core/twig/autoload.php";
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

	static function render($view, $dict){
		$dict = (array)$dict;
		$in = Z::template();
		$dict = array_merge($in, $dict);
		$uri = new ZURI;
		$split = explode(".", $view);

		if(empty($view)){
				return self::jinja()->render('404.html', $dict);
		}

		switch($view){
			case is_file(zroot."engines/{$view}"):
				break;
			case is_file(zroot."engines/{$uri->controller}/views/{$view}.html"):
				$view = "{$uri->controller}/views/{$view}.html";
				break;
			case count($split) == 2 && is_file(zroot."engines/{$split[0]}/views/{$split[1]}.html"):
				$view = "{$split[0]}/views/{$split[1]}.html";
				break;
			default:
				return self::jinja()->render('404.html', $dict);
		}

			return self::jinja()->render($view, $dict);
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
		if($path == "engines"){
			$html = explode(".", $html);
			$html[0] = $html[0]."/views";
			$html = join($html, "/");
			$html = $html.".html";
			return is_file(zroot.$path."/".$html) ? $html : "404.html";
		} else {
			return is_file(zroot.$path."/".$html) ? $html : "404.html";
		}


	}
}
