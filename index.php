<?php 
//error_reporting(0);
define('CHECK_INCLUDED', true);
require_once 'include/conf.php';
require_once 'include/functions.php';
require 'include/libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * /root
 * url - /
 * method - get
 * 
 */

$app->get('/', function() use ($app) {

pagenotfound();

});

/**
 * validate-app
 * url - /validate-app
 * method - post
 * params - app_id,imei
 */

$app->get('/validate-app', function() use ($app) {

	/*require_once dirname(__FILE__) . '/include/class/class_notifications.php';
	$Notifications = new Notifications();
	$Notifications->logreponds($app->request()->get('app_id'),$app->request()->get('imei'),$ac=1);*/

	if($app->request()->post('app_id')!='' && $app->request()->post('imei')!=''){
		// define response array 
		$response = array();
		//add your class, if required
		require_once dirname(__FILE__) . '/include/class/class_validate_app.php';
		$validate_app = new Validate_app();
		$app_key=$app->request()->post('app_id');
		$imei=$app->request()->post('imei');
		$validate = $validate_app->validate_app($app_key,$imei);
	
		if($validate){
		//  success
				$response["e"] = NO_ERROR;
		
		} else {

		//  error occurred
			$response["e"] = ERROR;
		
		
		}
		ReturnResponse(200, $response);
	}else{
		pagenotfound();
	}
});


/**
 * register-with-gcm-id
 * url - /register-gcm
 * method - post
 * params - app_id,imei,regid
 */

$app->get('/register-gcm', function() use ($app) {
	if($app->request()->post('app_id')!='' && $app->request()->post('imei')!=''){
		// define response array 
		$response = array();
		//add your class, if required
		require_once dirname(__FILE__) . '/include/class/class_validate_app.php';
		require_once dirname(__FILE__) . '/include/class/class_driver.php';

		$Driver = new Driver();
		$validate_app = new Validate_app();

		$app_key=$app->request()->post('app_id');
		$imei=$app->request()->post('imei');
		$regid=$app->request()->post('regid');
		$data=array('gcm_regid'=>$regid);
		$driver = $Driver-update($data,$app_key);
	
		if($driver){
		//  success
				$response["e"] = NO_ERROR;
		
		} else {

		//  error occurred
			$response["e"] = ERROR;
		
		
		}
		ReturnResponse(200, $response);
	}else{
		pagenotfound();
	}
});


/**
 * vehicle-loc-logs
 * url - /vehicle-loc-logs
 * method - tcp
 * params - app_id,lat,lng
 */

$app->get('/vehicle-loc-logs', function() use ($app) {

	require_once dirname(__FILE__) . '/include/class/class_vehicle_location_log.php';
	$host = "162.144.57.243";
	$port = 10000;
	$message = "";
	set_time_limit(0);
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

	// bind socket to port
	$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
	// put server into passive state and listen for connections
	$result = socket_listen($socket, 3); 
	//or die("Could not set up socket listener\n");

	while(true){
		  $com = socket_accept($socket) or die("Could not accept incoming connection\n");
	if($com){
	// read client input
	$input = socket_read($com, 1024) or die("Could not read input\n");
	// clean up input string
	//print_r($input);
	
	$json_res = json_encode($input);
	$json_res_decode = json_decode($input);

	$app_key	=	json_res_decode['app_id'];
	$lat  		= json_res_decode['lt'];
	$lng 		  = json_res_decode['lg'];
	$VehicleLocLog->logLocation($app_key,$lat,$lng,$id=gINVALID);
	//echo $json_res;
	//echo "Client says: ".$input."</br>";
	socket_write($com, $json_res, strlen ($json_res)) or die("Could not write output\n");
	}
	}
	// close sockets
	socket_close($com);
	socket_close($socket);
});



/**
 * log locations and fetch notifications
 * url - /trip-logs
 * method - get
 * params - app_id,lt,lg,td,lts,lgs,lte,lge,dt,srt,end,tid
 */

