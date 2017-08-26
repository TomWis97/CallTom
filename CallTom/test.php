<?php
/*	$test = array (
		"hoi"  => array(),
		"doei"  => array()
	);
	
	$test['hoi']['banaan'] = "geel";
	$test['hoi']['appel'] = "groen";
	$test['doei']['lol'] = "hihi";
	$test['doei']['ping'] = "pong";
	
	$testing = <<<EOD
<h1>Hoi</h1>
Een banaan is {$test['hoi']['banaan']}<br>
Een appel is {$test['hoi']['appel']}<br>

<h1>Doei</h1>
{$test['doei']['lol']}<br>
{$test['doei']['ping']}
EOD;
echo $testing;
test();


function test() {
	echo "test";
}*/

require("functions.php");

print_r($_COOKIE);
echo("<br>");

if(isset($_POST['set'])) {
	echo("Setting cookie.<br>");
	createcookie("Test");
}

if(isset($_POST['del'])) {
	echo("Deleting cookie.<br>");
	deletecookie();
}

if(isset($_POST['reload'])) {
	echo("Just reloading.<br>");
}

if(isset($_POST['autoreload'])) {
	echo("Automatic reload.");
	header("Refresh:0");
}

?>
<form method="post">
<input type="submit" name="set" value="set">
<input type="submit" name="del" value="del">
<input type="submit" name="reload" value="reload">
<input type="checkbox" name="autoreload">Automatic reload.
</form>
<?php
	$id = uniqid('u_');
	echo('ID: ' . $id . '<br>Length:' . strlen($id));