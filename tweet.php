<?php

if (!class_exists('Tweet')) {
    class Tweet 
    {
        private $id = null;
        private $text = null;
        private $created_at = null;
        private $user = null;
        private $entities = null;
      
        function __construct($arguments = array()) {
            $this->id = $arguments['id'];
            $this->text = $arguments['text'];
            $this->created_at = $arguments['created_at'];
            $this->user = $arguments['user'];
            $this->entities = $arguments['entities'];
        }

        function parse_text() {
            $text = $this->text;
        }

        private function replace_entities() {
            $text = $this->text;
            $replacements = array();

            $replaced_hashtags = array();
            foreach ($this->entities->hashtags as $key => $hashtag) {
                $replaced_hashtags[] = $hashtag->text;
                $text = preg_replace('/#' . $hashtag->text . '\b/i', '<a href="https://twitter.com/search?q=%23' . $hashtag->text . '&amp;src=hash" data-query-source="hashtag_click" class="hashtag customisable" dir="ltr" rel="tag" target="_blank">#<b>' . $hashtag->text . '</b></a>', $text);
            }

            $replaced_users = array();
            foreach ($this->entities->user_mentions as $key => $user_mention) {
                if (!in_array($user_mention->screen_name, $replaced_users)) {
                    $replaced_users[] = $user_mention->screen_name;
                    $text = preg_replace('/@' . $user_mention->screen_name . '\b/i', '<a href="https://twitter.com/intent/user?screen_name=' . $user_mention->screen_name . '" class="profile customisable h-card" dir="ltr">@<b class="p-nickname">' . $user_mention->screen_name . '</b></a>', $text);
                }
            }

           
            return $text;
        }

        function render() {
            $tweet_text = $this->replace_entities();
            ?>

            <link id="twitter-widget-css" rel="stylesheet" type="text/css" href="http://platform.twitter.com/embed/timeline.996256af577d2c3d78784b9bf8b648c6.default.css">
            <div class="root standalone-tweet ltr twitter-tweet not-touch" dir="ltr"  lang="en" >
                <blockquote class="tweet subject expanded h-entry" data-tweet-id="<?php echo $this->id; ?>" cite="https://twitter.com/<?php echo $this->user->screen_name; ?>/status/<?php echo $this->id; ?>">
                    <div class="header h-card p-author">
                        <a class="u-url profile" href="https://twitter.com/<?php echo $this->user->screen_name; ?>" aria-label="<?php echo $this->user->name; ?> (screen name: <?php echo $this->user->screen_name; ?>)">
                            <img class="u-photo avatar" alt="" src="<?php echo $this->user->profile_image_url; ?>" width="48" height="48">
                            <span class="full-name">
                                <span class="p-name customisable-highlight"><?php echo $this->user->name; ?></span>
                            </span>
                            <span class="p-nickname" dir="ltr">@<b><?php echo $this->user->name; ?></b></span>
                        </a>
                    </div>
                    <a href="https://twitter.com/<?php echo $this->user->screen_name; ?>" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false" data-dnt="true">Follow @<?php echo $this->user->screen_name; ?></a>
                    
                    <div class="e-entry-content">
                        <p class="e-entry-title">
                            <?php echo $tweet_text; ?>
                        </p>
                        <div class="dateline">
                            <a class="u-url customisable-highlight long-permalink" href="https://twitter.com/<?php echo $this->user->screen_name; ?>/statuses/<?php echo $this->id; ?>" data-datetime="<?php echo $this->created_at; ?>">
                                <time pubdate="" class="dt-updated" datetime="<?php echo $this->created_at; ?>" title="Time posted: 20 Feb 2013, 16:04:28 (UTC)"><?php echo $this->created_at; ?></time>
                            </a>
                        </div>

                    </div>
                        <div class="footer">
  
                        <ul class="tweet-actions">
                            <li><a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $this->id; ?>" class="reply-action web-intent" title="Reply"><i class="ic-reply ic-mask"></i><b>Reply</b></a></li>
                            <li><a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $this->id; ?>" class="retweet-action web-intent" title="Retweet"><i class="ic-retweet ic-mask"></i><b>Retweet</b></a></li>
                            <li><a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $this->id; ?>" class="favorite-action web-intent" title="Favorite"><i class="ic-fav ic-mask"></i><b>Favorite</b></a></li>
                        </ul>
                    </div>
                </blockquote>
            </div>

            <?php

            echo '<br/><br/>';
            echo $text;
        }
    }
}

?>