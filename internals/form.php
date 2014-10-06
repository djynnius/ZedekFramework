<?php

namespace __zf__;

class _Form extends Zedek{

	const cryptic = "SirAbubakarTafawaBalewa";

	public static function captcha($request=false, $session=false){
		$request = $request == false ? @$_POST['captcha'] : $request;
		$session = $session == false ? @$_SESSION['captcha'] : $session;
		$request = trim($request);
		return $request == $session ? true : false;
	}

	static function today(){
		return strftime("%Y-%m-%d", time());
	}

	static function now(){
		return strftime("%Y-%m-%d %H:%M:%S", time());
	}

	static function submitted($submit = "submit"){
		return isset($_POST[$submit]) ? true : false;
	}

	static public function compare($field1=false, $field2=false){
		$args = func_num_args();
		switch($args){
			case 1:
				if(is_array($field1)){
					$a = trim($field1[0]);
					foreach($field1 as $field){
						if($a != trim($field)) return false;
					}
					return true;
				} else {
					return false;
				}
				break;
			case 2:
				$field1 = trim($field1);
				$field2 = trim($field2);
				return $field1 == $field2 ? true : false;
				break;
			default:
				return false;
		}
	}

	static public function encrypt($text, $type="long"){
		switch($type){
			case "long":
				return self::longEncryption($text);
				break;
			case "short":
				return self::shortEncryption($text);
				break;
			default:
				return false;
		}
	}

	private function longEncryption($txt, $cryptic=false){
		if(empty($txt)) self::redirect("?msg=empty_field");
		$cryptic = trim(self::cryptic);
		$txt = trim($txt);
		$len = strlen($txt);
		$md5 = (strlen($cryptic)/$len) == 0 ? $txt.$cryptic : $cryptic.$txt;
		$sha1 = strlen($cryptic) > $len ? $cryptic.$txt : $txt.$cryptic;
		$crypt = strlen($cryptic) == $len ? $cryptic.$txt : $txt.$cryptic;
		return sha1($sha1).md5($md5).crypt($crypt, $cryptic);
	}

	private function shortEncryption($txt, $cryptic=false){
		return crypt(self::longEncryption($txt, $cryptic), self::cryptic);
	}

	static function fixDate($date){
		if(strpos($date, "/") != false){
			$date = explode("/", $date);
			$date = $date[2]."-".$date[0]."-".$date[1];				
		} elseif(strpos($date, "-") != false) {
			$date = $date;
		} else {
			$date = strftime("%Y-%m-%d", strtotime($date));
		}
		return $date;		
	}	
}