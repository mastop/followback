<?php

error_reporting(0);
include ("../include/webzone.php");
$connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET']);
//$callback = $_SERVER['DOCUMENT_ROOT'] . "/followback/twitter_connect/connect/callback.php"
$callback = "http://server/guilherme/followback/twitter_connect/connect/callback.php";
$tok = $connection->getRequestToken($callback);
$_SESSION['twitter_oauth_token'] = $tok['oauth_token'];
$_SESSION['twitter_oauth_token_secret'] = $tok['oauth_token_secret'];
$request_link = $connection->getAuthorizeURL($tok['oauth_token']);
//save referer to redirect to it once connected
$_SESSION['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
header('Location: ' . $request_link);
?>