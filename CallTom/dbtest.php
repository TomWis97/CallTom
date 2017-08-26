<?php
	require("functions.php");
	
	if(isset($_POST['devicename']) && $_POST['devicename'] == "") {
		die('You need to enter a device name.');
	}
	
	// Creating database connection here, because it's needed anyway.
	$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	
	// Request existing users.
	$st_requestusers = $db->prepare('SELECT * FROM users');
	$st_requestusers->execute();
	if($st_requestusers->rowCount() > 0) {
		$currentusers = $st_requestusers->fetchAll();
	} else {
		$currentusers = 0;
	}
	
	// Code responsible for adding a new user.
	if(isset($_POST['username']) && (!empty($_POST['username']))) {
		// Make sure that html injection is kinda prevented.
		$username = htmlentities($_POST['username']);
		$userid = uniqid('u_');
		// Prepare statement.
		$st_useradd = $db->prepare('INSERT INTO users (userid, username) VALUES (:uid, :una)');
		// Bind parameters.
		$st_useradd->bindParam(':uid', $userid);
		$st_useradd->bindParam(':una', $username);
		// Execute statement.
		$st_useradd->execute();
		// Call function to add device.
		adddevice($userid, $_POST['devicename']);
	} elseif(isset($_POST['selecteduser'])) {
		$selusr = $_POST['selecteduser'];
		if($selusr == 'nousers') {
			die('No users in database. You need to enter a username.');
		}
		// No username given, using selected username.
		if(validateuserid($selusr) == FALSE) {
			die('Error!11');
		} 
		adddevice($selusr, $_POST['devicename']);
	}
	
	function adddevice($userid, $devicename) {
		global $db;
		$devicename = htmlentities($devicename);
		$deviceid = uniqid('d_');
		$st_deviceadd = $db->prepare('INSERT INTO devices (deviceid, userid, devicename) VALUES (:did, :uid, :dna)');
		$st_deviceadd->bindParam(':did', $deviceid);
		$st_deviceadd->bindParam(':uid', $userid);
		$st_deviceadd->bindParam(':dna', $devicename);
		$st_deviceadd->execute();
		createcookie($deviceid);
		header("Location: new.php");
	}
	
?>

<form method="post">
	<select name="selecteduser">
	<?php
		if($currentusers == 0) {
			echo('<option value="nousers">Geen users in database.</option>');
		} else {
			foreach($currentusers as $row) {
				echo("<option value=\"{$row['userid']}\">{$row['username']}</option>");
			}
		}
	?>
	</select>
	<input type="text" name="username" placeholder="Nieuwe gebruikersnaam" <?php if($currentusers == 0) { echo("required"); } ?>>
	<input type="text" name="devicename" placeholder="Apparaatnaam" required>
	<button id="submitbutton" type="submit" class="customButton">Voeg dit apparaat toe.</button>
</form>