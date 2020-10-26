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
class ZView extends Zedek{

	public $template;
	public $view; //current view file
	public $theme; //theme folder name
	public $folder; //path to theme folder
	public $header;
	public $footer;
	public $configFile; //tpl.conf file path
	public $uri; //URIMaper Object

	/**
	* @param mixed $arg1
	* @param mixed $arg2
	* @param string $theme theme to render
	*/
	function __construct($arg1=null, $arg2=null, $theme=false){
		$this->theme = $this->getTheme($theme) != false && $this->getTheme($theme) != null ? $this->getTheme($theme) : "default";

		$fix = $this->fixArgs($arg1, $arg2);
		$this->template = $fix['template'];
		$this->view = $fix['view'];


		$this->folder = zweb."themes/{$this->theme}/";
		$this->setThemeFiles();
		$this->configFile = zroot."config/tpl.conf";
		$this->uri = new ZURI;
	}

	#cleans up arguments
	/**
	* @param mixed $arg1 
	* @param mixed $arg1 
	* @return arrray with indexes for template and view 
	*/
	private function fixArgs($arg1, $arg2){
		$args = func_get_args();
		$template = $this->template();
		$out = array('template'=>$template, 'view'=>false);
		foreach($args as $item){
			if(gettype($item) == "array"){
				//merge order allows for overwriting
				$out['template'] = array_merge($template, $item); 
			} elseif(gettype($item) == "string") {
				$out['view'] = (string)$item;
			} else {
				continue;
			}
		}
		return $out;
	}

	/**
	* Sets $this->header and $this->footer
	* @return all theme html files
	*/
	private function setThemeFiles(){
		$themeFolder = zweb."themes/";
		$this->theme = is_dir(zweb."themes/".$this->theme) ? $this->theme : "default";
		$files = scandir($themeFolder.$this->theme);
		$this->header = file_exists(zweb."themes/{$this->theme}/header.html") ? 
			file_get_contents(zweb."themes/{$this->theme}/header.html") : "";
		$this->footer = file_exists(zweb."themes/{$this->theme}/footer.html") ? 
			file_get_contents(zweb."themes/{$this->theme}/footer.html") : "";
	}

	#returns default template
	/**
	* @return array basic templating which may be overwritten
	*/
	public function template(){
		$config = new ZConfig;
		$uri = new ZURI;

		$global = new ZConfig("global");
		$version = new ZConfig("version");

		$a = array(
			'app'=>$global->get("app"), 
			'controller'=>is_null($uri->controller) ? "" : $uri->controller, 
			'method'=>is_null($uri->method) ? "" : $uri->method, 
			'footer'=>"Zedek Framework. Version".$config->get("version"), 
			'version'=> $version->get("version"), 
			'sub_version'=> "Zedek Framework " . $version->get("sub_version"), 
			'dir'=> $uri->dir, 
			'theme'=> $uri->dir."/themes/".$this->theme, 
			'common'=> $uri->dir."/themes/common", 
			'this_year'=> strftime("%Y", time()), 
			'this_month'=> strftime("%B", time()), 
			'today'=> strftime("%A, %B %d, %Y", time()), 
		);
		$b = $this->configTemplate();
		$a = array_merge($a, $b);
		return $a;
	}

	/**
	* @return simple templating resident in template.config
	*/
	public function configTemplate(){
		$file = zroot."config/tpl.conf";
		$json = file_get_contents($file);
		$pseudoArray = json_decode($json);
		$array = (array)$pseudoArray;
		return $array;
	}

	/**
	* @return string html to output on page
	*/
	public function display($view = false){
		$view = $view == false ? $this->getValidView() : $view;
		
		$view = self::zvEOL($view);
		$view = self::zvtemplate($view);

		$view = self::zvif($view);
		$view = self::zvfor($view);
		$view = self::zvforeach($view);
				
		return $view;		
	}

	/**
	* @return string html to output on page: themed
	*/	
	public function render(){
		$view = "";
		$view .= self::display($this->header);
		$view .= self::display();
		$view .= self::display($this->footer);
		return $view;
	}

	/**
	* @return string html from php to output on page
	*/	
	public function dynamic($controller=false){
		$controller = empty($this->uri->controller) || is_null($this->uri->controller) ? "default" : $this->uri->controller;		
		$view = empty($this->uri->method) || is_null($this->uri->method) ? "index" : $this->uri->method;
		$view = is_string($this->view) ? $this->view : $view;
		
		/*Allows for calling of templating information using the $self->key to return value*/
		$self = new \stdClass();
		foreach($this->template as $i=>$var){
			$self->$i = $var; 
		}

		$complex_view = explode("@", $view);
		if(count($complex_view) == 1 && file_exists(zroot."engines/{$controller}/views/{$view}.php")){
			@include_once zroot."engines/{$controller}/views/{$view}.php";
		} elseif(count($complex_view) == 2 && file_exists(zroot."engines/{$complex_view[1]}/views/{$complex_view[0]}.php")){
			@include_once zroot."engines/{$complex_view[1]}/views/{$complex_view[0]}.php";
		} else {

		}
	}

