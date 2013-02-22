<?php

/**
 * Plugin Name: Twitterpated
 * Author: Ryan Nielson
 * Author URI: http://ryannielson.ca
 * Version: 1.0.0
 * Description: Adds the ability to render twitter feeds using oAuth.
 */

require_once('TwitterHandler.php');
require_once('tweet.php');
require_once('twitteruser.php');
require_once('tweetfeed.php');

class Twitterpated {
   
    public function __construct() {
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_menu', array($this, 'settings'));

        add_action('admin_enqueue_scripts', array($this, 'add_admin_javascripts'));
        add_action('wp_enqueue_scripts', array($this, 'add_javascripts'));
        add_action('wp_enqueue_scripts', array($this, 'add_stylesheets'));

        add_action('wp_ajax_twitterpated_get_timeline', array($this, 'ajax_get_timeline'));
        add_action('wp_ajax_nopriv_twitterpated_get_timeline', array($this, 'twitterpated_ajax_get_timeline'));

        add_shortcode('twitterpated', array($this, 'shortcode'));
    }

    public function settings_init() {
        add_settings_section('twitterpated_admin_settings_section', 'Twitterpated Admin Options', array($this, 'admin_settings_callback'), 'twitterpated_settings');
        add_settings_field('twitterpated_consumer_key', 'Consumer Key', array($this, 'textfield_callback'), 'twitterpated_settings', 'twitterpated_admin_settings_section', array('twitterpated_consumer_key'));
        add_settings_field('twitterpated_consumer_secret', 'Consumer Secret', array($this, 'textfield_callback'), 'twitterpated_settings', 'twitterpated_admin_settings_section', array('twitterpated_consumer_secret'));

        register_setting('twitterpated_admin_settings_section', 'twitterpated_consumer_key');
        register_setting('twitterpated_admin_settings_section', 'twitterpated_consumer_secret');
    }

    public function settings() {
        add_menu_page('Twitterpated Settings', 'Twitterpated Settings', 'manage_options', 'twitterpated_settings', array($this, 'settings_display'));
    }

    public function add_admin_javascripts() {
        wp_enqueue_script('oauth_popup_script',  plugins_url('javascripts/jquery.popupcallback.js' , __FILE__));
        wp_enqueue_script('twitterpated_admin_script',  plugins_url('javascripts/twitterpated.admin.js' , __FILE__));
    }

    public function add_javascripts() {
        wp_enqueue_script('twitterpated_client_script', plugins_url('javascripts/twitterpated.client.js' , __FILE__));
        wp_enqueue_script('twitter_widget_script', 'http://platform.twitter.com/widgets.js'); // Already enqueued on EVERY page by NextGen :S 
    }

    public function add_stylesheets() {
        wp_enqueue_style('twitterpated_timeline_style', 'http://platform.twitter.com/embed/timeline.css');
        wp_enqueue_style('twitterpated_style', plugins_url('stylesheets/twitterpated.css' , __FILE__));
    }

    public function ajax_get_timeline() {
        $count = $_GET['count'];
        echo trim(stripslashes(json_encode(twitterpated_get_timeline($count))), '"');
        die();
    }

    public function admin_settings_callback() {  
        echo '<p>Select which areas of content you wish to display.</p>';  
    }

    public function textfield_callback($args) {
        // Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field  
        $html = '<input type="text" id="' . $args[0] .'"  name="' . $args[0] .'" value="' . get_option($args[0]) . '" />';  
        // Here, we will take the first argument of the array and add it to a label next to the checkbox  
        $html .= '<label for="' . $args[0] .'"> '  . $args[1] . '</label>';  
        echo $html;  
    }

    public function settings_display() {
        ?>

        <div class="wrap">
            <h2>Twitterpated Settings</h2>

            <form method="post" action="options.php"> 
                <?php if (current_user_can('twitterpated_administer')) { ?>
                    <?php settings_fields('twitterpated_admin_settings_section'); ?>
                    <?php do_settings_sections( 'twitterpated_settings' ); ?>
                    <?php submit_button(); ?>
                <?php } ?>
            </form>

            <?php
            if (get_option('twitterpated_consumer_key') && get_option('twitterpated_consumer_secret')) {
                $handler = new TwitterHandler();
                $handler->authorize();
            }
            ?>

        </div>

        <?php
    }

    public function on_activate() {
        $role = get_role('administrator');
        $role->add_cap("twitterpated_administer");
    }

    public function get_timeline($screen_name, $count = 1, $echo = false) {
        $handler = new TwitterHandler();
        
        $cache_key = "twitterpated_feed_" . $screen_name .  "_$count";
        $parameters = array('screen_name' => $screen_name, 'count' => $count);
        
        ob_start();
        $handler->user_timeline($cache_key, $parameters);
        $contents = ob_get_contents();
        ob_end_clean();

        if ($echo) {
            echo $contents;
            return true;
        }
        else
            return $contents;
    }

    public function shortcode($atts) {
        extract(shortcode_atts(array(
            'count' => 1,
            'screen_name' => false,
        ), $atts));


        if ($screen_name)
            return $this->get_timeline($screen_name, $count);
        else 
            return '';
    }
}

new Twitterpated();

function twitterpated_timeline($screen_name, $count = 1, $echo = false) {
    $twitterpated = new Twitterpated();
    return $twitterpated->get_timeline($screen_name, $count, $echo);
}

register_activation_hook(__FILE__, array('Twitterpated', 'on_activate'));

?>