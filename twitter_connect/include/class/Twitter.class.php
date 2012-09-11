<?php
class Twitter_class
{
    var $twitter_token;
    var $twitter_token_secret;

    function Twitter_class()
    {
        $tmp = $_SESSION['twitter_access_token'];
        $token = $tmp['oauth_token'];
        $token_secret = $tmp['oauth_token_secret'];
        $this->twitter_token = $token;
        $this->twitter_token_secret = $token_secret;
    }

    function getLoginData()
    {
        $tmp = $_SESSION['twitter_access_token'];
        $data['token'] = $tmp['oauth_token'];
        $data['token_secret'] = $tmp['oauth_token_secret'];
        $data['user_id'] = $tmp['user_id'];
        $data['screen_name'] = $tmp['screen_name'];
        return $data;
    }

    function isConnected()
    {
        if ($_SESSION['twitter_access_token'])
            return 1;
        else
            return 0;
    }

    function getUserData($criteria)
    {
        $user_id = $criteria['user_id'];
        $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $this->twitter_token, $this->twitter_token_secret);
        $content = $connection->get("users/show", $criteria);
        return $content;
    }

    function getFriends($criteria = '')
    {
        $user_id = $criteria['user_id'];
        $screen_name = $criteria['screen_name'];
        $cursor = $criteria['cursor'];
        if ($criteria['cursor'] == '')
            $criteria['cursor'] = '-1';
        $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $this->twitter_token, $this->twitter_token_secret);
        $content = $connection->get("statuses/friends", $criteria);
        $users['next_cursor'] = $content->next_cursor;
        $users['previous_cursor'] = $content->previous_cursor;
        $users['users'] = $this->formatUsersContent($content->users);
        return $users;
    }

    function formatTwitterUsers($users2)
    {
        for ($i = 0; $i < count($users2); $i++) {
            $id = $users2[$i]['user_id'];
            $name = $users2[$i]['screen_name'];
            $picture = $users2[$i]['profile_image_url'];
            $url = 'http://twitter.com/' . $users2[$i]['screen_name'];
            $users[$i]['id'] = $id;
            $users[$i]['name'] = $name;
            $users[$i]['picture'] = $picture;
            $users[$i]['url'] = $url;
        }
        return $users;
    }

    function getFollowers($criteria = '')
    {
        $user_id = $criteria['user_id'];
        $screen_name = $criteria['screen_name'];
        $cursor = $criteria['cursor'];
        if ($criteria['cursor'] == '')
            $criteria['cursor'] = '-1';
        $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $this->twitter_token, $this->twitter_token_secret);
        $content = $connection->get("statuses/followers", $criteria);
        $users['next_cursor'] = $content->next_cursor;
        $users['previous_cursor'] = $content->previous_cursor;
        $users['users'] = $this->formatUsersContent($content->users);
        return $users;
    }

    function formatUsersContent($content)
    {
        $i = 0;
        if (count($content) > 0) {
            foreach ($content as $value) {
                $created = $value->created_at;
                $text = $value->text;
                $users[$i]['description'] = $value->description;
                $users[$i]['profile_image_url'] = $value->profile_image_url;
                $users[$i]['screen_name'] = $value->screen_name;
                $users[$i]['name'] = $value->name;
                $users[$i]['user_id'] = $value->id;
                $users[$i]['listed_count'] = $value->listed_count;
                $users[$i]['url'] = $value->url;
                $users[$i]['statuses_count'] = $value->statuses_count;
                $users[$i]['followers_count'] = $value->followers_count;
                $users[$i]['friends_count'] = $value->friends_count;
                $users[$i]['location'] = $value->location;
                $users[$i]['following'] = $value->following;
                $users[$i]['status'] = $value->status->text;
                $users[$i]['status_id'] = $value->status->id;
                $users[$i]['status_date'] = $value->status->created_at;
                $i++;
            }
        }
        return $users;
    }

    function displayUsersIcons($criteria)
    {
        $users = $criteria['users'];
        $nb_display = $criteria['nb_display'];
        $width = $criteria['width'];
        if ($width == '')
            $width = "30";
        if ($nb_display > count($users) || $nb_display == '')
            $nb_display = count($users); //display value never bigger than nb users
        $display = '';
        for ($i = 0; $i < $nb_display; $i++) {
            $name = $users[$i]['name'];
            $picture = $users[$i]['picture'];
            $url = $users[$i]['url'];
            $display .= '<a href="' . $url . '" target="_blank" title="' . $name . '">';
            $display .= '<img src="' . $picture . '" width="' . $width . '" style="padding:2px;">';
            $display .= '</a>';
        }
        return $display;
    }

    function updateTwitterStatus($criteria)
    {
        $status = $criteria['status'];
        $token = $criteria['token'];
        $token_secret = $criteria['token_secret'];
        if ($token == '' || $token_secret == '') {
            $token = $this->twitter_token;
            $token_secret = $this->twitter_token_secret;
        }
        if (strlen($status) > 140) {
            $status = substr($status, 0, 140);
        }
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            //$connection->get('account/verify_credentials');
            $result = $connection->post('statuses/update', $criteria);
        }
        return $result;
    }

    function follow($name)
    {
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            //$connection->get('account/verify_credentials');
            $result = $connection->post('friendships/create/' . $name, array('screen_name' => $name));
        }
        return $result;
    }

    function unfollow($name)
    {
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            //$connection->get('account/verify_credentials');
            $result = $connection->post('friendships/destroy/' . $name, array('screen_name' => $name));
        }
        return $result;
    }

    function jaSigo($name)
    {
        $login = $this->getLoginData();
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            $result = $connection->get("friendships/exists", array('user_a' => $login['screen_name'], 'user_b' => $name));
        }
        return $result;
    }

    function meSegue($name)
    {
        $login = $this->getLoginData();
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            $result = $connection->get("friendships/exists", array('user_a' => $name, 'user_b' => $login['screen_name']));
        }
        return $result;
    }

    function getNonFollowers($name, $limit = 10)
    {
        $login = $this->getLoginData();
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        $result = array();
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            $friends = $connection->get("friends/ids", array('screen_name' => $name, 'stringify_ids' => 'true'));
            $followers = $connection->get("followers/ids", array('screen_name' => $name, 'stringify_ids' => 'true'));
            $result = array_diff_key(array_flip($friends->ids), array_flip($followers->ids));
            $result = array_keys($result);
            if (count($result) > $limit) {
                $result = array_slice($result, 0, $limit);
            }
            $users = $connection->get("users/lookup", array('user_id' => implode(',', $result)));
            return $users;
        }
        return false;
    }

    function countNonFollowers($name)
    {
        $login = $this->getLoginData();
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        $result = array();
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            $friends = $connection->get("friends/ids", array('screen_name' => $name, 'stringify_ids' => 'true'));
            $followers = $connection->get("followers/ids", array('screen_name' => $name, 'stringify_ids' => 'true'));
            $result = array_diff_key(array_flip($friends->ids), array_flip($followers->ids));
            return count($result);
        }
        return false;
    }

    function searchTweetsDetails($tweet, $p, $rpp)
    {
        $tweet = urlencode($tweet);
        $sxml = file_get_contents('http://search.twitter.com/search.json?lang='. LANG .'&page=' . $p . '&rpp=' . $rpp . '&q=' . $tweet);
        return json_decode($sxml);
    }

    function getLimit()
    {
        $login = $this->getLoginData();
        $token = $this->twitter_token;
        $token_secret = $this->twitter_token_secret;
        if ($token != '' && $token_secret != '') {
            $connection = new TwitterOAuth($_COOKIE['KEY'], $_COOKIE['SECRET'], $token, $token_secret);
            $result = $connection->get("account/rate_limit_status");
        }
        return $result;
    }
}