<?php
	require('functions.php');
	if(!isset($_GET['id'])) {
		die('Error!');
	} else {
		if(validatecallid($_GET['id']) == false) {
			die('Invalid callid!');
		}
	}
	$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	
	if(isset($_POST['reply'])) {
		switch($_POST['reply']) {
			case 2:
				$response = 2;
				break;
			case 3:
				$response = 3;
				break;
			case 4:
				$response = 4;
				break;
			default:
				die("Invalid reply!");
		}
		$msg = htmlentities($_POST['text']);
		$st_sendreply = $db->prepare('UPDATE calls SET response = :resp, remsg = :msg WHERE callid = :cid');
		$st_sendreply->bindParam(':resp', $response);
		$st_sendreply->bindParam(':msg', $msg);
		$st_sendreply->bindParam(':cid', $_GET['id']);
		$st_sendreply->execute();
		die("<script>window.location.href = 'sent.php';</script>");
	}
	
	$st_callread = $db->prepare('UPDATE calls
SET response = 1
WHERE callid = :cid');
	$st_callread->bindParam(':cid', $_GET['id']);
	$st_callread->execute();
	
	// Retrieve call data
	$st_getcall = $db->prepare('SELECT *
FROM calls, users, devices
WHERE calls.deviceid = devices.deviceid
AND devices.userid = users.userid
AND calls.callid = :cid');
	$st_getcall->bindParam(':cid', $_GET['id']);
	$st_getcall->execute();
	// Return false if there are no returning rows.
	$callinfo = $st_getcall->fetchAll();
	
	pageheader(0, "Antwoorden");
?>
<p>Send your reply here.</p>
<p class="title">Details:</p>
<table>
	<tr>
		<th>Reason</th><td><?php echo(converttypetostring($callinfo[0]['type']));?></td>
	</tr>
	<tr>
		<th>Message</th><td><?php 
		$msg = $callinfo[0]['message'];
		if($msg == "") {
			$msg = "*No Message*";
		}
		echo($msg);
		?></td>
	</tr>
	<tr>	
		<th>Time</th><td><?php echo(date('j-n-Y G:i.s', $callinfo[0]['time'])); echo(" ("); echo(time() - $callinfo[0]['time']); echo (' sec. ago)')?></td>
	</tr>
	<tr>	
		<th>By</th><td><?php echo($callinfo[0]['username']); ?></td>
	</tr>
	<tr>	
		<th>From</th><td><?php echo($callinfo[0]['devicename']); ?></td>
	</tr>
</table>
<p class="title">Answer:</p>
<form method="post">
	<button class="submitbutton" type="submit" name="reply" value="2">ASAP</button>
	<button class="submitbutton" type="submit" name="reply" value="3">Within 5 minutes</button>
	<button class="submitbutton" type="submit" name="reply" value="4">Other</button>
	<p class="title">Extra message (Optional)</p>
	<input type="text" name="text">
</form>
