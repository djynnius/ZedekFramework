<?php

namespace __zf__;

class Postman{
	function __construct(){}

	function sendMail($to, $subject, $message, $senderMail){
		mail($to, $subject, $message, "From: {$senderMail}");
	}	
}

?>