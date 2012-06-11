<!DOCTYPE html>
<html>
<body>

<?php 
require_once("steam/SteamAPI.php");
 ?>

<h1>Steam API Test</h1>

<?php
	$api = new SteamAPI();

	if(substr(php_sapi_name(), 0, 3) == "cli")
		$userID = $argv[1]; //Pull ID from CLI args
	else if(substr(php_sapi_name(), 0, 3) == "cli")
		$userID = $_GET["id"]; // Pull it from the CGI (or CGI-FCGI) args
	else
		$userID = NULL; //Some other PHP SAPI

	$user = $api->getUser($userID);

	if($user != NULL) {
		$fl = $user->getFriendsList();

		for($i = 0; $i < count($fl); $i++) {
			$friendUser = $api->getUser($fl[$i]->steamid);
			echo "Friend Steam ID: " . $friendUser->steamID64 . "\n<br />";
		}
	}
?>


</body>
</html>
