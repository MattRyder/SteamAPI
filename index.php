<!DOCTYPE html>
<html>
<body>

<?php require("steam/SteamAPI.php"); ?>

<h1>Steam API Test</h1>

<?php
	$api = new SteamAPI();

	$user = $api->getUser($_GET["id"]);
	print_r($user); //DEBUG
?>


</body>
</html>
