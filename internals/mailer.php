<?php
/**
* @package Zedek Framework
* @subpackage Interals zedek internal class
* @version 4
* @author defestdude <defestdude@gmail.com> Donald Mkpanam
*/
namespace __zf__;
require_once zroot . "libs/php/swiftmailer/autoload.php";
use \Swift_SmtpTransport as Swift_SmtpTransport;
use \Swift_Mailer as Swift_Mailer;
use \Swift_Message as Swift_Message;

class _ZwiftMailer{
	static public $server;
	static public $port;
	static public $username;
	static public $password;
	static public $from;
	static public $app;

	/**
	*
	*/
	static public function defaultConfig(){
		$conf = new ZConfig;
		self::$server = $conf->get('smtp_server');		
		self::$port = $conf->get('smtp_port');		
		self::$username = $conf->get('smtp_username');		
		self::$password = $conf->get('smtp_password');
		self::$from = ['apps@ncdc.gov.ng'=>'NCDC Info'];
		self::$app = $conf->get("app");
	}

	/**
	*@return object Swift_Transporter
	*/	
	static public function transporter(){
		$transporter = new Swift_SmtpTransport(self::$server, self::$port, "ssl");
		$transporter->setUsername(self::$username)->setPassword(self::$password);	
		return $transporter;	
	}

	/**
	*@return object Swift_Mailer
	*/
	static public function courier(){
		return new Swift_Mailer(self::transporter());		
	}

	/**
	*@param array $to array of arguments of recipients
	*@param string $subject email subject
	*@param string $body html message
	*@param string $from sender 
	*@return object Swift_Message 
	*/
	static public function message($to = [], $subject = null, $body = null, $from = false){

		$from = $from == false ? self::$from : $from ;
		if(is_array($to) && count($to) > 2){
			if(isset($to['subject'])) $subject = $to['subject'];
			if(isset($to['body'])) $body = $to['body'];
			if(isset($to['bcc'])) $bcc = $to['bcc'];
			if(isset($to['from'])) $from = $to['from'];
			if(isset($to['to'])) $to = $to['to'];
		}

		if($to == [] || !is_array($to) || empty($body) || is_null($body)) return false;
		$subject = $subject == self::$app ?  : $subject;
		$from = $from == false ? self::$from : $from;			

		return (new Swift_Message)->setFrom($from)->setTo($to)->setBcc($bcc)->setSubject($subject)->setBody($body, "text/html");
	}

	/**
	*@param array $to array of arguments of recipients
	*@param string $subject email subject
	*@param string $body html message
	*@param string $from sender 
	*@return object Swift_Message 
	*/
	static public function simpleMessage($subject = null, $to = false, $body=null, $from = null){
		$from = $from == false ? self::$from : $from;
		return (new Swift_Message($subject))->setFrom($from)->setTo($to)->setBody($body, "text/html");
	}


	static public function send($message){
		self::courier()->send($message);		
	}

}