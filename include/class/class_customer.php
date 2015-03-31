<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}


class Customer {

	private $connection;
	public  $error_description;

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = new Connection();
		$this->connection = $db->connect();
	}
	
		
	public function getUserByMobile($mobile) {
		$strSQL = "SELECT * FROM customers WHERE mobile = '".mysqli_real_escape_string($this->connection,$mobile)."'";

		$user_array = array();
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			$user_array["id"] = mysql_result($rsRES,0,'id');
			$user_array["name"] = mysql_result($rsRES,0,'name');
			$user_array["mobile"] = mysql_result($rsRES,0,'mobile');
			$user_array["app_id"] = mysql_result($rsRES,0,'app_id');

			return $user_array;
		}
		else{
			$this->error_description = "Login Failed";
			return false;
		}
	}

	public function getUserById($id) {
		$strSQL = "SELECT * FROM customers WHERE id = '".mysqli_real_escape_string($this->connection,$id)."'";

		$user_array = array();
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			$row=mysqli_fetch_row($rsRES);
			$user_array["id"] = $row[0];
			$user_array["name"] = $row[1];
			$user_array["mobile"] = $row[2];
			return $user_array;
		}
		else{
			
			return false;
		}
	}
}
?>
