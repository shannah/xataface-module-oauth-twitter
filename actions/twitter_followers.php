<?php
class actions_twitter_followers {
    function handle($params) {
        $user = getUser();
        $mod = Dataface_ModuleTool::getInstance()->loadModule('modules_oauth_twitter');
        $res = $mod->get("https://api.twitter.com/1.1/friends/ids.json?user_id=".urlencode($user->val('twitter_id')));
        print_r($res);
    }
}
