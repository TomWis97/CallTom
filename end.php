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
			$retxt = '"I\'ll be there ASAP!"';
			$reclass = "asap";
			break;
		case 3:
			$retxt = '"I\'ll be there within 5 minutes."';
			$reclass = "min";
			break;
		case 4:
			$retxt = '"Other reply."';
			$reclass = "other";
			break;
		default:
			die('Invalid reply code !!1');
	}
	
	
	pageheader(0, 'Reply received');
?>
<p>The reply is displayed below. If there's a message attached, it will also be displayed here.</p>
<p class="title">Reply:</p>
<div class="statusmsg <?php echo($reclass);?>"><?php echo('<p>' . $retxt . '</p>'); ?></div>
<?php
	if($msg != "") {
		echo('<p class="title">Attached message:</p>
		<p class="remsg">' . $msg . '</p>');
	}
	pagefooter();
?>