$app->get('/trip-logs', function() use ($app) {
	$app_key=$app->request()->get('app_id');
	$lat  = $app->request()->get('lt');
	$lng  = $app->request()->get('lg');
	$ts   = $app->request()->get('ts');
	$id	  =	$app->request()->get('tid');
	//$response['res']=$app_key.' '.$lat.' '.$lng.' '.$td;
	//add your class, if required
	require_once dirname(__FILE__) . '/include/class/class_vehicle_location_log.php';
	require_once dirname(__FILE__) . '/include/class/class_notifications.php';
	require_once dirname(__FILE__) . '/include/class/class_trip.php';
	require_once dirname(__FILE__) . '/include/class/class_driver.php';
	$Driver = new Driver();
	$VehicleLocLog = new VehicleLocationLog();
	$Notifications = new Notifications();
	$Trip = new Trip();	
	$driver_exists=$Driver->getDriver($app_key);
	if($driver_exists!=false){
		//$Notifications->logreponds($app_key,$tid='-1',$td,$_SERVER['QUERY_STRING']);
	if($ts==TRIP_START){
		$result=$VehicleLocLog->logLocation($app_key,$lat,$lng,$id);
		if($id>gINVALID){
			$driver_status=DRIVER_STATUS_ENGAGED;
			$res=$Driver->changeStatus($app_key,$driver_status);
		}
		if($res==true){
			$response["e"]=NO_ERROR;
		}else{
			$response["e"]=ERROR;
		}
	}else if($ts==TRIP_STOP){
		
		$dataArray['distance_in_km_from_app']		=	$app->request()->get('dt');
		$srt																		=	$app->request()->get('srt')/1000;
		$end																		=	$app->request()->get('end')/1000;
		$dataArray['trip_start_date_time']			=	date('Y-m-d H:i:s',$srt);
		$dataArray['trip_end_date_time']				=	date('Y-m-d H:i:s',$end);
		$dataArray['trip_status_id']						=	TRIP_STATUS_TRIP_COMPLETED;
		$tot																		= $app->request()->get('dt')*MINIMUM_TARIFF;
		$driver_status													=	DRIVER_STATUS_ACTIVE;
		
		$trips=$Trip->getDetails($id);

		if($tot < $trips['total_amount']){
			$dataArray['total_amount']	= $trips['total_amount'];
		}else{
			$dataArray['total_amount']	= $tot;
		}
		$Trip->update($dataArray,$id);	
		
		$res=$Driver->changeStatus($app_key,$driver_status);		
		
		$VehicleLocLog->logLocation($app_key,$lat,$lng,$id);
		if($res==true){
			$response["e"]=NO_ERROR;
		}else{
			$response["e"]=ERROR;
		}
	}
	}
	ReturnResponse(200, $response);
});

/**
 * reset driver status to active 
 * url - /reset
 * method - get
 * params - app_id
 */

$app->get('/reset', function() use ($app) {
	$app_key=$app->request()->get('app_id');
	$driver_status=DRIVER_STATUS_ACTIVE;
	//add your class, if required
	require_once dirname(__FILE__) . '/include/class/class_driver.php';
	$Driver = new Driver();
	$res=$Driver->changeStatus($app_key,$driver_status);
	if($res==true){
		$response["e"]=NO_ERROR;
	}else{
		$response["e"]=ERROR;
	}

	ReturnResponse(200, $response);
});

/**
 * display loged locations for testing pupose only 
 * url - /locations
 * method - get
 * params - 
 */

$app->get('/locations', function() use ($app) {

require_once dirname(__FILE__) . '/include/class/class_vehicle_location_log.php';
$VehicleLocLog = new VehicleLocationLog();

$locations=$VehicleLocLog->getLogocationLogs();
echo "<table><tr>
		<td>id</td>
		<td>app key</td>
		<td>lat</td>
		<td>lng</td>
		<td>lng</td>
		</tr>";
for($i=0;$i<count($locations);$i++){
echo "<tr>
		<td>".$locations[$i]['id']."</td>
		<td>".$locations[$i]['app_key']."</td>
		<td>".$locations[$i]['lat']."</td>
		<td>".$locations[$i]['lng']."</td>
		<td>".$locations[$i]["datetime"]."</td></tr>";

}
echo "</table>";
});

/**
 * user responds 
 * url - /user-responds
 * method - get
 * params - app_id,nid,tid,ac
 */

