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

class ZAlias{
	public static function getRoutes(){
		$engine_dir = scandir(zroot."engines");
		$engines = [];
		foreach($engine_dir as $e){
			if($e[0] != '.' && is_dir(zroot."engines/".$e)) $engines[] = $e;
		}

		$routes = [];

		foreach($engines as $engine){
			require_once zroot."engines/".$engine."/routes.php";
			$routes = array_merge($routes, $route);
		}

		return $routes;	
	}

	public static function aliasRoute($routes){
		foreach($routes as $i=>$uri){
			preg_match("`".$i."`", $_SERVER['REQUEST_URI'], $parts);
			if(count($parts) > 0){
				$uri = explode('/', $uri);
				$controller = $uri[0];
				$method = $uri[1];
				$args = [];
				foreach($parts as $j=>$v){
					if(gettype($j) == 'integer'){
						unset($parts[$j]);
					}
				}
				$args = $parts;
				$vals = array_values($args);
				$s = join(", ", $vals);
				
				Z::import($controller);
				$controller = new CController;
				$controller->$method($args);
				exit;
			}

		}

	}
}