	/**
	* @return string view html
	*/	
	public function getValidView(){
		$controller = $this->uri->controller == "" ? "default" : $this->uri->controller;
		$method = $this->uri->method;

		if(strpos($this->view, "@") != false){
			$split = explode("@", $this->view);
			$controller = trim($split[1]);
			$view = trim($split[0]);
		} else {
			$view = empty($this->view) ? $method : $this->view;
		}

		$engine = zroot."engines/";;
		$viewFile = $controller == "ztheme" ? 
			zweb."themes/{$this->theme}/{$view}.html" : 
			$engine."{$controller}/views/{$view}.html";

		if(file_exists($viewFile)){
			$view = file_get_contents($viewFile);	
		} elseif(file_exists($engine."{$controller}/views/{$method}.html")){
			$view = file_get_contents($engine."{$controller}/views/{$method}.html");
		} elseif(file_exists($engine."{$controller}/views/none.html")){
			$view = file_get_contents($engine."{$controller}/views/none.html");
		} elseif(file_exists($engine."/default/views/{$this->view}.html")){
			$view = file_get_contents($engine."/default/views/{$this->view}.html");
		} elseif(file_exists(zroot."engines/default/views/none.html")){
			$view = file_get_contents(zroot."engines/default/views/none.html");
		} else {
			$view = "";
		}
		return $view;
	}

	/**
	* @return string theme
	*/	
	public function getTheme($theme=false){
		if($theme != false){
			$this->theme = $theme;
			return $theme;
		};
		$conf = new ZConfig;
		$theme =  $conf->get("theme");
		return (file_exists(zweb."themes/".$theme."/")) ? $theme : "default";
	}

	/**
	* @param string sets theme in config.json
	*/	
	private function setTheme($theme){
		$conf = new ZConfig;
		$conf->set("theme", $theme);
	}

	function zvEOL($view){
		$view = str_replace(PHP_EOL, "", $view);
		$view = str_replace(array("\n","\r"), "", $view);		
		return $view;
	}

	function zvtemplate($view){
		global $__zf__core__view__template;
		$__zf__core__view__template = $this->template;

		$re = "/{{\s?([a-zA-Z0-9_-]+)\s?}}/";
		
		$view = preg_replace_callback(
			$re, 
			function($m){ 
				global $__zf__core__view__template;
				$tmp = $__zf__core__view__template;
				$ak = array_keys($tmp);
				return in_array("{$m[1]}", $ak) && gettype($tmp["{$m[1]}"]) == "string" ?  $tmp["{$m[1]}"] : $m[0]; 				
			}, 
			$view);	
		return $view;
	}

	function zvif($view){
		global $__zf__core__view__template;
		$__zf__core__view__template = $tmp = $this->template;
		$re = "/{%if\s+\[([a-zA-Z0-9_-]+)\]\s+\=\=\s+([a-zA-Z0-9._-]+)\s+\?\s+([a-zA-Z0-9#_.\s!*%><\/=\"-]+)\s+\:\s+([a-zA-Z0-9#_.\s!*%><\/=\"-]+)\s+%}/";
		preg_match_all($re, $view, $a);

		$out = "";
		$ak = array_keys($tmp);
		foreach($a[0] as $i=>$s){			
			if(in_array($a[1][$i], $ak) && $tmp[$a[1][$i]] == $a[2][$i]){
				$out = $a[3][$i];
			} else {
				$out = $a[4][$i];
			}						
			$view = str_replace($s, $out, $view);	
		}
		return $view;
	}

	/**
	* for simple lists not matrices
	*/
	function zvfor($view){ 
		global $__zf__core__view__template;
		$__zf__core__view__template = $tmp = $this->template;
		$re = "/{%for\s+([a-zA-Z0-9_-]+)\s+in\s+([a-zA-Z0-9_-]+)\s+\:\s+([^%]+)\s+%}/";
		preg_match_all($re, $view, $all);

		$ak = array_keys($tmp);

		foreach($all[0] as $j=>$code){
			if(in_array($all[2][$j], $ak)){
				$items = $tmp[$all[2][$j]];
				$o = "";
				foreach($items as $item){
					$_re = "/{{\s*(".$all[1][$j].")\s*}}/";
					$o .= preg_replace($_re, $item, $all[3][$j]);
				}
				$view = str_replace($code, $o, $view);
			} else {
				$view = str_replace($code, "{{for loop}}", $view);
			}
		}

		return $view;
	}

	function zvforeach($view){
		global $__zf__core__view__template;
		$__zf__core__view__template = $tmp = $this->template;

		$re = "/{%foreach\s+([a-zA-Z0-9_-]+)\s+as\s+([a-z]+)\s+\:\s+([^%]+)\s+%}/";
		preg_match_all($re, $view, $all);

		$tkeys = array_keys($tmp);

		foreach($all[1] as $j=>$items){
			if(in_array($items, $tkeys) && gettype($tmp[$items]) == "array" ){
				$o = "";
				foreach($tmp[$items] as $item){
					global $__zf__zv__tmp;
					$_tmp = $__zf__zv__tmp = $item;
					$_re = "/{{\s*".$all[2][$j]."\.([a-zA-Z0-9_-]+)\s*}}/";	
					$rep = "$1";
					//$o .= preg_replace($_re, $_tmp["$1"] , $all[3][$j]);
					$o .= preg_replace_callback($_re, function($m) {
						global $__zf__zv__tmp;
						$_tmp = $__zf__zv__tmp;
						return isset($_tmp[$m[1]]) ? $_tmp[$m[1]] : "";
					} , $all[3][$j]);
				}
				$view = str_replace($all[0][$j], $o, $view);
			} else {
				$view = str_replace($all[0][$j], "{{data loop}}", $view);
			}
		}

		return $view;
	}
}