$app->get('/user-responds', function() use ($app) {
	$app_key=$app->request()->get('app_id');
	$trip_id=$app->request()->get('tid');
	$notification_id=$app->request()->get('nid');
	$ac=$app->request()->get('ac');
	//add your class, if required
	require_once dirname(__FILE__) . '/include/class/class_driver.php';
	$Driver = new Driver();
	require_once dirname(__FILE__) . '/include/class/class_notifications.php';
	require_once dirname(__FILE__) . '/include/class/class_trip.php';
	$Notifications = new Notifications();
	$Trip = new Trip();	
	if($ac==TRIP_NOTIFICATION_REJECTED){
		$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
		$res=$Notifications->updateNotifications($data,$notification_id);
		if($res==true){
		$response['ac']=TRIP_REJECTED;
		$driver_status=DRIVER_STATUS_ACTIVE;
		$Driver->changeStatus($app_key,$driver_status);

		}else{
			$response['ac']=TRIP_ERROR;
		}

	}else if($ac==TRIP_NOTIFICATION_ACCEPTED) {
		$data=array('notification_status_id'=>NOTIFICATION_STATUS_RESPONDED,'notification_view_status_id'=>NOTIFICATION_VIEWED_STATUS);
		$Notifications->updateNotifications($data,$notification_id);
		$trips=$Trip->getDetails($trip_id);
		if(trim($trips['driver_id'])==gINVALID && trim($trips['trip_status_id'])==TRIP_STATUS_PENDING){
			$driver_id=$Driver->getDriver($app_key);
			if($driver_id!=false){
				$dataArray=array('driver_id'=>$driver_id['id'],'trip_status_id'=>TRIP_STATUS_ACCEPTED);
				$res=$Trip->update($dataArray,$trip_id);
				if($res==true){
					$trips=$Trip->getDetails($trip_id);
					require_once dirname(__FILE__) . '/include/class/class_customer.php';
					$Customer = new Customer();
					$Customers=$Customer->getUserById($trips['customer_id']);
					$response['ac']=TRIP_AWARDED;
					$response['cn']=$Customers['name'];
					$response['cm']=$Customers['mobile'];
					if($trips['trip_type_id']==FUTURE_TRIP){
						$driver_status=DRIVER_STATUS_ACTIVE;
						$Driver->changeStatus($app_key,$driver_status);
					}
				}else{
					$response['ac']=TRIP_ERROR;
				}		
			}else{
				$response['ac']=TRIP_ERROR;
			}
		}else{
			$response['ac']=TRIP_REGRET;
			$driver_status=DRIVER_STATUS_ACTIVE;
			$Driver->changeStatus($app_key,$driver_status);
		}

	}else if($ac==TRIP_NOTIFICATION_TIME_OUT){
		$data=array('notification_status_id'=>NOTIFICATION_STATUS_EXPIRED,'notification_view_status_id'=>NOTIFICATION_NOT_VIEWED_STATUS);
		$res=$Notifications->updateNotifications($data,$notification_id);
		//$Notifications->logreponds($app_key,$trip_id,$ac);
		if($res==true){
			$response['ac']=TRIP_TIME_OUT;
			$driver_status=DRIVER_STATUS_ACTIVE;
			$Driver->changeStatus($app_key,$driver_status);
		}else{
			$response['ac']=TRIP_ERROR;
		}
	}
	ReturnResponse(200, $response);
});



function checkFutureOrInstantTrip($tripdatetime){

		$date1 = date_create(date('Y-m-d H:i:s'));
		$date2 = date_create($tripdatetime);
		$diff= date_diff($date1, $date2);//echo $diff->d.' '. $diff->h.' '.$diff->i;
		if(($diff->d == 0 && $diff->h==0 && $diff->i > 30) || ($diff->d == 0 && $diff->h > 0) || $diff->d > 0) {

		return FUTURE_TRIP;

		}else{

		return INSTANT_TRIP;

		}

	}
function pagenotfound(){

header("HTTP/1.1 404 Page Not Found");
//echo "--- error message here -----";
die();

}

function ReturnResponse($http_response, $response) {
	//return response : json
    $app = \Slim\Slim::getInstance();
    $app->status($http_response);
    $app->contentType('application/json');
    echo json_encode($response);
}

$app->run();
?>
