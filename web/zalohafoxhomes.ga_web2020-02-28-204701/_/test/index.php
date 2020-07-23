<?php include 'retrieve.php';  ?>

<html>
<head>
	<title>Meteo</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
	
</head>
<body>
	<div class = "page">
		<div class = "title"> <h2> Meteorologická stanice Kajta </h2> </div>
		
		<div class = "datum" > <?php echo Dneska(); ?></div>
		<div class = "datum" > Obyvák
    		<div class = "hodnoty">
    			<div class = "values" > Teplota: <div id = "hodnota"> <?php echo ObyvakTeplota(); ?> °C</div> </div>
    	
    			<div class = "values" > Vlhkost: <div id = "hodnota"> <?php echo ObyvakVlhkost(); ?> %</div> </div>
    			
    			<div class = "values" > Tlak: <div id = "hodnota"> - </div></div>
    			
    			<div class = "values" > Znečištění: <div id = "hodnota"> - </div> </div>
    		</div>
    		<div class = "datum" > Data jsou stará  <span id = "hodnota"><?php echo ObyvakDataOld(); ?></span></div >
		</div >
		
		<div class = "datum" > Lázně
    		<div class = "hodnoty">
    			<div class = "values" > Teplota: <div id = "hodnota"> <?php echo KoupelnaTeplota(); ?> °C</div> </div>
    	
    			<div class = "values" > Vlhkost: <div id = "hodnota"> <?php echo KoupelnaVlhkost(); ?> %</div> </div>
    			

    		</div>
    		<div class = "datum" > Data jsou stará <span id = "hodnota"><?php echo KoupelnaDataOld() ;?></span> </div >
		</div >
		
		<div class = "datum" > Odpočívadlo
    		<div class = "hodnoty">
    			<div class = "values" > Teplota: <div id = "hodnota"> <?php echo LozniceTeplota(); ?> °C</div> </div>
    	
    			<div class = "values" > Vlhkost: <div id = "hodnota"> <?php echo LozniceVlhkost(); ?> %</div> </div>

    		</div>
    		<div class = "datum" > Data jsou stará  <span id = "hodnota"><?php echo LozniceDataOld() ;?></span></div >
		</div >
		<div class = "datum" > Tůňka
    		<div class = "hodnoty">
    			<div class = "values" > Teplota: <div id = "hodnota"> <?php echo AkvariumTeplota(); ?> °C</div> </div>
    	
    			<div class = "values" > Kyslík: <div id = "hodnota"> <?php echo AkvariumKyslik(); ?> %</div> </div>

    		</div>
    		<div class = "datum" > Data jsou stará  <span id = "hodnota"><?php echo AkvariumDataOld() ;?></span></div >
		</div >
		
		
		
		
		<div class = "datum" ><img src="/_/test/image/face-cat.png" > 
			<p> <?php  echo  catApi() ; ?></p> </div>
		
	

	
</body>
</html>
