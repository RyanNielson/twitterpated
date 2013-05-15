<?php

if (!class_exists('TweetFeed')) {
    class TweetFeed 
    {
        var $screen_name = '';
        var $tweets = array();

        function __construct($screen_name, $tweets = array()) {
            $this->screen_name = $screen_name;
            $this->tweets = $tweets;
        }

        function render($bird_colour = 'light') {
             ?>

            <div class="twitterpated-feed" class="stream">
                <div class="feed-header">
                    <a href="http://twitter.com/<?php echo $this->screen_name; ?>" target="_blank">
                        <?php if ($bird_colour == 'dark') { ?>
                            <img src="<?php echo plugins_url('images/twitter-bird-dark-bgs.png' , __FILE__) ?>" />
                        <?php } else { ?>
                            <img src="<?php echo plugins_url('images/twitter-bird-light-bgs.png' , __FILE__) ?>" />
                        <?php } ?>
                        <h3>@<?php echo $this->screen_name; ?></h3>
                    </a>
                </div>
                <ol class="h-feed">
                <?php foreach ($this->tweets as $tweet) { $tweet->render(); } ?>
                </ol>
            </div>

            <?php
        }

        function add_tweet($tweet) {
            $this->tweets[] = $tweet;
        }
    }
}

?>
