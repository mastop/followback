<?php
error_reporting(0);
include("header.php");

$path_to_library = 'twitter_connect/';
include($path_to_library . 'include/webzone.php');

if (isset($_POST['key']) && isset($_POST['secret']) && isset($_POST['callback'])) {

    $config = new Config();
    $config->setConfig($_POST['key'], $_POST['secret'], $_POST['callback']);
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
    <div id="info" class="container">
        <div class="row mg-top">
            <div id="avatar" class="span2">
                <img class="profile-img" src=" <?php echo $profile_image_url; ?>" >
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
                        <li class="active"><a href="#search" data-toggle="tab"><i class="icon-search"></i>&nbsp;Search</a></li>
                        <li><a href="#unfollow" data-toggle="tab"><i class="icon-user"></i>&nbsp;Unfollow</a></li>
                    </ul>
                    <div id="myTabContent" class="tab-content content">
                        <div class="tab-pane fade in active" id="search">
                            <form method=get action="twitter_connect/connect/connect.php">
                                <div class="row-fluid">
                                    <div class="span2">
                                        Texto:
                                    </div>
                                </div>
                                <div class="row-fluid field-main">
                                    <input class="input-xlarge" type="text" name="hashtag" />
                                </div>
                                <div class="row-fluid">
                                    Número de páginas:
                                </div>
                                <div class="row-fluid field-main">
                                    <select class="span2" name="pg">
                                        <option>1</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
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
                                    <input type="checkbox" value="1" name="follow" />
                                    Seguir
                                </div>
                                <div class="row-fluid"><input type="checkbox" value="1" name="twfollow" />
                                    Seguir usuários citados nos tweets
                                </div>
                                <div class="row-fluid"><input type="checkbox" value="1" name="mastop" checked="checked" />
                                    Seguir a Mastop
                                </div>
                                <div class="row-fluid pad-top">
                                    <div class="span3">
                                    <input type="submit" class="btn btn-primary" value=" Buscar ">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="unfollow">
                            <form method=get action="twitter_connect/connect/connect.php">
                                <div class="row-fluid field-main">
                                    <div class="span3">Unfollow em:</div>
                                </div>
                                <div class="row-fluid field-main">
                                        <select class="span2" name="limit">
                                            <option>10</option>
                                            <option>20</option>
                                            <option>30</option>
                                            <option>40</option>
                                            <option>50</option>
                                        </select>
                                </div>
                                <div class="row-fluid field-main">
                                    <div class="span3">
                                        <input type="submit" class="btn btn-primary" value=" OK " />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
} elseif ((!isset($_COOKIE['KEY'])) && (!isset($_COOKIE['SECRET'])) && (!isset($_COOKIE['CALLBACK']))) {

    echo '<style>
           .form{
                width: 450px; 
                height: 40px;
            }
            .field{
                width: 100px; 
                float: left;
            }
          </style>';
    echo '<form name="inicio" method="post" action="' . $_SERVER['PHP_SELF'] . '">';
    echo '<div>
                <h2>ConfiguraÃ§Ãµes Iniciais</h2>
              </div>';
    echo '<div>
                <div class="form">
                    <div class="field"><label>Key</label></div>
                    <input type="text" name="key" style="width: 300px;"/>
                </div>
                <div class="form">
                    <div class="field"><label>Secret</label></div>
                    <input type="text" name="secret" style="width: 300px;"/>
                </div>
                <div class="form">
                    <div class="field"><label>Callback</label></div>
                    <input type="text" name="callback" style="width: 300px;"/>
                </div>
              </div>';
    echo '<div class="form">
                <input type=submit value="Salvar" />
              </div>';
    echo '</form>';
} else {

    echo '<h3>Clique no link abaixo para se conectar ao Twitter</h3>';
    echo '<p>';
    echo '<a href="' . $path_to_library . 'connect/connect.php">Conectar com o Twitter</a>';
    echo '</p>';
}

include("footer.php")
?>