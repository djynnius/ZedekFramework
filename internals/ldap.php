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
/**
* @package Zedek Framework
* @subpackage Interals zedek internal class
* @version 4
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
*/

namespace __zf__;

class _LDAP extends Zedek{

	/**
	* @param string $username ie LDAP RDN or DN
	* @param string $password ie associated password
	* @param string $ip eg 192.168.0.1
	* @param string $domain eg organization.local
	* @param int $version LDAP protocol version
	* @return bool 
	*/
	public static function connect($username, $password, $ip, $domain, $ou, $version=3) {
		$username = trim($username);
		$password = trim($password);

		// connect to LDAP server
		$cxn = ldap_connect($ip) or die ("Could not connect to LDAP server.");
		if($cxn){
			// binding to LDAP server
			ldap_set_option($cxn, LDAP_OPT_PROTOCOL_VERSION, $version);
			$ldapbind = @ldap_bind($cxn, $username."@".$domain, $password);
			
			// verify binding
			$sdomain = explode(".", $domain);
			if($ldapbind) {
				$dn = "OU={$ou},DC={$sdomain[0]},DC={$sdomain[1]}";
				$filter="(sAMAccountName={$username})";
				$params = array("cn", "sn", "givenname", "mail");
				$sr=ldap_search($cxn, $dn, $filter, $params);
				$info = ldap_get_entries($cxn, $sr);
				if ($info["count"] == 1){
					$_SESSION["_LDAP"]["login"] = "1";
					$_SESSION["_LDAP"]["user"] = $info[0]["cn"][0];
					$details = explode(",", $info[0]["dn"]);
					$details = explode("=", $details[1]);
					$office = $details[1];
					$_SESSION["_LDAP"]["OU"] = $office;
					$bool = true;
				} else {
					$bool = false;
				}				
			} else {
				$bool = false;
			}		
		} else {
			$bool = false;
		}
		@ldap_close($cxn);
		return $bool;
	} 
}