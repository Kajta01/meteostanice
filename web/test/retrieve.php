<?php
	// retrieve data from mysql database

	header("Content-type: text/javascript");
	include("connect.php");

	$link=Connection();


	if( isset($_REQUEST["type"]) ){

	   $type = $_REQUEST['type'];
	   switch ($type) {

		    case "c":
		    	$result=mysql_query("SELECT * FROM teplota ORDER BY datum DESC LIMIT 1", $link);
		        echo json_encode(mysql_fetch_row($result));
		        break;

		}
		mysql_free_result($result);
	}

	mysql_close();

	// make array from mysql table rows
	function mysqlData($result){
		$array = array();
		while($row = mysql_fetch_row($result)){
			$array[] = $row;
   		}
	   	return $array;
	}




?>
