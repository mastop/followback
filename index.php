<?php
error_reporting(0);
if (isset($_GET['PT'])) {
    setcookie('LANG', "PT", time() + (60 * 60 * 24 * 365));
    header("location:" . $_SERVER["PHP_SELF"]);
}elseif (isset($_GET['ENG'])) {
    setcookie('LANG', "ENG", time() + (60 * 60 * 24 * 365));
    header("location:" . $_SERVER["PHP_SELF"]);
}
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
                <a id="url" href=" <?php echo $profile_url ?>" target="_blank"><?php echo $profile_url; ?></a>
            </div>
        </div>
    </div>
    <div class="row left">
        <div class="span1 left">
            <b><?php echo $statuses_count ?></b>
            <?php echo TWEETS; ?>
        </div>
        <div class="span1">
            <b><?php echo $friends_count ?></b>
            <?php echo FOLLOWING; ?>
        </div>
        <div class="span1">
            <b><?php echo $followers_count ?></b>
            <?php echo FOLLOWERS; ?>
        </div>
    </div>
    <div class="row-fluid show-grid bar">
        <div id="fllw" class="span2">
            <?php echo NO_FOLLOWERS; ?>: <b><?php echo $users ?></b>
        </div>
        <div class="span3">
            <?php echo HOURLY_LIMIT; ?>: <b><?php echo $limits->hourly_limit ?></b>
        </div>
        <div class="span3">
            <?php echo RESET; ?>: <b><?php echo date($date, $limits->reset_time_in_seconds) ?></b>
        </div>
        <div class="span2">
            <?php echo REQUESTS; ?>: <b><?php echo 350 - $limits->remaining_hits ?></b>
        </div>
    </div>
