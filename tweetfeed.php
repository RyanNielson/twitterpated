<?php

if (!class_exists('TweetFeed')) {
    class TweetFeed 
    {
        var $tweets = array();

        function __construct($tweets = array()) {
            $this->tweets = $tweets;
        }

        function render() {
            
        }
    }
}

?>
