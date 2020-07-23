<?php
	$servername = 'localhost'; // Server Name
	$username = 'arduino01'; // Username
	$password = 'AAarduino01'; // Password
	$dbName = 'meteostanice01'; // Database Name
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    
    $conn = new mysqli($servername, $username, $password, $dbName);
    //checking if there were any error during the last connection attempt
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    //the SQL query to be executed
    $query = "SELECT * FROM teplota order by datum desc";
    //storing the result of the executed query
    $result = $conn->query($query);
    
    
     //initialize the array to store the processed data
    $jsonArray = array();
    //check if there is any data returned by the SQL Query
    if ($result->num_rows > 0) {
      //Converting the results into an associative array
      while($row = $result->fetch_assoc()) {
        $jsonArrayItem = array();
        $jsonArrayItem['datum'] = $row['datum'];
        $jsonArrayItem['value'] = $row['teplota'];
        //append the above created object into the main array.
        array_push($jsonArray, $jsonArrayItem);
      }
    }
    
    //Closing the connection to DB
    $conn->close();
    //set the response content type as JSON
    header('Content-type: application/json');
    //output the return value of json encode using the echo function.
    echo json_encode($jsonArray);
?>