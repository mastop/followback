<?php

Class Config {

    public function setConfig($key, $secret) {

        //Cookies timeout, 1 year
        setcookie('KEY', $key, time() + (60 * 60 * 24 * 365));
        setcookie('SECRET', $secret, time() + (60 * 60 * 24 * 365));
    }

}