<?php

// Create connection
$con=mysqli_connect("localhost","arduino01","AAarduino01","meteostanice01");

// Check connection
if (mysqli_connect_errno())
{
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$tabulka = (string)$_GET['tabulka'];
switch ($tabulka)
{
    case "Koupelna":
        $teplota = (float)$_GET['teplota'];
        $vlhkost = (float)$_GET['vlhkost'];
        $sql = "INSERT INTO $tabulka (Date, Teplota, Vlhkost) VALUES (NOW(),$teplota,$vlhkost)";
        if(mysqli_query($con, $sql)){
            echo "Records added successfully.";
            echo date("H:i:s");
            echo $tabulka;
            echo $teplota;
            echo $vlhkost;
        }
        break;
        
    case "Akvarium":
        $teplota = (float)$_GET['teplota'];
        $kyslik = (float)$_GET['kyslik'];
        $sql = "INSERT INTO $tabulka (Date, Teplota, Kyslik) VALUES (NOW(),$teplota,$kyslik)";
        if(mysqli_query($con, $sql)){
            echo "Records added successfully.";
            echo date("H:i:s");
            echo $tabulka;
        }
        break;    
        
}
mysqli_close($con);
?>