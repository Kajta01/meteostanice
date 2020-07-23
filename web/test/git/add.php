<?php
   
   // Recieve data from the station and add them to the database
   include("connect.php");
   $link=Connection();

   if(isset($_POST["temp"]) && isset($_POST["pres"]) && isset($_POST["hum"]) && isset($_POST["pass"])){
      if($_POST["pass"] == '*****'){
   	   $temp = $_POST["temp"];
         $pres = $_POST["pres"];
   	   $hum = $_POST["hum"]; 	
         mysql_query("INSERT INTO `senzory2` (`pressure`, `temperature`, `humidity`) 
         VALUES ('".parseInt($pres)."','".parseInt($temp)."','".parseInt($hum)."')", $link);
      }
   }

   $row = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM minmax WHERE  DATE(date) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))", $link));
  
   // Insert yesterdays min, max and average values to the minmax table
   if($row[0] == "0"){
      mysql_query(
         "INSERT INTO minmax (date, tmin, tmax, pressure) VALUES(
            (SELECT DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))),
            (SELECT MIN(temperature) FROM senzory2 WHERE DATE(time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))),
            (SELECT MAX(temperature) FROM senzory2 WHERE DATE(time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))),
            (SELECT AVG(pressure) FROM senzory2 WHERE DATE(time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)))
         )", $link
      );
   }

   mysql_close($link);

   // Prevent from inserting other than numerical values into database
   function parseInt($data){
      $int = (int) preg_replace('/[^0-9]/', '', $data);
      return $int;
   }

?>

