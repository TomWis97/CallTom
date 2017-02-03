<?php
	require("settings.php");
	
	function checkdevice() {
		// Function for checking the device ID sent in cookie. Can be used at the beginning of each page.
		if (isset($_COOKIE['call_devid'])) {
			if(validatedeviceid($_COOKIE['call_devid']) == false) {
				deletecookie();
				die("Error: device ID doesn\'t exist in database!<br>Sent ID: {$_COOKIE['call_devid']}");
			}
			return true;
		}
		else {
			header("Location: setup.php");
			die('Redirecting...');
		}
		exit();
	}
	
	function validateuserid($userid) {
		// Function for checking a userid. It checks if the ID exsists in the database. If it does, it returns TRUE. Otherwise, it returns FALSE.
		
		// Making database variables available from function.
		global $dbpdodsn;
		global $dbuser;
		global $dbpassword;
		
		$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$st_validateuserid = $db->prepare('SELECT * FROM users WHERE userid = :uid');
		$st_validateuserid->bindParam(':uid', $userid);
		$st_validateuserid->execute();
		// Return false if there are no returning rows.
		if($st_validateuserid->rowCount() == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	function validatedeviceid($deviceid) {
		// Function for checking a deviceid. It checks if the ID exists in the database. If it does, it returns TRUE. Otherwise, it returns FALSE.
		
		// Making database variables available from function.
		global $dbpdodsn;
		global $dbuser;
		global $dbpassword;
		
		$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$st_validatecallid = $db->prepare('SELECT * FROM devices WHERE deviceid = :did');
		$st_validatecallid->bindParam(':did', $deviceid);
		$st_validatecallid->execute();
		// Return false if there are no returning rows.
		if($st_validatecallid->rowCount() == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	function validatecallid($deviceid) {
		// Function for checking a callid. It checks if the ID exists in the database. If it does, it returns TRUE. Otherwise, it returns FALSE.
		
		// Making database variables available from function.
		global $dbpdodsn;
		global $dbuser;
		global $dbpassword;
		
		$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$st_validatecallid = $db->prepare('SELECT * FROM calls WHERE callid = :cid');
		$st_validatecallid->bindParam(':cid', $deviceid);
		$st_validatecallid->execute();
		// Return false if there are no returning rows.
		if($st_validatecallid->rowCount() == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	function createcookie($deviceid) {
		$cookiename = "call_devid";
		$cookievalue = $deviceid;
		$cookieexpire = time() + 31536000;
		setcookie($cookiename, $cookievalue, $cookieexpire, "/");
	}
	
	function deletecookie() {
		$cookiename = "call_devid";
		$cookievalue = $_COOKIE[$cookiename];
		$cookieexpire = time() - 3600;
		setcookie($cookiename, $cookievalue, $cookieexpire, "/");
	}
	
	function push($deviceid, $callid, $typeid, $time, $msg, $remind) {
		global $webroot;
		$names = getnames($deviceid);
		$username = $names[0];
		$devicename = $names[1];
		$sender = "$username ($devicename)";
		$formattedtime = date('H:i.s \(D\)', $time);
		$type = converttypetostring($typeid);
		if(empty($msg)) {
			$newmsg = "*No message*";
		}
		else {
			$newmsg = $msg;
		}
		$body = "Message: $newmsg
By: $sender
Time: $formattedtime";
		$url = $webroot . 'reply.php?id=' . $callid;
		$title = "Called! $username: $type";
		if($remind == true) {
			$title = "Reminder: " . $title;
		}
		pushlink($title, $body, $url);
	}
	
	function getnames($deviceid) {
		global $dbpdodsn;
		global $dbuser;
		global $dbpassword;
		// This function gets the username and devicename from the deviceid.
		$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$st_getnames = $db->prepare('SELECT u.username, d.devicename FROM devices d, users u WHERE d.userid = u.userid AND d.deviceid = :did');
		$st_getnames->bindParam(':did', $deviceid);
		$st_getnames->execute();
		$names = $st_getnames->fetchAll();
		$username = $names[0]['username'];
		$devicename = $names[0]['devicename'];
		return array($username, $devicename);
	}
	
	function getcalldata($callid) {
		global $dbpdodsn;
		global $dbuser;
		global $dbpassword;
		$db = new PDO($dbpdodsn, $dbuser, $dbpassword, array(PDO::ATTR_EMULATE_PREPARES => false,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$st_getcalldata = $db->prepare('SELECT * FROM calls WHERE callid = :cid');
		$st_getcalldata->bindparam(':cid', $callid);
		$st_getcalldata->execute();
		$calldata = $st_getcalldata->fetchAll();
		$call = $calldata[0];
		return $call;
	}
	
	function pushlink($title, $body, $url) {
		global $pbapikey;
		$curl = curl_init('https://api.pushbullet.com/v2/pushes');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: Bearer $pbapikey"]);
		curl_setopt($curl, CURLOPT_POSTFIELDS, ["type" => "link", "title" => $title, "body" => $body, "url" => $url]);
		curl_exec($curl);
	}
	
	function converttypetostring($type) {
		switch($type) {
			case 1:
				return "Food";
				break;
			case 2:
				return "IT Help";
				break;
			case 3:
                //TODO: Find better translation. Dutch: "Contact gewenst"
				return "Talk to us!";
				break;
			default:
				return false;
		}
	}
	
	function pageheader($refreshtime, $page) {
		// All variables should be set.
		// $refreshtime  = If set, refresh page automatically every x seconds. Set to 0 to disable.
		// $page         = Set the page title.
		
		// Setting variables for header
		// Automatic refreshing.
		global $headertitle;
		
		if ($refreshtime == 0) {
			$header_refresh = "<!-- No automatic refreshing on this page. -->";
		}
		else {
			$header_refresh = "<meta http-equiv=\"refresh\" content=\"$refreshtime\">";
		}
		$header_title = $headertitle . " ($page)";
		$header = <<<EOD
<!DOCTYPE html>
<html>
	<head>
		<title>$header_title</title>
		<meta charset="utf-8">
		<link href="style.css" rel="stylesheet" type="text/css">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		$header_refresh
	</head>
	<body>
		<div id="container">
			<div id="header">
				<span id="headertitle">$headertitle</span>
				<span id="headerpage">$page</span>
			</div>
			<div id="main">
EOD;
		echo($header);
	}
	function pagefooter() {
		$footer = <<<EOD
			</div>
		</div>
	</body>
</html>
EOD;
		echo($footer);
	}

	/* Creation of tables in MySQL:
CREATE TABLE `users` (
   `userid` varchar(15) NOT NULL COMMENT 'u_$uniqid',
   `username` varchar(30) NOT NULL,
   PRIMARY KEY (userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `devices` (
   `deviceid` varchar(15) NOT NULL COMMENT 'd_$uniqid',
   `userid` varchar(15) NOT NULL,
   `devicename` varchar(30) NOT NULL,
   PRIMARY KEY (deviceid),
   FOREIGN KEY (userid) REFERENCES users(userid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `calls` (
	`callid` varchar(15) NOT NULL COMMENT 'c_$uniqid',
	`deviceid` varchar(15) NOT NULL,
	`type` int(1) NOT NULL,
	`time` int(11) NOT NULL,
	`response` int(1) NOT NULL,
	`reminded` int(1) NOT NULL,
	`message` text,
	`remsg` text,
	PRIMARY KEY (callid),
	FOREIGN KEY (deviceid) REFERENCES devices(deviceid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;*/
	?>
