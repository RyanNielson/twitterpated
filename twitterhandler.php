<?php 

require_once('twitteroauth/twitteroauth.php');
require_once(dirname(__FILE__) . '/../../../wp-config.php');
require_once(ABSPATH . 'wp-load.php');

require_once('tweet.php');
require_once('twitteruser.php');
require_once('tweetfeed.php');

if (!class_exists('TwitterHandler')) {
    class TwitterHandler {
        public $consumer_key, $consumer_secret;
        public $access_token;

        function __construct() {
            $this->consumer_key = $this->load_consumer_key();
            $this->consumer_secret = $this->load_consumer_secret();

            $this->access_token = $this->load_access_token();
        }

        function check_credentials() {
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
            $content = $connection->get('account/verify_credentials');
            if (empty($content) || $content->error) {
                return false;
            }

            return true;
        }

        function authorize() {
            if ($_GET['signout']) {
                $this->signout();
            }

            if ($_GET['redirect']) {
                $this->redirect();
            }
            else if ($_GET['callback']) {
                $this->callback();
            }
            else {
                if (empty($this->access_token) || empty($this->access_token['oauth_token']) || empty($this->access_token['oauth_token_secret']) || !$this->check_credentials()) {
                    $this->signout();
                    $this->connect();
                }   
                else {
                    $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);

                    if ($this->check_credentials())
                        echo 'Signed in as ' . $this->access_token['screen_name'] . ' <a href="' . $this->get_settings_page_url() . '&signout=signout">Sign Out</a>';
                }
            } 
        }

        function connect() {
            if ($this->consumer_key === '' || $this->consumer_secret === '')
                $content = 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
            else
                $content = '<a href="' . $this->get_redirect_url() . '" id="twitter-sign-in-btn"><img src="'. $this->get_signin_image_url() . '" alt="Sign in with Twitter"/></a>';
            echo $content;
        }

        function redirect() {
            session_start();
            /* Build TwitterOAuth object with client credentials. */
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
             
            /* Get temporary credentials. */
            $request_token = $connection->getRequestToken($this->get_callback_url());
            $token = $request_token['oauth_token'];
            $secret_token = $request_token['oauth_token_secret'];
            
            /* Save temporary credentials to session. */
            $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
            $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
            
            /* If last connection failed don't display authorization link. */
            switch ($connection->http_code) {
              case 200:
                /* Build authorize URL and redirect user to Twitter. */
                $url = $connection->getAuthorizeURL($token);
                header('Location: ' . $url); 
                break;
              default:
                /* Show notification if something went wrong. */
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
            }
        }

        function callback() {
            if (isset($_REQUEST['oauth_verifier'])) { 
                $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

                /* Request access tokens from twitter */
                $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

                /* Remove no longer needed request tokens */
                unset($_SESSION['oauth_token']);
                unset($_SESSION['oauth_token_secret']);

                /* If HTTP response is 200 continue otherwise send to connect page to retry */
                if (200 == $connection->http_code) {
                    /* The user has been verified and the access tokens can be saved for future use */
                    $_SESSION['status'] = 'verified';
                    $this->access_token = $access_token;
                    $this->save_access_token($access_token);

                    echo '<script>window.close();</script>';
                } else {
                    echo '<script>window.close();</script>';
                }
            }
        }

        function use_cache($cache_key) {
            if (isset($cache_key) && $cache_key !== false && $cache_key !== null)
                return true;
            else 
                return false;
        }

        function get($endpoint, $cache_key, $parameters=array()) {
            $content = false;
            if ($this->use_cache($cache_key)) // Ryan Removed
                $content = $this->get_cache_value($cache_key);
            if ($content === false) {
                if ($this->check_credentials()) {
                    $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token['oauth_token'], $this->access_token['oauth_token_secret']);
                    $content = $connection->get($endpoint, $parameters);
                    if ($this->use_cache($cache_key))
                        $this->save_cache_value($cache_key, $content);
                }
                else
                    echo 'CREDENTIALS NO GOOD';
            }

            return $content;
        }

        function user_timeline($cache_key, $parameters=array(), $echo=false) {
            $content = $this->get_cache_value($cache_key);
            if ($content === false) {
                $tweets = $this->get('statuses/user_timeline', $cache_key . '_json', $parameters);
               
                $content = '';

                foreach ($tweets as $tweet) {
                    $tweetthing = new Tweet(
                        array(
                            'id' => $tweet->id_str,
                            'text' => $tweet->text,
                            'created_at' => $tweet->created_at,
                            'entities' => $tweet->entities,
                            'user' => new TwitterUser(
                                array(
                                    'name' => $tweet->user->name,
                                    'screen_name' => $tweet->user->screen_name,
                                    'profile_url' => 'http://twitter.com/' . $tweet->user->screen_name,
                                    'profile_image_url' => $tweet->user->profile_image_url,
                                    'following' => $tweet->user->following,
                                    'follow_request_sent' => $tweet->user->follow_request_sent
                                )
                            )
                        )
                    );

                    $tweetthing->render();
                }

            }

            return $content;
        }

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
            return set_transient($key, $value, 5);
        }
    }
}

?>