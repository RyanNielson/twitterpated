<?php

if (!class_exists('TwitterUser')) {
    class TwitterUser 
    {
        public $name = null;
        public $screen_name = null;
        public $profile_url = null;
        public $profile_image_url = null;
        public $following = false;
        public $follow_request_sent = false;
      
        function __construct($arguments = array()) {
            $this->name = $arguments['name'];
            $this->screen_name = $arguments['screen_name'];
            $this->profile_url = $arguments['profile_url'];
            $this->profile_image_url = $arguments['profile_image_url'];
            $this->following = $arguments['following'];
            $this->follow_request_sent = $arguments['follow_request_sent'];
        }

        function render() {

        }
    }
}

?>