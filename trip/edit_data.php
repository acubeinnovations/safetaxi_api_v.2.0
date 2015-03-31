<?php

//edit_data.php
$id=$_POST['id'];
$status_id=$_POST['name'];
$notification_view_status_id=$_POST['notification_view_status_id'];
include "db.php";
//

echo $order = "UPDATE notifications 

          SET notification_status_id='$status_id',notification_view_status_id='$notification_view_status_id'

          WHERE 

          id='$id'";


//UPDATE  `safetaxi`.`notifications` SET  `notification_status_id` =  '12' WHERE  `notifications`.`id` =1;


mysql_query($order);


?>
