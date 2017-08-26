<?php
	require('functions.php');
	/*pushlink('Dit is de titel.', 'Dit is tekst.
En dit is een
nieuwe
regel.', 'reddit.com');sdfsdf*/
	/*$time = time();
	$formatted = date('H:i.s \(D\)', $time);
	echo $formatted;*/
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	echo $actual_link;
	