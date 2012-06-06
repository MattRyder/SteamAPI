<!DOCTYPE html>
<html>
<body>

<?php 
require_once("steam/SteamAPI.php");
 ?>

<h1>Steam API Test</h1>

<?php
	$api = new SteamAPI();

	$user = $api->getUser($_GET["id"]);

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
