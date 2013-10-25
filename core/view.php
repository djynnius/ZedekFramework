<?php
#templating engine
namespace __zf__;

class ZView extends Zedek{
	public $template;
	public $view;
	public $theme;
	public $header;
	public $footer;
	public $style;
	public $script;
	public $configFile;
	public $uri;

	function __construct($arg1=null, $arg2=null){
		$fix = $this->fixArgs($arg1, $arg2);
		$this->template = $fix['template'];
		$this->view = $fix['view'];
		$this->theme = $this->getTheme() != false && $this->getTheme() != null ? $this->getTheme() : "default";
		$this->getAllThemeFiles();
		$this->configFile = zroot."config/template.json";
		$this->uri = new URIMaper;
	}

	#cleans up arguments
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
	private function getThemeFile($file, $type="html"){
		$puts = file_exists(zroot."themes/{$this->theme}/{$file}.{$type}") ? 
					file_get_contents(zroot."themes/{$this->theme}/{$file}.{$type}") : 
					(file_exists(zroot."themes/default/{$file}.{$type}") ? 
						file_get_contents(zroot."themes/default/{$file}.{$type}") : 
						""
					);		
		return $puts;
	}

	#pulls in all theme files and assigns them to class attibutes
	private function getAllThemeFiles(){
		$themeFolder = zroot."themes/";
		$files = scandir($themeFolder.$this->theme);

		foreach($files as $file){
			if(!is_dir($themeFolder.$file)){
				$info = pathinfo($themeFolder.$file);
				$this->$info['filename'] = $this->getThemeFile($info['filename'], $info['extension']);
			}
		}		
	}	

	#returns default template
	private function template(){
		$config = new ZConfig;
		$a = array(
			'footer'=>"Zedek Framework. Version".$config->get("version"), 
		);
		$b = $this->configTemplate();
		$a = array_merge($a, $b);
		return $a;
	}

	public function configTemplate(){
		$file = zroot."config/template.json";
		$json = file_get_contents($file);
		$pseudoArray = json_decode($json);
		$array = (array)$pseudoArray;
		return $array;
	}

	public function render(){
		$header = $this->header;
		$footer = $this->footer;		
		$view = $this->getValidView();
		$this->stylesAndScripts(); //set styles and scripts
		foreach($this->template as $k=>$v){
			$header = $this->simpleReplace($header, $k, $v);
			$footer = $this->simpleReplace($footer, $k, $v);
			if(is_string($v)){
				$view = str_replace("{{".$k."}}", "$v", $view);
			} elseif(is_array($v)){
				$view = $this->makeLoop($view, $k, $v);
			}
		}
		$render = $header.$view.$footer;		
		return $render;
	}

	private function stylesAndScripts(){
		$this->template['style'] = $this->style;
		$this->template['script'] = $this->script;
		#external scripts
		$this->template['jQuery2'] = $this->getExternalScript("jQuery1.10.2");
		$this->template['jQuery1'] = $this->getExternalScript("jQuery2.0.3");
		$this->template['jQueryMigrate'] = $this->getExternalScript("jQueryMigrate1.2.1");
		$this->template['jQueryUI'] = $this->getExternalScript("jQueryUI");
		$this->template['jQueryNivo'] = $this->getExternalScript("jQueryNivo");
		$this->template['nicEdit'] = $this->getExternalScript("nicEdit");
	}

	private function getExternalScript($file){
		$file = zroot."libs/external_packages/js/".$file.".js";
		return file_exists($file) ? file_get_contents($file) : false;
	}

	private function getValidView(){
		$class = $this->uri->class == "" ? "default" : $this->uri->class;
		$method = $this->uri->method;
		$viewFile = zroot."engines/{$class}/view/{$this->view}.html";
		if(file_exists($viewFile)){
			$view = file_get_contents($viewFile);	
		} elseif(file_exists(zroot."engines/{$class}/view/{$method}.html")){
			$view = file_get_contents(zroot."engines/{$class}/view/{$method}.html");
		} elseif(file_exists(zroot."engines/{$class}/view/none.html")){
			$view = file_get_contents(zroot."engines/{$class}/view/none.html");
		} elseif(file_exists(zroot."engines/default/view/{$this->view}.html")){
			$view = file_get_contents(zroot."engines/default/view/{$this->view}.html");
		} elseif(file_exists(zroot."engines/default/view/none.html")){
			$view = file_get_contents(zroot."engines/default/view/none.html");
		} else {
			$view = "";
		}
		return $view;		
	}

	public function getTheme(){
		$conf = new ZConfig;
		return $conf->get("theme");
	}

	private function setTheme($theme){
		$conf = new ZConfig;
		$conf->set("theme", $theme);
	}

	private function simpleReplace($html, $k, $v){
		if(is_string($v)){
			$html = str_replace("{{".$k."}}", $v, $html);
		}
		return $html;
	}

	private function makeLoop($view, $k, $v){
		preg_match_all("#{%for[\s]*(.*) in (.*) :[\s]*(.*)[\s]*endfor%}#", $view, $match);
		$i = 0;
		foreach($match[2] as $loop){
			if($k == $loop){
				$html = $match[0][$i];
				$j = 0;
				$replace = "";
				foreach($v as $match[1]){
					global $_loopObj;
					$_loopObj = (object)$v[$j];
					$find = preg_replace_callback(
						'#([a-z0-9_-]+)(\.)([a-zA-Z0-9_-]+)#', 
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

	private function logic(){}
}

?>