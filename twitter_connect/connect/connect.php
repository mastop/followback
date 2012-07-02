<?php

error_reporting(0);
include ("../include/webzone.php");
if (isset($_GET['hashtag'])) {
    $hashtag = $_GET['hashtag'];
    $p = (!empty($_GET['p'])) ? $_GET['p'] : 1;
    $rpp = (!empty($_GET['rpp'])) ? $_GET['rpp'] : 100;
    $twitter = new Twitter_class();
    $result = $twitter->searchTweetsDetails($hashtag, $p, $rpp);
    //print_r($result->results);
    if (isset($_GET['mastop'])) {
        $twitter->follow("MastopInternet");
    }
    if (is_array($result->results)) {
        $seguir = array();
        $verificado = array();
        $pattern = '/\@[a-z0-9_]+/i';
        foreach ($result->results as $k => $v) {
            if (!in_array($v->from_user, $verificado)) {
                $sigo = $twitter->jaSigo($v->from_user);
                $verificado[] = $v->from_user;
            } else {
                $sigo = (in_array($v->from_user, $seguir)) ? false : true;
            }
            if (!$sigo) {
                $seguir[] = $v->from_user;
                $st = '<strong style="color:red">N</strong>';
            } else {
                $st = '<strong style="color:green">S</strong>';
            }
            echo ($k + 1) . ": " . $st . " <==> " . $v->from_user . " (" . $v->from_user_id . " - " . date("d/m/y H:i:s", strtotime($v->created_at)) . ") " . utf8_decode(str_replace($hashtag, "<b>$hashtag</b>", $v->text)) . "<hr />";
            if (!empty($_GET['twfollow'])) {
                preg_match_all($pattern, $v->text, $matches);
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $tw) {
                        $twUsr = substr($tw, 1);
                        if (!in_array($twUsr, $verificado)) {
                            $sigoTw = $twitter->jaSigo($twUsr);
                            if (!$sigoTw)
                                $seguir[] = $twUsr;
                            $verificado[] = $twUsr;
                        }
                    }
                }
            }
        }
        if (!empty($_GET['follow']) && count($seguir) > 0) {
            $seguir = array_unique($seguir);
            foreach ($seguir as $v) {
                $seg = $twitter->follow($v);
                //print_r($seg); 
                sleep(2);
            }
            echo '<h1>' . count($seguir) . ' Usuários foram seguidos:</h1>';
            echo '<h2>' . implode('<br />', $seguir) . '<h2>';
            //print_r($seg);
        }
    }
    echo '<a href="' . $_SERVER['HTTP_REFERER'] . '">Back</a>';
} elseif (isset($_GET['unfollow'])) {
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 50;
    $twitter = new Twitter_class();
    $login_data = $twitter->getLoginData();
    $nonfollowers = $twitter->getNonFollowers($login_data['screen_name'], $limit);
    $ret = '';
    if ($nonfollowers) {
        foreach ($nonfollowers as $u) {
            $twitter->unfollow($u->screen_name);
            $ret .= '<a href="http://twitter.com/' . $u->screen_name . '" title="' . $u->screen_name . ' (' . $u->followers_count . ')" style="margin:5px"><img src="' . $u->profile_image_url . '" width="40" style="padding:2px;"/></a> ';
        }
        echo count($nonfollowers) . ' usuários tomaram unfollow na cabeça.<br /><br />';
        echo $ret;
        echo '<br /><a href="' . $_SERVER['HTTP_REFERER'] . '">Voltar</a>';
        echo '<br /><hr /><br />';
        //echo '<pre>'.print_r($nonfollowers, true).'</pre>';
    } else {
        echo '<h1>Erro! Volte mais tarde</h1>';
    }
} elseif ($_GET['logout'] == 1) {
    unset($_SESSION['twitter_access_token']);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET']);
    $tok = $connection->getRequestToken($_COOKIE['CALLBACK']);
    //exit(var_dump($tok));
    $_SESSION['twitter_oauth_token'] = $tok['oauth_token'];
    $_SESSION['twitter_oauth_token_secret'] = $tok['oauth_token_secret'];
    $request_link = $connection->getAuthorizeURL($tok['oauth_token']);
    //save referer to redirect to it once connected
    $_SESSION['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
    header('Location: ' . $request_link);
}
echo '<br /><br />';
$twitter = new Twitter_class();
$limits = $twitter->getLimit();
echo 'Limite da API por hora: <b>' . $limits->hourly_limit . '</b><br />';
echo 'Próximo reset: <b>' . date('d/m/Y H:i:s', $limits->reset_time_in_seconds) . '</b><br />';
echo 'Requisições restantes: <b>' . $limits->remaining_hits . '</b><br />';
?>