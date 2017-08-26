<?php
	require('functions.php');
	//checkdevice();
	
	echo($_COOKIE['call_devid'] . "<br>");
	
	if(validatedeviceid($_COOKIE['call_devid']) == true) {
		echo("True!");
	}
	
	if(validatedeviceid($_COOKIE['call_devid']) == false) {
		echo("False.");
	}
	
?>
<br>Stuff!