</div>
<div class="container mg-top">
    <div id="main" class="container-fluid no-padding">
        <div class="row-fluid tab-content main">
            <div id="main-menu" class="span5">
                <ul id="myTab" class="nav nav-tabs content-tab">
                    <li class="active">
                        <a id="title-search" href="#search" data-toggle="tab">
                            <i class="icon-search"></i>&nbsp;<?php echo SEARCH; ?>
                        </a>
                    </li>
                    <li>
                        <a id="title-unfollow" href="#unfollow" data-toggle="tab">
                            <i class="icon-user"></i>&nbsp;<?php echo UNFOLLOW_TAG; ?>
                        </a>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content content">
                    <div class="tab-pane fade in active" id="search">
                        <div class="row-fluid mg-top">
                            <div class="span2">
                                <?php echo TEXT; ?>:
                            </div>
                        </div>
                        <div class="row-fluid mg-top">
                            <input class="input-xlarge" type="text" name="hashtag"/>
                        </div>
                        <div class="row-fluid mg-top">
                            <?php echo RECORDS_PER_PAGE; ?>:
                        </div>
                        <div class="row-fluid mg-top">
                            <select class="span2" name="rpp">
                                <option>50</option>
                                <option>100</option>
                                <option>150</option>
                                <option>200</option>
                                <option>250</option>
                            </select>
                        </div>
                        <div class="row-fluid mg-top">
                            <input type="checkbox" value="1" name="follow"/>
                            <?php echo FOLLOW; ?>
                        </div>
                        <div class="row-fluid mg-top">
                            <input type="checkbox" value="1" name="twfollow"/>
                            <?php echo MENTIONED; ?>
                        </div>
                        <div class="row-fluid mg-top">
                            <input type="checkbox" value="1" name="mastop" checked="checked"/>
                            <?php echo FOLLOW_MASTOP; ?>
                        </div>
                        <div class="row-fluid pad-top">
                            <div class="offset3">
                                <input id="mg-botton" type="submit" class="btn btn-primary"
                                       value=" <?php echo SEARCH_BUTTON; ?> "/>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="unfollow">
                        <div class="row-fluid mg-top">
                            <div class="span4"><?php echo UNFOLLOW; ?>:</div>
                        </div>
                        <div class="row-fluid mg-top">
                            <select class="span2" name="unfollow">
                                <option value="0"></option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="row-fluid">
                            <div class="offset3">
                                <input type="submit" class="btn btn-primary mg-left" value=" OK "/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="main-result" class="span7">
                <?php
                //Hashtag Search
                if (isset($_POST['hashtag']) && isset($_POST['follow'])) {
                    $hashtag = $_POST['hashtag'];
                    if (isset($_POST['page'])) {
                        $p = $_POST['page'];
                    } else {
                        $p = 1;
                    }
                    $rpp = (!empty($_POST['rpp'])) ? $_POST['rpp'] : 100;
                    $twitter = new Twitter_class();
                    $result = $twitter->searchTweetsDetails($hashtag, $p, $rpp);
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
                                $avatar [] = '<a href="http://www.twitter.com/' . $v->from_user . '" target="_blank"><img class="avatar" src="' . $v->profile_image_url . '" title="@' . $v->from_user . '" /></a>';
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
                        if (count($seguir) > 0) {
                            $seguir = array_unique($seguir);
                            echo '<div id="bar" class="alert alert-info">' . NEW_FOLLOWING . '<strong> ' . count($seguir) . ' </strong> ' . PEOPLE . ' . </div>'; //Traduzir
                            $rdm_avatar = array_rand(array_unique($avatar), 8);
                            $rdm_tweets = array_rand(array_unique($tweets), 5);
                            foreach ($seguir as $v) {
                                $seg = $twitter->follow($v);
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
                                echo utf8_encode($tweets[$t]) . '<br />';
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo '<tr>';
                            echo '<td>';
                            echo '<input type="submit" class="btn btn-info" value="' . AGAIN . '" />';
                            echo '<input type="hidden" name="hashtag" value="' . $hashtag . '" />';
                            $p = $p + 1;
                            echo '<input type="hidden" name="page" value="' . $p . '" />';
                            echo '<input type="hidden" name="follow" value="1" />';
                            if (!empty($_POST['twfollow'])) {
                                echo '<input type="hidden" name="twfollow" value="1" />';
                            }
                            echo '</td>';
                            echo '</tr>';
                            echo '</table>';
                        }
                    }
                }
                //Unfollow
                if (isset($_POST['unfollow'])) {
                    if ($_POST['unfollow'] != "0") {
                        $limit = $_POST['unfollow'];
                        $twitter = new Twitter_class();
                        $login_data = $twitter->getLoginData();
                        $nonfollowers = $twitter->getNonFollowers($login_data['screen_name'], $limit);
                        $ret = '';
                        if ($nonfollowers) {
                            foreach ($nonfollowers as $u) {
                                $twitter->unfollow($u->screen_name);
                                $ret .= '<a href="http://twitter.com/' . $u->screen_name . '" title="@' . $u->screen_name . '" style="margin:5px"><img src="' . $u->profile_image_url . '" width="40" style="padding:2px;"/></a> ';
                            }
                            echo '<div id="bar" class="alert alert-info">' . LEAVE_FOLLOW . ' <strong> ' . count($nonfollowers) . ' </strong>' . PEOPLE . '.  </div>';
                            echo '<div id="no-border" class="well">';
                            echo $ret;
                            echo '</div>';
                        }
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
    <div class="container">
        <div class="row">
            <div class="span8 offset2 connect config">
                <div class="row mg-top">
                    <div class="span4 offset2">
                        <h2><?php echo SETTINGS; ?></h2>
                    </div>
                </div>
                <div class="row mg-top">
                    <div class="span2 offset1">
                        <label>Consumer Key</label>
                    </div>
                    <div class="span4">
                        <input class="input-large" type="text" name="key"/>
                    </div>
                </div>
                <div class="row mg-top">
                    <div class="span2 offset1">
                        <label>Consumer Secret</label>
                    </div>
                    <div class="span4">
                        <input class="input-large" type="text" name="secret"/>
                    </div>
                </div>
                <div class="row mg-top">
                    <div id="btnsave" class="span4">
                        <input class="btn btn-primary" type="submit" value="<?php echo SAVE; ?>"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
} else {
    ?>
<div class="container">
    <div class="row content-tab">
        <div class="span8 offset2 connect config">
            <div class="row">
                <div class="span4 offset2">
                    <a id="connect" class="btn btn-primary btn-large"
                       href="<?php echo $path_to_library . "connect/connect.php"; ?>"><?php echo CONNECT; ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
include("footer.php")
?>