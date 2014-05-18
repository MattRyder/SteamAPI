<?php
// Use SteamUtility to fetch URLs and other stuff
require_once 'SteamUtility.php';

/**
* SteamGame - Representation of any Steam title on the Store
*
* @category   SteamAPI
* @copyright  Copyright (c) 2012 Matt Ryder (www.mattryder.co.uk)
* @license    GPLv2 License
* @version    v1.3
* @link       https://github.com/MattRyder/SteamAPI/blob/master/steam/SteamGame.php
* @since      Class available since v1.0
*/
class SteamGame {

	private $appID;
	private $apiKey;

	/**
	* Constructor
	* @param int    $appID    Game Application ID
	* @param string $apiKey   API key for http://steamcommunity.com/dev/
	*/
	function __construct($appID, $apiKey = null) {

		$this->appID = $appID;
		if (!is_null($apiKey)) {
			$this->apiKey = $apiKey;
		}
	}

	/**
	* GetNewsItems - Gets the latest news posts about the SteamGame
	* @param $newsItemCount: How many news enties you want to get returned.
	* @param $maxLength: Maximum length of each news article
	* @return $gameNews: Array containing news entries.
	*/
	function getNewsItems($newsItemCount = 3, $maxLength = 300) {

		if($newsItemCount == NULL)
			$newsItemCount = 3;
		if($maxLength == NULL)
			$maxLength = 300;

		if(!empty($this->appID)) {

			$base = "http://api.steampowered.com/ISteamNews/GetNewsForApp/v0002/"
			      . "?appid={$this->appID}&count={$newsItemCount}&maxlength={$maxLength}&format=xml";

			$newsData = new SimpleXMLElement(SteamUtility::fetchURL($base));

			$i = 0;
      foreach ($newsData->newsitems->newsitem as $item) {
        $this->gameNews = array(
          'gid'             => (string) $item->gid,
          'title'           => (string) $item->title,
          'url'             => (string) $item->url,
          'is_external_url' => (string)$item->is_external_url,
          'author'          => (string)$item->author,
          'contents'        => (string)$item->contents,
          'feedlabel'       => (string)$item->feedlabel,
          'date'            => (string)$item->date,
          'feedname'        => (string)$item->feedname
        );
			}

			return $this->gameNews;
		} else {
			return null;
		}
	}

	/**
	 * GetSchemaForGame - Loads schema for a selected game
	 * @param  string $lang Language for descriptions
	 * @return array        Schema as an associative array cointaining ['achievements'] and ['stats']
	 */
	function getSchema($lang = 'en') {
		if(!empty($this->appID)) {
			$base = "http://api.steampowered.com/ISteamUserStats/GetSchemaForGame/v0002/?key={$this->apiKey}&appid={$this->appID}&l={$lang}";
		}

		$json = SteamUtility::fetchURL($base);
		if(!$json) {
			return null;
		}

		$gameSchema = json_decode($json, true);

		if (!$gameSchema) {
			return null;
		}

		$this->gameSchema = array(
			'achievements' => $gameSchema['game']['availableGameStats']['achievements'],
			'stats'        => $gameSchema['game']['availableGameStats']['stats']
		);

		return $this->gameSchema;
	}

	/**
	 * Sets an API key for api.steampowered.com
	 * @param string $apiKey API key
	 */
	function setApiKey($apiKey) {
		$this->apiKey = $apiKey;
	}
}

?>
