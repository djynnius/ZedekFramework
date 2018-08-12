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

	/**
	 * Checks the length of the input
	 * @param  mixed $a the input
	 * @param  array  $options options for min, max and exact match
	 * @return input as validated or null
	 */
	static public function length($a, $options=[]){
	}

	/**
	 * checks if the input is a match
	 * @param mixed $a input
	 * @param  string $regex regular expression to match againt
	 * @return [type] 
	 */
	static public function matching($a, $regex='//'){
		return preg_match($regex, $a);
	}

	/**
	 * [asURL description]
	 * @param  [type] $a [description]
	 * @return [type]    [description]
	 */
	static function asURL($a){
		return filter_var($a, FILTER_SANITIZE_ENCODED);
	}

	/**
	 * [asURL description]
	 * @param  [type] $a [description]
	 * @return [type]    [description]
	 */
	static function asString($a){
		return filter_var($a, FILTER_SANITIZE_STRING);
	}
	
	/**
	 * Filter method to return valid email characters
	 * @param string $a email
	 * @return string validated email
	 */
	static function asEmail($a){
		return filter_var($a, FILTER_SANITIZE_EMAIL);
	}
	
	/**
	 * URL filter
	 * @param  string $a URL
	 * @return string validated URL encoded for the browser
	 */
	static function safeSQL($a){
		return filter_var($a, FILTER_SANITIZE_MAGIC_QUOTES);
	}

	/**
	 * Integer validation including digits + and -
	 * @param  mixed $a integer
	 * @return integer
	 */
	static function asInt($a){
		return filter_var($a, FILTER_SANITIZE_NUMBER_INT);
	}

	/**
	 * strip Tags
	 * @param  string $a html with tags
	 * @return string html without tags
	 */
	static function noTags($a){
		return filter_var($a, FILTER_SANITIZE_SPECIAL_CHARS);
	}


}