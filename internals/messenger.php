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

class _Msg{
	static public function send($post){
		$config = new ZConfig;
		foreach($post as $k=>$item){
			$$k = $item;
		}

		$to = self::fixEmailAddresses($to);
		$cc = isset($cc) ? self::fixEmailAddresses($cc) : "";

		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";		
		$header .= "To: {$to}"."\r\n";
		$header .= "From: ".$config->get('app')." <".$config->get('email').">"."\r\n";
		$header .= "Cc: {$cc}"."\r\n";

		mail($to, $subject, $message, $header);

	}

	private function fixEmailAddresses($to){
		$to = trim($to);
		$to = explode(" ", $to);

		$o = array();
		foreach($to as $i=>$v){
			$v = trim($v);
			$v = explode(",", $v);
			foreach($v as $j=>$u){
				$u = trim($u);
				if(!empty($u)) $o[] = $u;
			}
		}

		$to = null;
		$to = join(",", $o);
		return $to;		
	}

}