<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Form Edit Data</title>
</head>

<body>
<table border=1>
  <tr>
    <td align=center>Form Edit Employees Data</td>
  </tr>
  <tr>
    <td>
      <table>
      <?php
      include "db.php";//database connection
      $id=$_GET['id'];
      $order = "SELECT * FROM notifications where id='$id'";
      $result = mysql_query($order);
      $row = mysql_fetch_array($result);
      ?>
      <form method="post" action="edit_data.php">
      <input type="hidden" name="id" value="<?php echo "$row[id]"?>">
        <tr>        
          <td>Notification View status id</td>
          <td>
            <input type="text" name="notification_view_status_id" 
        size="20" value="<?php echo "$row[notification_view_status_id]"?>">
          </td>
        </tr>
        <tr>

        </tr>

         <tr>        
          <td>Notification Status ID</td>
          <td>
            <input type="text" name="name" 
        size="20" value="<?php echo "$row[notification_status_id]"?>">
          </td>
        </tr>
        <tr>

        </tr>



        <tr>
          <td align="right">
            <input type="submit" 
          name="submit value" value="Edit">
          </td>
        </tr>
      </form>
      </table>
    </td>
  </tr>
</table>
</body>
</html>