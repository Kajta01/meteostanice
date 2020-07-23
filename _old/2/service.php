<?php

// Create connection
$con=mysqli_connect("192.168.2.81","leccos","M08DEK7f","Meteostanice");

// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$tepl = (float)$_GET['hodnota'];
if($tepl != 0)
{
$sql = "INSERT INTO teplota (hodnota) VALUES ($tepl)";
if(mysqli_query($con, $sql)){
    echo "Records added successfully.";
    echo date("H:i:s");
    echo $_GET['teplota'];
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
}
}




// This SQL statement selects ALL from the table 'Locations'
$sql = "SELECT * FROM teplota";

// Check if there are results
if ($result = mysqli_query($con, $sql))
{
	// If so, then create a results array and a temporary one
	// to hold the data
	$resultArray = array();
	$tempArray = array();

	// Loop through each row in the result set
	while($row = $result->fetch_object())
	{
		// Add each row into our results array
		$tempArray = $row;
	    array_push($resultArray, $tempArray);
	}

	// Finally, encode the array to JSON and output the results
	echo json_encode($resultArray), PHP_EOL;
}

// Close connections
mysqli_close($con);
?>
