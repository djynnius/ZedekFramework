<?php
#templating engine
class ZView extends Zedek{
	public $template;
	public $view;
	public $theme;
	public $header;
	public $footer;
	public $style;
	public $script;

	function __construct($arg1=null, $arg2=null){
		$fix = self::fixArgs($arg1, $arg2);
		$this->template = $fix['template'];
		$this->view = $fix['view'];

		$this->theme = self::getTheme() != false ? self::getTheme() : "default";
		$this->header = file_get_contents(zroot."themes/{$this->theme}/header.html");
		$this->footer = file_get_contents(zroot."themes/{$this->theme}/footer.html");
		$this->style = file_get_contents(zroot."themes/{$this->theme}/style.css");
		$this->script = file_get_contents(zroot."themes/{$this->theme}/script.js");
	}

	#cleans up arguments
	private function fixArgs($arg1, $arg2){
		$args = func_get_args();
		$template = self::template();
		$out = array('template'=>$template, 'view'=>false);
		foreach($args as $item){
			if(gettype($item) == "array"){
				$out['template'] = array_merge($template, $item); //merge order allows for overwriting
				//print_r($out);
			} elseif(gettype($item) == "string") {
				$out['view'] = (string)$item;
			} else {
				continue;
			}
		}
		return $out;
	}

	#returns default template
	private function template(){
		$a = array(
			'foo'=>"bar", 
			'zoo'=>"rar",
			'some'=>"cool",
			'school'=>array(
				array('name'=>"Bunmi", 'sex'=>"male"), 
				array('name'=>"Obogo", 'sex'=>"male"), 
				array('name'=>"Ruby", 'sex'=>"female")
			),
		);
		return $a;
	}

	function render(){
		Z::import("uri.maper");
		$uri = new URIMaper();
		$controler = $uri->controler;
		$method = $uri->method;

		$header = $this->header;
		$footer = $this->footer;
		
		#check for view file, index in engine, default none
		$viewFile = zroot."engines/{$controler}/view/{$this->view}.html";
		if(file_exists($viewFile)){
			$view = file_get_contents($viewFile);	
		} elseif(file_exists(zroot."engines/{$controler}/view/{$method}.html")){
			$view = file_get_contents(zroot."engines/{$controler}/view/{$method}.html");
		} elseif(file_exists(zroot."engines/{$controler}/view/index.html")){
			$view = file_get_contents(zroot."engines/{$controler}/view/none.html");
		} else {
			$view = file_get_contents(zroot."engines/default/view/none.html");
		}
		
		$this->template['style'] = $this->style; //pass in style
		$this->template['script'] = $this->script; //pass in script

		foreach($this->template as $k=>$v){$header = self::replace($header, $k, $v);}
		foreach($this->template as $k=>$v){$footer = self::replace($footer, $k, $v);}
		foreach($this->template as $k=>$v){
			if(is_string($v)){
				$view = str_replace("{{".$k."}}", "$v", $view);
			} elseif(is_array($v)){
				$view = self::loop($view, $k, $v);

			}
		}

		$render = $header;
		$render .= $view;
		$render .= $footer;

		echo $render;
	}

	function getTheme(){
		Z::import("config");
		$conf = new ZConfig();
		return $conf->getValue("theme");
	}

	function setTheme($theme){
		Z::import("config");
		$conf = new ZConfig();
		$conf->setValue("theme", $theme);
	}

	function replace($html, $k, $v){
		if(is_string($v)){
			$html = str_replace("{{".$k."}}", $v, $html);
		}
		return $html;
	}

	function loop($view, $k, $v){
		preg_match_all("#{%for[\s]*(.*) in (.*) :[\s]*(.*)[\s]*%}#", $view, $match);
		//print_r($match);
		$i = 0;
		foreach($match[2] as $loop){
			if($k == $loop){
				$html = $match[0][$i];
				//var_dump($match);
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

}

?>