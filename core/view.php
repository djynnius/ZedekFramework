<?php
/**
* @package Zedek Framework
* @subpackage ZView zedek themeing engine
* @version 3
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
	public $configFile; //template.json file path
	public $uri; //URIMaper Object

	/**
	* @param mixed $arg1
	* @param mixed $arg2
	* @param string $theme theme to render
	*/
	function __construct($arg1=null, $arg2=null, $theme=false){
		$fix = $this->fixArgs($arg1, $arg2);
		$this->template = $fix['template'];
		$this->view = $fix['view'];

		$this->theme = $this->getTheme($theme) != false && $this->getTheme($theme) != null ? $this->getTheme($theme) : "default";

		$this->folder = zweb."themes/{$this->theme}/";
		$this->getAllThemeFiles();
		$this->configFile = zroot."config/template.json";
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

	#pulls theme files
	/**
	* @param string $file 
	* @param string $type 
	* @return string file contents 
	*/
	private function getThemeFile($file, $type="html"){
		$puts = file_exists(zweb."themes/{$this->theme}/{$file}.{$type}") ? 
					@file_get_contents(zweb."themes/{$this->theme}/{$file}.{$type}") : 
					(file_exists(zweb."themes/default/{$file}.{$type}") ? 
						file_get_contents(zweb."themes/default/{$file}.{$type}") : 
						""
					);		
		return $puts;
	}

	/**
	* Sets $this->header and $this->footer
	* @return all theme html files
	*/
	private function getAllThemeFiles(){
		$themeFolder = zweb."themes/";
		$files = scandir($themeFolder.$this->theme);

		if(gettype($files) != "array") $files = array();
		foreach($files as $file){
			if(!is_dir($themeFolder.$file)){
				$info = pathinfo($themeFolder.$file);
				$this->$info['filename'] = $this->getThemeFile($info['filename'], @$info['extension']);
			}
		}
	}

	#returns default template
	/**
	* @return array basic templating which may be overwritten
	*/
	private function template(){
		$config = new ZConfig;
		$uri = new ZURI;
		$a = array(
			'app'=>"Zedek Framework", 
			'controller'=>is_null($uri->controller) ? "" : $uri->controller, 
			'method'=>is_null($uri->method) ? "" : $uri->method, 
			'footer'=>"Zedek Framework. Version".$config->get("version"), 
			'version'=> $config->get("version"), 
			'dir'=> $uri->dir, 
			'theme'=> $uri->dir."/themes/".$this->getTheme(), 
			'common'=> $uri->dir."/themes/common", 
			'this year'=> strftime("%Y", time()), 
			'this month'=> strftime("%B", time()), 
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
		$file = zroot."config/template.json";
		$json = file_get_contents($file);
		$pseudoArray = json_decode($json);
		$array = (array)$pseudoArray;
		return $array;
	}

	/**
	* @return string html to output on page
	*/
	public function display(){
		$view = $this->getValidView();

		foreach($this->template as $k=>$v){
			if(is_string($v)){
				$view = str_replace("{{".$k."}}", "$v", $view);
			} elseif(empty($v)){
				$view =  str_replace("{{".$k."}}", "", $view);
			} elseif(is_array($v)){
				$view = $this->makeLoop($view, $k, $v);
			}
		}
		$render = $view;		
		return $render;		
	}

	/**
	* @return string html to output on page for index.html in theme folder
	*/
	public function displayIndex(){
		return $this->displayOne("index");
	}

	/**
	* @return string html to output on page for 404.html in theme folder
	*/
	public function display404(){
		return $this->displayOne("404");
	}

	/**
	* @return string html to output on page
	*/
	private function displayOne($type){
		$view = file_exists(zweb."themes/{$this->theme}/{$type}.html") ? 
					file_get_contents(zweb."themes/{$this->theme}/{$type}.html") : "The view you requested does not exist.";

		foreach($this->template as $k=>$v){
			if(is_string($v)){
				$view = str_replace("{{".$k."}}", "$v", $view);
			} elseif(empty($v)){
				$view =  str_replace("{{".$k."}}", "", $view);
			} elseif(is_array($v)){
				$view = $this->makeLoop($view, $k, $v);
			}
		}
		$render = $view;		
		return $render;		
	}

	/**
	* @return string html to output on page: themed
	*/	
	public function render(){
		$header = $this->header;
		$footer = $this->footer;		
		$view = $this->getValidView();
		foreach($this->template as $k=>$v){
			$header = $this->simpleReplace($header, $k, $v);
			$footer = $this->simpleReplace($footer, $k, $v);
			if(is_string($v)){
				$view = str_replace("{{".$k."}}", "$v", $view);
			} elseif(empty($v)){
				$view =  str_replace("{{".$k."}}", "", $view);
			} elseif(is_array($v)){
				$view = $this->makeLoop($view, $k, $v);
			}
		}
		$render = $header.$view.$footer;		
		return $render;
	}

	/**
	* @return string html from php to output on page: themed
	*/	
	public function dynamic($controller=false){
		$controller = empty($this->uri->controller) || is_null($this->uri->controller) ? "default" : $this->uri->controller;		
		$view = empty($this->uri->method) || is_null($this->uri->method) ? "index" : $this->uri->method;	
		$view = is_string($this->view) ? $this->view : $view;
		
		$header = $this->header;
		$footer = $this->footer;		
		$this->stylesAndScripts(); //set styles and scripts
		foreach($this->template as $k=>$v){
			$header = $this->simpleReplace($header, $k, $v);
			$footer = $this->simpleReplace($footer, $k, $v);
		}
		print $header;		
		@include_once zroot."engines/{$controller}/views/{$view}.php";				
		print $footer;		
	}

	/**
	* @return string view html
	*/	
	public function getValidView(){
		$controller = $this->uri->controller == "" ? "default" : $this->uri->controller;
		$method = $this->uri->method;

		$s = new ZSites;
		$engine = $s->getEngine();
		$viewFile = $engine."{$controller}/views/{$this->view}.html";

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
		$sites = new ZSites;
		$theme = isset($sites->get($_SERVER["SERVER_NAME"])->theme) ? $sites->get($_SERVER["SERVER_NAME"])->theme : $conf->get("theme");
		return (file_exists(zweb."themes/".$theme."/")) ? $theme : "default";
	}

	/**
	* @param string sets theme in config.json
	*/	
	private function setTheme($theme){
		$conf = new ZConfig;
		$conf->set("theme", $theme);
	}

	/**
	* @return string html to output on page after replacing template information
	*/		
	private function simpleReplace($html, $k, $v){
		if(is_string($v)){
			$html = str_replace("{{".$k."}}", $v, $html);
		}
		return $html;
	}

	/**
	* @return string html to output on page after replacing multidimensional array as loop
	*/		
	private function makeLoop($view, $k, $v){
		preg_match_all("#{%foreach[\s]+(.*)[\s]+as[\s]+(.*):[\s]*(.*)[\s]*%}#", $view, $match);
		$i = 0;
		$items = @$match[1][0];
		$item = @$match[2][0];
		$template = @$match[3][0];
		
		foreach($match[1] as $loop){
			if($k == $loop){
				$html = $match[0][$i];
				$j = 0;
				$replace = "";
				foreach($v as $match[2]){
					global $_loopObj;
					$_loopObj = (object)$v[$j];
					$find = preg_replace_callback(
						"#\{\{(".$item.")(\.)([a-zA-Z0-9_-]+)\}\}#", 
						create_function(
							'$m', 
							'global $_loopObj; 
							$arg = $m[3]; 
							return @$_loopObj->{$arg};'
						), 
						$match[3][$i]
					);	
						$replace .= $find;
					$j++;
				}
				$view = str_replace($html, $replace, $view);
			}
			$i++;
		}
		return $view;
	}
}