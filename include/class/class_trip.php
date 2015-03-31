<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}

class Trip {

	private $connection;
	public  $error_description;

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = new Connection();
		$this->connection = $db->connect();
	}
	
	public function update($dataArray = array(),$id){
		if(count($dataArray)>0){
			$i=0;
			$strSQL = "UPDATE trips SET ";
			foreach($dataArray as $key=>$value){
				$strSQL .= $key."='".$value;
				if(count($dataArray)-1>$i){
				$strSQL .="',";
				}else{
				$strSQL .="'";
				}
				$i++;
			}
			$strSQL .=" WHERE id='".$id."'";

			//$strSQL = substr($strSQL,0,-1);

			$rsRES = mysqli_query($this->connection,$strSQL);
			
			if(mysqli_affected_rows($this->connection) == 1){
				
				return true;
			}else{
				return false;
			}	
		}else{
			
			return false;
		}
		
	}

	public function getDetails($id)
	{
		
		$strSQL = "SELECT * FROM trips WHERE id = '".mysqli_real_escape_string($this->connection,$id)."'";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			return mysqli_fetch_assoc($rsRES);
		}else{
			
			return false;
		}
		
	}
}
