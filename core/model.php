<?php

namespace __zf__;

abstract class ZModel implements ZIModel{	
	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new URIMaper;
		$this->_init();
	}

	function _init(){}
}

interface ZIModel{
	function _init();
}