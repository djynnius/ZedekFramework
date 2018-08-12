<?php
/**
* @package Zedek Framework
* @subpackage ZController zedek super controller class
* @version 4.0
* @author djyninus <psilent@gmail.com> Ikakke Ikpe
* @link https://github.com/djynnius/zedek
* @link https://github.com/djynnius/zedek.git
*/

namespace __zf__;

class App extends ZModel{

	function __tmp__(){
		$tmp = array();
		return $tmp;
	}

	function something(){
		$sql = "SELECT * FROM users";
		return self::__data__($sql);
	}

	/**
	*@param String $sql query
	*@return Array Multidimensional arrray
	*/
	function __data__($sql){
		$recs = ZORM::rows($sql);		
		
		$o = [];
		
		foreach($recs as $i=>$rec){
			$o[] = (array)$rec;
		}

		return $o;	
	}

}
