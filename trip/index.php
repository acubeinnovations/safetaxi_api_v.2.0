
<table>
  <tr>
    <td align="center">EDIT DATA</td>
  </tr>
  <tr>
    <td>
      <table border="1">
      <?php
      include"db.php";//database connection
      $order = "SELECT * FROM notifications";
      $result = mysql_query($order);
      while ($row=mysql_fetch_array($result)){
        echo ("<tr><td>$row[notification_type_id]</td>");
        echo ("<td>$row[notification_status_id]</td>");
        echo ("<td>$row[notification_view_status_id]</td>");
        echo ("<td>$row[app_key]</td>");
        echo ("<td>$row[trip_id]</td>");
        echo ("<td>$row[message]</td>");
        echo ("<td>$row[user_id]</td>");
        echo ("<td>$row[created]</td>");
        echo ("<td>$row[updated]</td>");
        echo ("<td><a href=\"edit_form.php?id=$row[id]\">Edit</a></td></tr>");
      }
      ?>
      </table>
