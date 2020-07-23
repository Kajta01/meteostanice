<?php
// Create connection
$conn=mysqli_connect("localhost","arduino01","AAarduino01","meteostanice01");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$tabulka = (string)$_GET['tabulka'];

$sql    = "SELECT * FROM $tabulka order by Date DESC LIMIT 200";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        
        $row = str_replace(".",",",$row);

        $rows[] = $row;
    }

    echo json_encode($rows);
} else {
    echo "no results found";
}

mysqli_close($conn);
?>