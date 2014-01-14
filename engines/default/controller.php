<?php
namespace __zf__;
use \PDO as PDO;
class CController extends ZController{
	function _default(){
		$this->render('index');
	}
}