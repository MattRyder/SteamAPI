<!DOCTYPE html>
<html>
<body>

<?php 
require_once("steam/SteamAPI.php");
 ?>

<h1>Steam API Test </h1>

<?php


	//Handler for building this API from Terminal CLI:
	if(substr(php_sapi_name(), 0, 3) == "cli")
		$userID = $argv[1]; //Pull ID from CLI args
	else
		$userID = $_GET["id"]; // Pull it from the apache / CGI / whatever

	//Call the SteamUser constructor with either the 17-digit Steam Community ID
    //or their custom URL (i.e. robinwalker)
    $api = new SteamAPI();
	$user = $api->getUser($userID);

	$foo = $user->getFriendsList();
	print_r($foo);

/*
	if($user != NULL) {

		$foo = $user->getGamesList();

		printf("%-6s\t%-50s\t%-15s%s", "App ID", "Game Name", "Hours On Record", PHP_EOL);

		for($i = 0; $i < count($foo); $i++) {
			printf("%-7s\t%-50s\t%-2.1f%s", $foo[$i]->appID, $foo[$i]->name, $foo[$i]->hoursOnRecord, PHP_EOL);
		}
    }*/
?>


</body>
</html>
