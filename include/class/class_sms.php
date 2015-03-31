<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}

class Sms {
	private $connection;
	public  $error_description;

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = New Connection();
		$this->connection = $db->connect();
	}

	public function send_sms($mobile,$message)
	{

	   	$url="http://tx.ebensms.in/api/web2sms.php?workingkey=9412x5966i644f695452&sender=ConnectNCabs&to=".$mobile."&message=".urlencode($message);


		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output=curl_exec($ch);
		curl_close($ch);                               
		return $output;
	}
    
}
?>
