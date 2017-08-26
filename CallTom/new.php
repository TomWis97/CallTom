<?php
	/*   Documentatie: Cause
		1 = Voedsel
		2 = IT hulp
		3 = Contact gewenst
	*/
	/*   Documentatie: Response
		0 = Geen response
		1 = Geopend
		2 = ASAP
		3 = Binnen 5 minuten
		4 = Anders
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
	pageheader(0, "Reden kiezen")
?>
<p>Geef de reden aan door op de knop te drukken. Eventueel kan er tekst worden toegevoegd (doe dit eerst).</p>
<form method="post">
	<button class="submitbutton" type="submit" name="cause" value="1">Eten/drinken</button>
	<button class="submitbutton" type="submit" name="cause" value="2">IT hulp</button>
	<button class="submitbutton" type="submit" name="cause" value="3">Contact gewenst</button>
	<p class="title">Extra tekst (Optioneel)</p>
	<input type="text" name="text">
</form>
<?php pagefooter(); ?>