<?php 

require_once('twitterhandler.php');

require_once(dirname(__FILE__) . '/../../../wp-config.php');
require_once(ABSPATH . 'wp-load.php');

class WPTwitterHandler extends AbstractTwitterHandler {
    function signout() {
        delete_option('twitterpated_access_token');
        $this->access_token = $this->load_access_token();
    }

    function save_access_token($access_token) {
        update_option('twitterpated_access_token', serialize($access_token));
        $_SESSION['access_token'] = $access_token;
    }

    function load_access_token() {   
        $this->access_token = unserialize(get_option('twitterpated_access_token'));
        return $this->access_token;
    }

    function load_consumer_key() {
        return get_option('twitterpated_consumer_key');
    }   

    function load_consumer_secret() {
        return get_option('twitterpated_consumer_secret');
    }   

    function get_redirect_url() {
        return plugins_url('wppopup.php?redirect=redirect' , __FILE__);
    }

    function get_callback_url() {
        return plugins_url('wppopup.php?callback=callback' , __FILE__);
    }

    function get_signin_image_url() {
        return plugins_url('images/sign-in-with-twitter-gray.png' , __FILE__);
    } 

    function get_settings_page_url() {
        return menu_page_url('twitterpated_settings', false);
    } 

    function get_cache_value($key) {
        return get_transient($key);
    }

    function save_cache_value($key, $value) {
        return set_transient($key, $value, 60);
    }
}

?>