<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}

class VehicleLocationLog {

	private $connection;
	public  $error_description;

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = new Connection();
		$this->connection = $db->connect();
	}

	
	public function logLocation($app_key,$lat,$lng,$trip_id='-1') {

		$strSQL = "INSERT INTO  vehicle_locations_logs (app_key,lat,lng,trip_id,created) VALUES (";
		$strSQL .="'".$app_key."',";
		$strSQL .="'".$lat."',";
		$strSQL .="'".$lng."',";
		$strSQL .="'".$trip_id."',";
		$strSQL .="'".date('Y-m-d H:i:s')."')";
		$result=mysqli_query($this->connection, $strSQL);
		return true;
		
	}

	public function getLogocationLogs($app_key='') {
		$strSQL = "SELECT * FROM  vehicle_locations_logs";
		if($app_key!=''){
		$strSQL .= " WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."'";
		}
		$strSQL .= " order by id DESC";
		$locations = array();
		$rsRES = mysqli_query($this->connection,$strSQL);
		$i=0;
		if ( mysqli_num_rows($rsRES) >= 1 ){
			while ($row=mysqli_fetch_row($rsRES))
			{
			$locations[$i]["id"] = $row[0];
			$locations[$i]["app_key"] = $row[1];
			$locations[$i]["lat"] = $row[2];
			$locations[$i]["lng"] = $row[3];
			$locations[$i]["trip_id"] = $row[4];
			$locations[$i]["datetime"] = $row[5];
			$i++;
			
			}
			return $locations;
		}
		else{
			
			return false;
		}
	}
	
	

}
?>
