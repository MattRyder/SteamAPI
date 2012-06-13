<?php
/**
* SteamGame - Representation of any Steam title on the Store
*
* @category   SteamAPI
* @copyright  Copyright (c) 2012 Matt Ryder (www.mattryder.co.uk)
* @license    GPLv2 License
* @version    v1.0
* @link       https://github.com/MattRyder/SteamAPI/blob/master/steam/SteamGame.php
* @since      Class available since v1.0
*/
class SteamGame {

	private $appID;

	/**
	* Constructor
	* @param $appID: Game Application ID
	*/
	function __construct($appID) {
		$this->appID = $appID;
	}

	/**
	* GetNewsItems - Gets the latest news posts about the SteamGame
	* @param $newsItemCount: How many news enties you want to get returned.
	* @param $maxLength: Maximum length of each news article	 
	*/
	function getNewsItems($newsItemCount = 3, $maxLength = 300) {

		if($newsItemCount == NULL)
			$newsItemCount = 3;
		if($maxLength == NULL)
			$maxLength = 300;

		if(!empty($this->appID)) {

			$base = "http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/"
			      . "?appid={$this->appID}&count={$newsItemCount}&maxlength={$maxLength}&format=xml";

			$newsData = new SimpleXMLElement(file_get_contents($base));

			$i = 0;
			$this->gameNews = array();
			foreach ($newsData->newsitems->newsitem as $item) {
				$this->gameNews[$i]->gid 			 = (string) $item->gid;
				$this->gameNews[$i]->title 			 = (string) $item->title;
				$this->gameNews[$i]->url 			 = (string) $item->url;
				$this->gameNews[$i]->is_external_url = (string) $item->is_external_url;
				$this->gameNews[$i]->author 		 = (string) $item->author;
				$this->gameNews[$i]->contents 		 = (string) $item->contents;
				$this->gameNews[$i]->feedlabel		 = (string) $item->feedlabel;
				$this->gameNews[$i]->date 			 = (string) $item->date;
				$this->gameNews[$i]->feedname 		 = (string) $item->feedname;
				$i++;
			}

			return $this->gameNews;
		}
	}
}

?>