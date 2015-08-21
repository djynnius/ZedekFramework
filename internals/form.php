<?php

namespace __zf__;

class _Form extends Zedek{

	const cryptic = "RevealerOfSecrets";

	public static function crypt(){
		$global = new ZConfig;
		return empty($global->get("encryptionKey")) || $global->get("encryptionKey") == "your_encryption"  ? self::cryptic :  $global->get("encryptionKey");
	}

	public static function captcha($request=false, $session=false){
		$request = $request == false ? @$_POST['captcha'] : $request;
		$session = $session == false ? @$_SESSION['captcha'] : $session;
		$request = trim($request);
		return $request == $session ? true : false;
	}

	/**
	* @param string $a 
	* @return string
	* aimed at preparing strings to avoid SQL injection 
	*/
	static function prepare($a){
		$a = trim($a);
		return addslashes($a);
	}

	static function batchPrepare($a=[]){
		foreach($a as $i=>$v){
			$a[$i] = self::prepare($v);
		}
		return $a;
	}

	/**
	* @param array $a
	* @return array 
	* aimed at preparing $_POST request
	*/
	static function prepareArray($a = false){
		$a = $a == false ? $_POST : $a;
		if(gettype($a) != "array") return false;
		unset($a["submit"]);
		foreach($a as $k=>$v){
			$o = trim($v);
			$o = $k == "password" ? self::encrypt($v) : addslashes($o);
			$a[$k] = $o;
		}
		return $a;
	}

	static function today(){
		return strftime("%Y-%m-%d", time());
	}

	static function now(){
		return strftime("%Y-%m-%d %H:%M:%S", time());
	}

	static function submitted($submit = "submit", $remove_submit=1){ 
		if(isset($_POST["submit"])) unset($_POST["submit"]);
		return isset($_POST[$submit]) ? true : false; 
	} 

	static function posted(){ 
		if(isset($_POST["submit"])) unset($_POST["submit"]);
		return count($_POST) > 0 ? true : false; 
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

	function same($a, $b){
		return $a == $b ? true : false;
	}

	function different($a, $b){
		return $a == $b ? false : true;
	}

	static public function encrypt($text, $type="long", $cryptic=false){

		switch($type){
			case "long":
				return self::longEncryption($text, $cryptic);
				break;
			case "short":
				return self::shortEncryption($text, $cryptic);
				break;
			default:
				return false;
		}
	}

	private function longEncryption($txt, $cryptic=false){
		if(empty($txt)) self::redirect("?msg=empty_field");
		$cryptic = trim(self::crypt());
		$txt = trim($txt);
		$len = strlen($txt);
		$md5 = (strlen($cryptic)/$len) == 0 ? $txt.$cryptic : $cryptic.$txt;
		$sha1 = strlen($cryptic) > $len ? $cryptic.$txt : $txt.$cryptic;
		$crypt = strlen($cryptic) == $len ? $cryptic.$txt : $txt.$cryptic;
		return sha1($sha1).md5($md5).crypt($crypt, $cryptic);
	}

	private function shortEncryption($txt, $cryptic=false){
		return crypt(self::longEncryption($txt, $cryptic), self::crypt());
	}

	static function fixDate($date){
		$dashed = explode("-", $date);
		if(count($dashed) == 3 && strlen($dashed[2]) == 4){
			$date = $dashed[2]."-".$dashed[1]."-".$dashed[0];
		} elseif(strpos($date, "/") != false){
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