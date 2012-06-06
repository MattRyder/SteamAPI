SteamAPI
========

SteamAPI is a PHP API for interacting with Valve's Steam Community.

SteamAPI offers the ability to pull entire player profiles, including most played games, Steam Ratings, and groups subscribed to.

Overview
--------

It's simple to integrate SteamAPI into your web application!

To access user profiles, create the required SteamAPI object, and then create a SteamUser object for the target user:

    <?php 

    require("steam/SteamAPI.php");

    $api = new SteamAPI();

    $user = $api->getUser($vanityURL);
    print_r($user); //Or whatever you want to do with it! :)

    ?>