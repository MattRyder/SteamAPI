<?php

include_once("SteamUser.php");

class SteamAPI {
	
	function __construct() { }

	function getUser($id) {
		$this->user = new SteamUser($id);
		return $this->user;
	}


}

?>