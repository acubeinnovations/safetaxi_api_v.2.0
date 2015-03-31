<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}

class Validate_app{

	private $connection;
	public  $error_description;

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = new Connection();
		$this->connection = $db->connect();
	}

	
	
	public function validate_app($app_id,$imei){

		$strSQL = "SELECT id FROM drivers WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_id)."' AND device_imei='".mysqli_real_escape_string($this->connection,$imei)."'";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){

			return true;

		}else{
			return false;
		}	
	}
}
