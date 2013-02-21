<?php

if (!class_exists('TweetFeed')) {
    class TweetFeed 
    {
        var $tweets = array();

        function __construct($tweets = array()) {
            $this->tweets = $tweets;
        }

        function render() {
            ?>

            <div class="twitterpated-feed">

            <?php

            foreach ($this->tweets as $tweet) {
                $tweet->render();
            }

            ?>

            </div>

            <?php
        }

        function add_tweet($tweet) {
            $this->tweets[] = $tweet;
        }
    }
}

?>
