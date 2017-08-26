<?php
	require('functions.php');
	checkdevice();
	if(!isset($_GET['callid'])) {
		die('Error!');
	} else {
		if(validatecallid($_GET['callid']) == false) {
			die('Invalid callid!');
		}
	}
	$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$st_getresponse = $db->prepare('SELECT * FROM calls WHERE callid = :callid');
	$st_getresponse->bindParam(':callid', $_GET['callid']);
	$st_getresponse->execute();
	$response = $st_getresponse->fetchAll();
	$reply = $response[0]['response'];
	$msg = $response[0]['remsg'];
	switch($reply) {
		case 2:
			$retxt = '"Ik kom er zo snel mogelijk aan!"';
			$reclass = "asap";
			break;
		case 3:
			$retxt = '"Ik kom er binnen 5 minuten aan!"';
			$reclass = "min";
			break;
		case 4:
			$retxt = '"Andere reactie."';
			$reclass = "other";
			break;
		default:
			die('Huh?! Dit snap ik niet!111');
	}
	
	
	pageheader(0, 'Antwoord ontvangen');
?>
<p>Hieronder staat de reactie. Als er een bericht bij is gestuurd, staat deze er ook bij.</p>
<p class="title">Reactie:</p>
<div class="statusmsg <?php echo($reclass);?>"><?php echo('<p>' . $retxt . '</p>'); ?></div>
<?php
	if($msg != "") {
		echo('<p class="title">Toegevoegd bericht:</p>
		<p class="remsg">' . $msg . '</p>');
	}
	pagefooter();
?>