<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}


class Driver{

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = new Connection();
		$this->connection = $db->connect();
	}

	public function changeStatus($app_key,$status){
		$strSQL = "UPDATE  drivers SET driver_status_id= '".$status."',updated='".date('Y-m-d H:i:s')."' WHERE app_key='".$app_key."'";
		$rsRES = mysqli_query($this->connection,$strSQL);
			
			if(mysqli_affected_rows($this->connection) == 1){
				
				return true;
			}else{
				
				return false;
			}

	}
	
	public function getDriver($app_key){
		
		$strSQL = "SELECT id FROM drivers WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND  driver_status_id!=".DRIVER_STATUS_SUSPENDED." AND  driver_status_id!=".DRIVER_STATUS_DISMISSED;
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			return mysqli_fetch_assoc($rsRES);
		}else{
			
			return false;
		}
		
	}



}
?>
