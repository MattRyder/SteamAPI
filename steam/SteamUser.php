<?php
class SteamUser {
	
	private $userID;
	private $vanityURL;
	
	function __construct($id) {
		
		if(is_numeric($id)) {
			$this->userID = $id;
		} 
		else {
			$this->vanityURL = strtolower($id);
		}

		$this->getProfileData();
	}
	
	function getProfileData() {

		//Set Base URL for the query:
		if(empty($this->vanityURL)) {
			$base = "http://steamcommunity.com/profiles/{$this->userID}/?xml=1";
		} else {
			$base = "http://steamcommunity.com/id/{$this->vanityURL}/?xml=1";
		}

		try {
			$content = file_get_contents($base);
			if(!empty($content)) {
				$parsedData = new SimpleXMLElement($content);
			}
		} catch (Exception $e) {
			echo "Whoops! Something went wrong!\n\nException Info:\n" . $e . "\n\n";
			return;
		}	

		if(!empty($parsedData)) {
			$this->steamID64 = (string)$parsedData->steamID64;
			$this->steamID = (string)$parsedData->steamID;
			$this->stateMessage = (string)$parsedData->stateMessage;
			$this->visibilityState = (int)$parsedData->visibilityState;
			$this->privacyState = (string)$parsedData->privacyState;

			$this->avatarIcon = (string)$parsedData->avatarIcon;
			$this->avatarMedium = (string)$parsedData->avatarMedium;
			$this->avatarFull = (string)$parsedData->avatarFull;

			$this->vacBanned = (int)$parsedData->vacBanned;
			$this->tradeBanState = (string)$parsedData->tradeBanState;
			$this->isLimitedAccount = (string)$parsedData->isLimitedAccount;
			
			$this->onlineState = (string)$parsedData->onlineState;
			$this->inGameServerIP = (string)$parsedData->inGameServerIP;

			//If their account is public, get that info:
			if($this->privacyState == "public") {
				$this->customURL = (string)$parsedData->customURL;
				$this->memberSince = (string)$parsedData->memberSince;
				
				$this->steamRating = (float)$parsedData->steamRating;
				$this->hoursPlayed2Wk = (float)$parsedData->hoursPlayed2Wk;

				$this->headline = (string)$parsedData->headline;
				$this->location = (string)$parsedData->location;
				$this->realname = (string)$parsedData->realname;
				$this->summary = (string)$parsedData->summary;
			}

			//If they're in a game, grab that info:
			if($this->onlineState == "in-game") {
				$this->inGameInfo = array();
				$this->inGameInfo["gameName"] = (string)$parsedData->inGameInfo->gameName;
				$this->inGameInfo["gameLink"] = (string)$parsedData->inGameInfo->gameLink;
				$this->inGameInfo["gameIcon"] = (string)$parsedData->inGameInfo->gameIcon;
				$this->inGameInfo["gameLogo"] = (string)$parsedData->inGameInfo->gameLogo;
				$this->inGameInfo["gameLogoSmall"] = (string)$parsedData->inGameInfo->gameLogoSmall;
			}

			//Get their most played video games:
			if(!empty($parsedData->mostPlayedGames)) {
				$this->mostPlayedGames = array();

				$i = 0;
				foreach ($parsedData->mostPlayedGames->mostPlayedGame as $mostPlayedGame) {
					$this->mostPlayedGames[$i]->gameName = (string)$mostPlayedGame->gameName;
					$this->mostPlayedGames[$i]->gameLink = (string)$mostPlayedGame->gameLink;
					$this->mostPlayedGames[$i]->gameIcon = (string)$mostPlayedGame->gameIcon;
					$this->mostPlayedGames[$i]->gameLogo = (string)$mostPlayedGame->gameLogo;
					$this->mostPlayedGames[$i]->gameLogoSmall = (string)$mostPlayedGame->gameLogoSmall;
					$this->mostPlayedGames[$i]->hoursPlayed = (string)$mostPlayedGame->hoursPlayed;
					$this->mostPlayedGames[$i]->hoursOnRecord = (string)$mostPlayedGame->hoursOnRecord;
					$this->mostPlayedGames[$i]->statsName = (string)$mostPlayedGame->statsName;
					$i++;
				}
			}

			//Any weblinks listed in their profile:
			if(!empty($parsedData->weblinks)) {
				$this->weblinks = array();

				$i = 0;
				foreach ($parsedData->weblinks->weblink as $weblink) {
					$this->weblinks[$i]->title = (string)$weblink->title;
					$this->weblinks[$i]->link = (string)$weblink->link;
					$i++;
				}
			}

			//And grab any subscribed groups:
			if(!empty($parsedData->groups)) {
				$this->groups = array();

				$i = 0;
				foreach ($parsedData->groups->group as $group) {
					$this->groups[$i]->groupID64 = (string)$group->groupID64;
					$this->groups[$i]->groupName = (string)$group->groupName;
					$this->groups[$i]->groupURL = (string)$group->groupURL;
					$this->groups[$i]->headline = (string)$group->headline;
					$this->groups[$i]->summary = (string)$group->summary;

					$this->groups[$i]->avatarIcon = (string)$group->avatarIcon;
					$this->groups[$i]->avatarMedium = (string)$group->avatarMedium;
					$this->groups[$i]->avatarFull = (string)$group->avatarFull;

					$this->groups[$i]->memberCount = (string)$group->memberCount;
					$this->groups[$i]->membersInChat = (string)$group->membersInChat;
					$this->groups[$i]->membersInGame = (string)$group->membersInGame;
					$this->groups[$i]->membersOnline = (string)$group->membersOnline;

					$i++;
				}

			}
		}
	}

	function getFriendsList() {
		include_once("private/apikey.php");

		if(!empty($this->steamID64)) {
			//Setup URL to the steam API for the list:
			$baseURL = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/";
			$baseURL = $baseURL . "?key={$apikey}&steamid={$this->steamID64}&relationship=friend&format=xml";

			$parsedFL = new SimpleXMLElement(file_get_contents($baseURL));
			$this->friendList = array();

			$i = 0;
			foreach ($parsedFL->friends->friend as $friend) {
				$this->friendList[$i]->steamid = (string)$friend->steamid;
				$this->friendList[$i]->relationship = (string)$friend->relationship;
				$this->friendList[$i]->friend_since = (string)$friend->friend_since;
				$i++;
			}

			return $this->friendList;
		}
	}

	function convertToCommunityID() {

		if(!empty($this->steamID64)) {

			$Y = $this->steamID64 % 2; //Parity bit at end of 64-bit ID
			$Z = gmp_and($this->steamID64, "0xFFFFFFFF"); //Get the Account ID
			$Z = gmp_strval(gmp_div($Z, 2));

			return "STEAM_0:{$Y}:{$Z}";
		}

	}
}
?>