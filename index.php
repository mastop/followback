<?php
error_reporting(0);
include("header.php");
$path_to_library = 'twitter_connect/';
include($path_to_library . 'include/webzone.php');

if (isset($_POST['key']) && isset($_POST['secret'])) {

    $config = new Config();
    $config->setConfig($_POST['key'], $_POST['secret']);
    header('Location: ' . $_SESSION['PHP_SELF']);
}

$twitter = new Twitter_class();
if ($twitter->isConnected()) {

    //get Twitter login data (token, token secret, user id and display name)
    $login_data = $twitter->getLoginData();
    $user_id = $login_data['user_id'];
    $screen_name = $login_data['screen_name'];

    //get the user's Twitter information inside a "$data" object
    $data = $twitter->getUserData(array('user_id' => $user_id));
    $description = $data->description;
    $profile_url = 'http://twitter.com/' . $screen_name;
    $url = $data->url;
    $profile_image_url = $data->profile_image_url;
    $name = $data->name;
    $statuses_count = $data->statuses_count;
    $friends_count = $data->friends_count;
    $followers_count = $data->followers_count;
    $users = $twitter->countNonFollowers($login_data['screen_name']);
    $limits = $twitter->getLimit();
    ?>
<form method=POST action="<?php echo $_SERVER['PHP_SELF'];?>" class="form-horizontal">
<div id="info" class="container">
    <div class="row mg-top">
        <div id="avatar" class="span2">
            <img class="profile-img" src=" <?php echo $profile_image_url; ?>">
        </div>
        <div class="row">
            <div class="span3">
                <h4><?php echo $name; ?></h4>
                <a href=" <?php echo $profile_url ?>" target="_blank"><?php echo $profile_url; ?></a>
            </div>
        </div>
    </div>
    <div class="row-fluid left">
        <div class="span1 left">
            <b><?php echo $statuses_count ?></b>
            Tweets
        </div>
        <div class="span1">
            <b><?php echo $friends_count ?></b> Seguindo
        </div>
        <div class="span1">
            <b><?php echo $followers_count ?></b> Seguidores
        </div>
    </div>
    <div id="fluidGridSystem" class="row-fluid show-grid bar">
        <div id="fllw" class="span2">
            Não Seguidores: <b><?php echo $users ?></b>
        </div>
        <div class="span3">
            Limite por hora: <b><?php echo $limits->hourly_limit ?></b>
        </div>
        <div class="span3">
            Reset: <b><?php echo date('d/m/Y H:i:s', $limits->reset_time_in_seconds) ?></b>
        </div>
        <div class="span2">
            Requisições: <b><?php echo $limits->remaining_hits ?></b>
        </div>
    </div>
</div>
<div class="container mg-top">
    <div id="main" class="container-fluid no-padding">
        <div class="row-fluid tab-content main">
            <div id="main-menu" class="span5">
                <ul id="myTab" class="nav nav-tabs content-tab">
                    <li class="active"><a href="#search" data-toggle="tab"><i class="icon-search"></i>&nbsp;Search</a>
                    </li>
                    <li><a href="#unfollow" data-toggle="tab"><i class="icon-user"></i>&nbsp;Unfollow</a></li>
                </ul>
                <div id="myTabContent" class="tab-content content">
                    <div class="tab-pane fade in active" id="search">
                        <div class="row-fluid">
                            <div class="span2">
                                Texto:
                            </div>
                        </div>
                        <div class="row-fluid field-main">
                            <input class="input-xlarge" type="text" name="hashtag"/>
                        </div>
                        <div class="row-fluid">
                            Registros por Página:
                        </div>
                        <div class="row-fluid field-main">
                            <select class="span2" name="rpp">
                                <option>50</option>
                                <option>100</option>
                                <option>150</option>
                                <option>200</option>
                                <option>250</option>
                            </select>
                        </div>
                        <div class="row-fluid">
                            <input type="checkbox" value="1" name="follow"/>
                            Seguir
                        </div>
                        <div class="row-fluid"><input type="checkbox" value="1" name="twfollow"/>
                            Seguir usuários citados nos tweets
                        </div>
                        <div class="row-fluid">
                            <input type="checkbox" value="1" name="mastop" checked="checked"/>
                            Seguir a Mastop
                        </div>
                        <div class="row-fluid pad-top">
                            <div class="offset3">
                                <input type="submit" class="btn btn-primary" value=" Buscar "/>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="unfollow">
                        <div class="row-fluid field-main">
                            <div class="span3">Unfollow em:</div>
                        </div>
                        <div class="row-fluid field-main">
                            <select class="span2" name="unfollow">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="row-fluid field-main">
                            <div class="offset3">
                                <input type="submit" class="btn btn-primary" value=" OK "/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="main-result" class="span7">
                <?php
                if (isset($_POST['hashtag'])) {
                    $hashtag = $_POST['hashtag'];
                    $p = 1;
                    $rpp = (!empty($_POST['rpp'])) ? $_POST['rpp'] : 100;
                    $twitter = new Twitter_class();
                    $result = $twitter->searchTweetsDetails($hashtag, $p, $rpp);
                    //print_r($result->results);
                    $res = $twitter->jaSigo("MastopInternet");
                    if (isset($_POST['mastop']) && ($res == false)) {
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
                                $avatar [] = '<a href="http://www.twitter.com/' . $v->from_user . '" target="_blank"><img class="avatar" src="' . $v->profile_image_url . '"></a>';
                                $tweets[] = '@' . $v->from_user . " - " . utf8_decode(str_replace($hashtag, "<b>$hashtag</b>", $v->text));
                            }
                            if (!empty($_POST['twfollow'])) {
                                preg_match_all($pattern, $v->text, $matches);
                                if (!empty($matches[0])) {
                                    foreach ($matches[0] as $tw) {
                                        $twUsr = substr($tw, 1);
                                        if (!in_array($twUsr, $verificado)) {
                                            $sigoTw = $twitter->jaSigo($twUsr);
                                            if (!$sigoTw) {
                                                $seguir[] = $twUsr;
                                                $verificado[] = $twUsr;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($_POST['follow']) && count($seguir) > 0) {
                            $seguir = array_unique($seguir);
                            echo '<div id="bar" class="alert alert-info">Voce seguiu <strong> ' . count($seguir) . ' </strong> pessoas. </div>';
                            $rdm_avatar = array_rand(array_unique($avatar), 8);
                            $rdm_tweets = array_rand(array_unique($tweets), 5);
                            foreach ($seguir as $v) {
                                //$seg = $twitter->follow($v);
                                sleep(2);
                            }
                            echo '<div id="no-border" class="well">';
                            foreach ($rdm_avatar as $a) {
                                echo $avatar[$a];
                            }
                            echo '</div>'; //Class well
                            echo '<table class="table table-bordered">';
                            foreach ($rdm_tweets as $t) {
                                echo "<tr>";
                                echo '<td class="twitter-anywhere-user">';
                                echo $tweets[$t] . '<br />';
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo '<tr>';
                            echo '<td>';
                            echo '<input type="submit" class="btn btn-info" value=" Buscar Novamente " />';
                            echo '</td>';
                            echo '</tr>';
                            echo '</table>';
                        }
                    }
                }
                if (isset($_POST['unfollow'])) {
                    $twitter = new Twitter_class();
                    $login_data = $twitter->getLoginData();
                    $nonfollowers = $twitter->getNonFollowers($login_data['screen_name'], $limit);
                    $ret = '';
                    if ($nonfollowers) {
                        foreach ($nonfollowers as $u) {
                            $twitter->unfollow($u->screen_name);
                            $ret .= '<a href="http://twitter.com/' . $u->screen_name . '" title="' . $u->screen_name . ' (' . $u->followers_count . ')" style="margin:5px"><img src="' . $u->profile_image_url . '" width="40" style="padding:2px;"/></a> ';
                        }
                        echo 'Você deixou de seguir ' . count($nonfollowers) . ' usuários.';
                        echo $ret;
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
</form>
<?php
} elseif ((!isset($_COOKIE['KEY'])) && (!isset($_COOKIE['SECRET']))) {
    ?>
<form method=POST action="<?php echo $_SERVER['PHP_SELF'];?>" class="form-horizontal">
    <div class="container-fluid">
        <div id="config" class="span7 offset4 connect">
            <div class="row mg-top">
                <div class="span4 offset2">
                    <h2>Configurações Iniciais</h2>
                </div>
            </div>
            <div class="row mg-top">
                <div class="span1 offset1">
                    <label>Key</label>
                </div>
                <div class="span4">
                    <div class="input-append">
                        <input class="input-append" type="text" name="key"/>
                        <span class="add-on"><i class="icon-info-sign"></i></span>
                    </div>
                </div>
            </div>
            <div class="row mg-top">
                <div class="span1 offset1">
                    <label>Secret</label>
                </div>
                <div class="span4">
                    <div class="input-append">
                        <input class="input-append" type="text" name="secret"/>
                        <span class="add-on "><i class="icon-info-sign"></i></span>
                    </div>
                </div>
            </div>
            <div class="row mg-top">
                <div class="span4 offset6">
                    <input class="btn btn-primary" type=submit value="Salvar"/>
                </div>
            </div>
        </div>
        <div class="span6">
            <img src="layout/img/people.png" class="people"/>
        </div>
    </div>
</form>
<?php
} else {
    ?>
<div class="container-fluid">
    <div id="connect" class="span5 offset4 connect">
        <div id="mg-connect" class="row content-tab">
            <div class="span3 offset1">
                <a class="btn btn-large btn-primary" href="<?php echo $path_to_library . "connect/connect.php"; ?>">Conectar com o Twitter!</a>
            </div>
        </div>
    </div>
    <div class="span6 offset2">
        <img src="layout/img/people.png" class="people"/>
    </div>
</div>
<?php
}
?>
<?php
include("footer.php")
?>