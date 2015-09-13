<?php
libxml_use_internal_errors(true);

// Use SteamUtility to fetch URLs and other stuff
require_once 'SteamUtility.php';

/**
* SteamApps - Used to query Steam Apps in general
*
* @category   SteamAPI
* @copyright  Copyright (c) 2015 Matt Ryder (www.mattryder.co.uk)
* @license    GPLv2 License
* @version    v1.3
* @link       https://github.com/MattRyder/SteamAPI/blob/master/steam/SteamUser.php
* @since      Class available since v1.3
*/
class SteamApps {

  const API_BASE = "http://api.steampowered.com/ISteamApps/";
  private $apiKey;

  /**
   * Constructor
   * @param string $apiKey  Steam Community API key
   */
  function __construct($apiKey = null) {
    if (!is_null($apiKey)) {
      $this->apiKey = $apiKey;
    }
  }

  /**
   * WebAPI/GetAppList
   * - Returns a list of Apps, with their ID and Name set
   * @param boolean $reload Whether to miss cache and reload data
   */
  function getAppList($reload = false) {
    $apiAction = "GetAppList/v2/?format=xml";

    // Valve highly recommend caching this, it's quite a big GET
    if(isset($this->appList) && !$reload) {
      return $this->appList;
    } else {
      $this->appList = array();
    }

    $apiUrl = self::API_BASE . $apiAction;
    $parsedData = SteamUtility::fetchDataFromUrl($apiUrl);

    if(!empty($parsedData)) {
      $i = 0;
      foreach ($parsedData->apps[0] as $app) {
        $this->appList[$i] = new stdClass();
        $this->appList[$i]->appId = (string)$app->appid;
        $this->appList[$i]->name = (string)$app->name;
        $i++;
      }
    }
    return $this->appList;
  }

  /**
   * WebAPI/GetServersAtAddress
   * - Returns a list of Servers active for the given IP Address
   */
  function getServersAtAddress($serverAddress) {
    $apiAction = "GetServersAtAddress/v1?format=xml";
    $apiParams = "&addr=" . $serverAddress;

    if(empty($serverAddress)) {
      echo "Error: No server address given!", PHP_EOL;
      return NULL;
    }

    $this->serverList = array();

    $apiUrl = self::API_BASE . $apiAction . $apiParams;
    $parsedData = SteamUtility::fetchDataFromUrl($apiUrl);

    if(!empty($parsedData)) {
      $i = 0;
      foreach ($parsedData->servers->server as $server) {
        $this->serverList[$i] = new stdClass();
        $this->serverList[$i]->address = (string)$server->addr;
        $this->serverList[$i]->gmsindex = (string)$server->gmsindex;
        $this->serverList[$i]->appId = (string)$server->appid;
        $this->serverList[$i]->gameDir = (string)$server->gamedir;
        $this->serverList[$i]->region = (string)$server->region;
        $this->serverList[$i]->secure = (string)$server->secure;
        $this->serverList[$i]->lan = (string)$server->lan;
        $this->serverList[$i]->gamePort = (string)$server->gameport;
        $this->serverList[$i]->specPort = (string)$server->specPort;
        $i++;
      }
    }
    return $this->serverList;
  }

  /**
   * WebAPI/UpToDateCheck
   * - Checks if the given version of the app is up to date
   * @param string appId    The ID of which app to check
   * @param string version  The version that you want to check
   */
  function upToDateCheck($appId, $version) {
    $apiAction = "UpToDateCheck/v1?format=xml";

    if(empty($appId)) {
      echo "Error: No server address given!", PHP_EOL;
      return NULL;
    }
    if(empty($version)) {
      echo "Error: No version supplied!", PHP_EOL;
      return NULL;
    }

    // Set the params:
    $apiParams = "&appid=" . $appId;
    $apiParams .= "&version=" . $version;

    $this->versionCheck = new stdClass();

    $apiUrl = self::API_BASE . $apiAction . $apiParams;
    $parsedData = SteamUtility::fetchDataFromUrl($apiUrl);

    if(!empty($parsedData) && (boolean)$parsedData->success) {
      $this->versionCheck->upToDate = (string)$parsedData->up_to_date;
      $this->versionCheck->isListable = (string)$parsedData->version_is_listable;
      $this->versionCheck->requiredVersion = (string)$parsedData->required_version;
      $this->versionCheck->message = (string)$parsedData->message;
    }
    return $this->versionCheck;
  }
}

?>