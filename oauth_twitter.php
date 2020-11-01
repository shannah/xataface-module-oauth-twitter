<?php
class modules_oauth_twitter {
    
    const VERIFY_CREDENTIALS_URL = "https://api.twitter.com/1.1/account/verify_credentials.json?skip_status=true&include_entities=false";
    
    public function __construct() {
        $app = Dataface_Application::getInstance();
        $app->registerEventListener('oauth_fetch_user_data', array($this, 'oauth_fetch_user_data'), false);
        $app->registerEventListener('oauth_extract_user_properties_from_user_data', array($this, 'oauth_extract_user_properties_from_user_data'), false);
    }
    
    function post($url, $data=array(), $json=true) {
        $mod = Dataface_ModuleTool::getInstance()->loadModule('modules_oauth');
        return $mod->post('twitter', $url, $data, $json);
    }
    
    function get($url, $headers='', $json=true) {
        $mod = Dataface_ModuleTool::getInstance()->loadModule('modules_oauth');
        return $mod->get('twitter', $url, $headers, $json);
    }
        
    public function oauth_fetch_user_data($evt) {
        
        if ($evt->service !== 'twitter') {
            return;
        }
		$app = Dataface_Application::getInstance();
		$config = @$app->_conf['oauth_twitter'];
		$email = $config and @$config['include_email'];
		$url = self::VERIFY_CREDENTIALS_URL;
		if ($email) {
			$url .= '&include_email=true';
		}
        $res = $this->get($url);
        if (df_http_response_code() < 200 or df_http_response_code() > 299) {
            throw new Exception("Failed to get user credentials.  Response code ". df_http_response_code());
        }
        if (!@$res['id']) {
            throw new Exception("Failed to get user credentials.");
        }
        
        $evt->out = $res;
        
    }
    
    public function oauth_extract_user_properties_from_user_data($evt) {
        if ($evt->service !== 'twitter') {
            return;
        }
        
        if (isset($evt->userData['id'])) {
            $evt->out = array('id' => $evt->userData['id'], 'name' => $evt->userData['screen_name'], 'username' => $evt->userData['screen_name']);
        }

    }
            
}

