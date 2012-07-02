<?php

/**
 * @file
 * A single location to store configuration.
 */
//define("CONSUMER_KEY", "IFpB3VAAsFDXkGIfPTF9w");
//define("CONSUMER_SECRET", "qICDam4fA5Llj0Yup5G0HLDmbTMIAWO1SuSfLTlVhw");
//define("OAUTH_CALLBACK", "http://server/fernando/twitter_connect/twitter_connect/connect/callback.php");

Class Config {

    public function setConfig($key, $secret, $callback) {

        //Cookies timeout, 1 year
        setcookie('KEY', $key, time() + (60 * 60 * 24 * 365));
        setcookie('SECRET', $secret, time() + (60 * 60 * 24 * 365));
        setcookie('CALLBACK', $callback, time() + (60 * 60 * 24 * 365));
    
        
    }

}