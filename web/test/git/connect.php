<?php

	// Connect to MySQL database
	function Connection(){
		$server="192.168.1.50";
		$user="jarda";
		$pass="*****";
		$db="weather";
	   	
		$connection = mysql_connect($server, $user, $pass);

		if (!$connection) {
	    	die('MySQL ERROR: ' . mysql_error());
		}
		
		mysql_select_db($db) or die( 'MySQL ERROR: '. mysql_error() );

		return $connection;
	}
?>
