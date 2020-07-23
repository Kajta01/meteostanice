<?php

	// Connect to MySQL database
	function Connection(){
		$server="localhost";
		$user="arduino01";
		$pass="AAarduino01";
		$db="meteostanice01";

		$connection = mysql_connect($server, $user, $pass);

		if (!$connection) {
	    	die('MySQL ERROR: ' . mysql_error());
		}

		mysql_select_db($db) or die( 'MySQL ERROR: '. mysql_error() );

		return $connection;
	}
?>
