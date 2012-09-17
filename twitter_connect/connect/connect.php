<?php
error_reporting(0);
include ("../include/webzone.php");
if (isset($_GET['logout'])) {
    unset($_SESSION['twitter_access_token']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET']);
    $callback = "http://" . $_SERVER['SERVER_NAME'] . "/twitter_connect/connect/callback.php";
    $tok = $connection->getRequestToken($callback);
    $_SESSION['twitter_oauth_token'] = $tok['oauth_token'];
    $_SESSION['twitter_oauth_token_secret'] = $tok['oauth_token_secret'];
    $request_link = $connection->getAuthorizeURL($tok['oauth_token']);
//save referer to redirect to it once connected
    $_SESSION['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
    header('Location: ' . $request_link);
}