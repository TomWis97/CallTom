<?php
	/*   Documentation: Cause
		1 = Food
		2 = IT help
        //TODO: Again, better translation.
		3 = Talk to us!
	*/
	/*   Documentation: Response
		0 = No response
		1 = Read
		2 = ASAP
		3 = Within 5 minutes
		4 = Other
	*/
	require('functions.php');
	checkdevice();
	
	if(isset($_POST['cause'])) {
		$callid = uniqid('c_');
		$deviceid = $_COOKIE['call_devid'];
		$type = 0;
		switch($_POST['cause']) {
			case 1:
				$type = 1;
				break;
			case 2:
				$type = 2;
				break;
			case 3:
				$type = 3;
				break;
			default:
				die('Error: Invalid cause!');
		}
		$time = time();
		$response = 0;
		$message = htmlentities($_POST['text']);
		$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$st_addcall = $db->prepare('INSERT INTO calls (callid, deviceid, type, time, response, message, reminded) VALUES (:cid, :did, :ty, :ti, :re, :m, 0)');
		$st_addcall->bindParam(':cid', $callid);
		$st_addcall->bindParam(':did', $deviceid);
		$st_addcall->bindParam(':ty', $type);
		$st_addcall->bindParam(':ti', $time);
		$st_addcall->bindParam(':re', $response);
		$st_addcall->bindParam(':m', $message);
		$st_addcall->execute();
		push($deviceid, $callid, $type, $time, $message, false);
		header('Location: wait.php?callid=' . $callid);
		die('Redirecting...');
	}
	pageheader(0, "Choosing cause")
?>
<p>Enter the cause by pressing the corresponding button. If you want to add text (optional), enter your message before pressing the button.</p>
<form method="post">
	<button class="submitbutton" type="submit" name="cause" value="1">Food</button>
	<button class="submitbutton" type="submit" name="cause" value="2">IT help</button>
    <!-- TODO: Better translation. -->
	<button class="submitbutton" type="submit" name="cause" value="3">Contact gewenst</button>
	<p class="title">Extra tekst (Optioneel)</p>
	<input type="text" name="text">
</form>
<?php pagefooter(); ?>
