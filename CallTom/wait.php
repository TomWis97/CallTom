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
	
	// Request response.
	$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	$st_getresponse = $db->prepare('SELECT * FROM calls WHERE callid = :callid');
	$st_getresponse->bindParam(':callid', $_GET['callid']);
	$st_getresponse->execute();
	$response = $st_getresponse->fetchAll();
	
	// Notifying if no response for 27 seconds.
	$timeago = time() - $response[0]['time'];
	if(($timeago > 27) && ($response[0]['reminded'] == 0) && ($response[0]['response'] == 0)) {
		push($response[0]['deviceid'], $response[0]['callid'], $response[0]['type'], $response[0]['time'], $response[0]['message'], true);
		$st_setreminded = $db->prepare('UPDATE calls SET reminded = 1 WHERE callid = :cid');
		$st_setreminded->bindParam(':cid', $_GET['callid']);
		$st_setreminded->execute();
	}
	
	switch($response[0]['response']) {
		case 0:
			$statusclass = "noreply";
			$statusmsg = "(Nog) geen reactie.";
			break;
		case 1:
			$statusclass = "seen";
			$statusmsg = "Gezien. Nog geen antwoord.";
			break;
		default:
			header("Location: end.php?callid=" . $_GET['callid']);
			die('Redirecting...');
	}
	
	pageheader(3, "Wachten op een reactie");
?>
<p>Even wachten op een reactie...</p>
<p class="title">Status:</p>
<div class="statusmsg <?php echo($statusclass);?>"dfdf>
<p><?php echo($statusmsg); ?></p>
</div>
<div class="spinner">...</div>
<?php pagefooter(); ?>