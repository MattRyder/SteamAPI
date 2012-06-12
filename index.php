<!DOCTYPE html>
<html>
<body>

<?php 
require_once("steam/SteamAPI.php");
 ?>

<h1>Steam API Test</h1>

<?php
	$api = new SteamAPI();

	//Handler for building this API from Terminal CLI:
	if(substr(php_sapi_name(), 0, 3) == "cli")
		$userID = $argv[1]; //Pull ID from CLI args
	else
		$userID = $_GET["id"]; // Pull it from the apache / CGI / whatever

	$user = $api->getUser($userID);
?>


</body>
</html>
