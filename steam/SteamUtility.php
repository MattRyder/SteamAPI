<?php

/**
* SteamUtility - Utilities used by other Steam classes
*
* @category   SteamAPI
* @copyright  Copyright (c) 2012 Matt Ryder (www.mattryder.co.uk)
* @license    GPLv2 License
* @version    v1.2
* @link       https://github.com/MattRyder/SteamAPI/blob/master/steam/SteamUtility.php
* @since      Class available since v1.2
*/
class SteamUtility {

	public static $connectTimeout = 2; // 2 seconds

	/**
	 * Performs a GET on the API and boxes the XML data.
	 * @param string $url Target URL
	 */
	public static function fetchDataFromUrl($url) {
		try {
			$content = SteamUtility::fetchURL($url);
			if ($content) {
				return new SimpleXMLElement($content);
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * Fetches content of a given URL via HTTP GET method.
	 * @param  string $url Target URL
	 * @return string      Response content or FALSE on error
	 */
	public static function fetchURL($url)
	{
		if (self::iniGetBool('allow_url_fopen'))
		{
			$ctx = stream_context_create(array(
				'http' => array(
					'timeout' => self::$connectTimeout
			)));
			return file_get_contents($url, false, $ctx);
		}
		elseif (function_exists('curl_init'))
		{
			$handle = curl_init();
			curl_setopt($handle, CURLOPT_URL, $url);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_TIMEOUT, self::$connectTimeout);
			return curl_exec($handle);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns boolean value of a php.ini setting
	 * @param  string  $ini_name Setting name
	 * @return boolean           Setting value
	 */
	private static function iniGetBool($ini_name) {
		$ini_value = ini_get($ini_name);

		switch (strtolower($ini_value))
		{
			case 'on':
			case 'yes':
			case 'true':
				return 'assert.active' !== $ini_name;

			case 'stdout':
			case 'stderr':
				return 'display_errors' === $ini_name;

			default:
				return (bool) (int) $ini_value;
		}
	}
}
