<?php
$db_host = 'localhost'; // Server Name
$db_user = 'arduino01'; // Username
$db_pass = 'AAarduino01'; // Password
$db_name = 'meteostanice01'; // Database Name
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
#######################################################################
function Data($tabulka, $sloupec)
{
    $sql = 'SELECT * FROM ' . $tabulka . '  order by Date desc limit 1';
	$conn = $GLOBALS['con'];
	$query = mysqli_query($conn, $sql);

	while ($row = mysqli_fetch_array($query))
		{
			return $row[''.$sloupec.''];
		}
}
########################################################################
function ObyvakTeplota()
{
    return Data('Obyvak','Teplota');
}
function ObyvakVlhkost()
{
    return Data('Obyvak','Vlhkost');
}

function ObyvakDataOld()
{
	$dat = Data('Obyvak','Date');
    $numberofsecs = round(abs(time()+ 60*60 -  strtotime($dat) ),2);
	convertDate($numberofsecs);	
}
####################################################################################
function LozniceTeplota()
{
    return Data('Loznice','Teplota');
}
function LozniceVlhkost()
{
    return Data('Loznice','Vlhkost');
}

function LozniceDataOld()
{
	$dat = Data('Loznice','Date');
    $numberofsecs = round(abs(time()+ 60*60 -  strtotime($dat) ),2);
	convertDate($numberofsecs);	
}
####################################################################################
function KoupelnaTeplota()
{
    return Data('Koupelna','Teplota');
}
function KoupelnaVlhkost()
{
    return Data('Koupelna','Vlhkost');
}

function KoupelnaDataOld()
{
	$dat = Data('Koupelna','Date');
    $numberofsecs = round(abs(time()+ 60*60 -  strtotime($dat) ),2);
	convertDate($numberofsecs);	
}
####################################################################################
function AkvariumTeplota()
{
    return Data('Akvarium','Teplota');
}
function AkvariumKyslik()
{
    return Data('Akvarium','Kyslik');
}

function AkvariumDataOld()
{
	$dat = Data('Akvarium','Date');
    $numberofsecs = round(abs(time()+ 60*60 -  strtotime($dat) ),2);
	convertDate($numberofsecs);	
}
####################################################################################

function convertDate($time)
{

	$days = floor($time / (60 * 60 * 24));
	$time -= $days * (60 * 60 * 24);

	$hours = floor($time / (60 * 60));
	$time -= $hours * (60 * 60);

	$minutes = floor($time / 60);
	$time -= $minutes * 60;

	$seconds = floor($time);
	$time -= $seconds;

	echo "{$days}d {$hours}h {$minutes}m {$seconds}s";
}
#####################################################################
function Dneska()
{
	echo datum()." ".svatekDnes();
}
function svatekDnes()
{
    $content =file_get_contents("https://api.abalin.net/get/today?country=cz");
    $result  = json_decode($content);
    return "Svátek má ".$result->data->name_cz;
}
function datum()
{
	return date("d. m. Y");
}


function catApi()
{
    $content =file_get_contents("https://cat-fact.herokuapp.com/facts/random?animal=cat&amount=1");
    $result  = json_decode($content);
    return  $result->text;
}

?>


