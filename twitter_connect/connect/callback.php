<?php
include("../include/webzone.php");

$oauth_token = $_GET['oauth_token'];
$oauth_verifier = $_GET['oauth_verifier'];

//If the oauth_token is old redirect to the connect page
if (isset($oauth_token) && $_SESSION['twitter_oauth_token'] != $oauth_token) {
  	header('Location: '.$_SESSION['HTTP_REFERER']);
}

//Create TwitteroAuth object with app key/secret and token key/secret from default phase
$connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret']);

//Request access tokens from twitter
$access_token = $connection->getAccessToken($oauth_verifier);

//Save the access tokens
$_SESSION['twitter_access_token'] = $access_token;

//Remove no longer needed request tokens
unset($_SESSION['twitter_oauth_token']);
unset($_SESSION['twitter_oauth_token_secret']);

header('Location: '.$_SESSION['HTTP_REFERER']);

/*
//If HTTP response is 200 continue otherwise send to connect page to retry
if (200 == $connection->http_code) {
  	//The user has been verified and the access tokens can be saved for future use
	header('Location: validation.php');
}
else {
  	//Save HTTP status for error dialog on connnect page.
  	header('Location: '.$_SESSION['HTTP_REFERER']);
}
*/

?>