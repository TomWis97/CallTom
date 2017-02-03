<?php
	require("functions.php");
	if(checkdevice()) {
		header("Location: new.php");
		die('Redirecting...');
	}
?>