<?php
// prevent execution of this page by direct call by browser
if ( !defined('CHECK_INCLUDED') ){
	exit();
}

class Notifications {

	private $connection;
	public  $error_description;

	function __construct() {
		require_once dirname(__FILE__) . '/class_connection.php';
		$db = new Connection();
		$this->connection = $db->connect();
	}
	
	

	public function tripNotifications($app_key){
		
		$strSQL = "SELECT * FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_NEW_TRIP." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id DESC LIMIT 1";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			return mysqli_fetch_assoc($rsRES);
		}else{
			
			return false;
		}
		
	}
	public function tripAwardedNotifications($app_key){
		
		$strSQL = "SELECT * FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_TRIP_AWARDED." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id DESC LIMIT 1";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			return mysqli_fetch_assoc($rsRES);
		}else{
			
			return false;
		}
		
	}
	public function tripRegretNotifications($app_key){
		
		$strSQL = "SELECT * FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_TRIP_REGRET." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id DESC LIMIT 1";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) == 1 ){
			return mysqli_fetch_assoc($rsRES);
		}else{
			
			return false;
		}
		
	}
	public function tripCancelNotifications($app_key){
		
		$strSQL = "SELECT trip_id,id FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_TRIP_CANCELLED." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) >= 1 ){
			$i=0;
			$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
			//return mysqli_fetch_row($rsRES);
			while ($row=mysqli_fetch_row($rsRES))
			{
			$trips[$i]=array('tid'=>$row[0]);	
			$this->updateNotifications($data,$row[1]);
			$i++;
			}
			return $trips;
		}else{
			
			return false;
		}
		
	}
	public function tripUpdateNotifications($app_key){
		
		$strSQL = "SELECT trip_id,id FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_TRIP_UPDATE." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) >= 1 ){
			$i=0;
			$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
			while ($row=mysqli_fetch_row($rsRES))
			{
			$trips[$i]=$row[0];	
			$this->updateNotifications($data,$row[1]);
			$i++;
			}
			return $trips;
		}else{
			
			return false;
		}
		
	}

	public function reccurenttrips($app_key){
		
		$strSQL = "SELECT N.trip_id as tid,N.id as nid,T.trip_from as fr,T.trip_to as 'to',UNIX_TIMESTAMP(CONCAT(T.pick_up_date,' ',T.pick_up_time)) as sec,C.name as cn,C.mobile as cm FROM notifications as N LEFT JOIN trips as T ON T.id=N.trip_id LEFT JOIN customers as C ON C.id=T.customer_id WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_TRIP_RECCURENT." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY N.id";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) >= 1 ){
			$i=0;
			$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
			while ($row=mysqli_fetch_row($rsRES))
			{
			$customer['cn']		=	$row[5];
			$customer['cm']		=	$row[6];
			$trips[$i]['tid']	=	$row[0];	
			$trips[$i]['nid']	=	$row[1];
			$trips[$i]['fr']	=	$row[2];
			$trips[$i]['to']	=	$row[3];
			$trips[$i]['sec']	=	$row[4]*1000;
			$this->updateNotifications($data,$row[1]);
			$i++;
			}
			$rtrips['customer']=$customer;
			$rtrips['trips']=$trips;
			return $rtrips;
		}else{
			
			return false;
		}
		
	}

	public function commonmsgNotifications($app_key){
		
		$strSQL = "SELECT id,message FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_COMMON_MSGS." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) >= 1 ){
			$i=0;
			$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
			while ($row=mysqli_fetch_row($rsRES))
			{
			$cmsgs[$i]=$row[1];	
			$this->updateNotifications($data,$row[0]);
			$i++;
			}
			return $cmsgs;
		}else{
			
			return false;
		}
		
	}

	public function paymentNotifications($app_key){
		
		$strSQL = "SELECT id,message FROM notifications WHERE app_key = '".mysqli_real_escape_string($this->connection,$app_key)."' AND notification_type_id=".NOTIFICATION_TYPE_PAYMENT_MSGS." AND notification_status_id=".gINVALID." AND  notification_view_status_id=".NOTIFICATION_NOT_VIEWED_STATUS." ORDER BY id";
		$rsRES = mysqli_query($this->connection,$strSQL);
		if ( mysqli_num_rows($rsRES) >= 1 ){
			$i=0;
			$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
			while ($row=mysqli_fetch_row($rsRES))
			{
			$pmsgs[$i]=$row[1];	
			$this->updateNotifications($data,$row[0]);
			$i++;
			}
			return $pmsgs;
		}else{
			
			return false;
		}
		
	}

	public function updateNotifications($dataArray = array(),$id) {
		if($dataArray){
			$i=0;
			$strSQL = "UPDATE  notifications SET ";
			foreach($dataArray as $key=>$value){
				$strSQL .= $key."='".mysqli_real_escape_string($this->connection,$value);
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

	public function logreponds($app_key,$tid,$ac,$string=''){
			
		$strSQL = "INSERT INTO  userresponds (app_id,tid,ac,string,created) VALUES (";
		$strSQL .="'".$app_key."',";
		$strSQL .="'".$tid."',";
		$strSQL .="'".$ac."',";
		$strSQL .="'".$string."',";
		$strSQL .="'".date('Y-m-d H:i:s')."')";
		$result=mysqli_query($this->connection, $strSQL);
		return true;
		}
}
