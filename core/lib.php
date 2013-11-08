<?php

namespace __zf__;

abstract class Zlibs implements ZIlib{	
	function __construct(){
		$this->orm = new ZORM;
		$this->uri = new URIMaper;
		$this->_init();
	}

	function _init(){}
}

interface ZIlib{
	function _init();
}