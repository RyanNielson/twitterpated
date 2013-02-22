<?php

if (!class_exists('Tweet')) {
    class Tweet 
    {
        private $id = null;
        private $text = null;
        private $created_at = null;
        private $user = null;
        private $retweeting_user = null;
        private $retweet = false;
        private $entities = null;
      
        function __construct($arguments = array()) {
            $this->id = $arguments['id'];
            $this->text = $arguments['text'];
            $this->created_at = $arguments['created_at'];
            $this->user = $arguments['user'];
            $this->entities = $arguments['entities'];
            $this->retweet = $arguments['retweet'];
            $this->retweeting_user = isset($arguments['retweeting_user']) ? $arguments['retweeting_user'] : null;
        }

        function parse_text() {
            $text = $this->text;
        }

        private function formatted_tweet_text() {
            $text = $this->replace_hashtags($this->text);
            $text = $this->replace_mentions($text);
            $text = $this->replace_urls($text);
            return $text;
        }

        private function replace_hashtags($text) {
            $replaced_hashtags = array();
            foreach ($this->entities->hashtags as $hashtag) {
                if (!in_array($hashtag->text, $replaced_hashtags)) {
                    $replaced_hashtags[] = $hashtag->text;
                    $text = preg_replace('/#' . $hashtag->text . '\b/', '<a href="https://twitter.com/search?q=%23' . $hashtag->text . '&amp;src=hash" data-query-source="hashtag_click" class="hashtag customisable" dir="ltr" rel="tag" target="_blank">#<b>' . $hashtag->text . '</b></a>', $text);
                }
            }

            return $text;
        }

        private function replace_mentions($text) {
            $replaced_users = array();
            foreach ($this->entities->user_mentions as $user_mention) {
                if (!in_array($user_mention->screen_name, $replaced_users)) {
                    $replaced_users[] = $user_mention->screen_name;
                    $text = preg_replace('/@' . $user_mention->screen_name . '\b/', '<a href="https://twitter.com/intent/user?screen_name=' . $user_mention->screen_name . '" class="profile customisable h-card" dir="ltr">@<b class="p-nickname">' . $user_mention->screen_name . '</b></a>', $text);
                }
            }

            return $text;
        }

        private function replace_urls($text) {
            $replaced_urls = array();
            foreach ($this->entities->urls as $url) {
                if (!in_array($url->url, $replaced_urls)) {
                    $replaced_users[] = $url->url;
                    //$text = str_replace($url->url, );

                    $replacement_text = '<a href="' . $url->url . '"dir="ltr" data-expanded-url="' . $url->expanded_url . '" class="link customisable" target="_blank" title="' . $url->expanded_url . '">' . $url->url . '</a>';
                    $text = str_replace($url->url, $replacement_text, $text);
                    //$text = preg_replace('/@' . $user_mention->screen_name . '\b/', '<a href="https://twitter.com/intent/user?screen_name=' . $user_mention->screen_name . '" class="profile customisable h-card" dir="ltr">@<b class="p-nickname">' . $user_mention->screen_name . '</b></a>', $text);
                }
            }

            return $text;
        }

        private function formatted_posted_datetime() {
            $date_str = $this->created_at;
            $timestamp = strtotime($date_str);

            return $this->twitter_date_convertor($timestamp);
        }

        private function twitter_date_convertor($created_time){
            $current_time = time();
            $time_diff = $current_time - $created_time;

            if ($time_diff < 0 || $time_diff === null)
                return '';

            if ($time_diff < 60)
                return "just now";
            else if ($time_diff < 120) 
                return "1m";
            else if ($time_diff < 3600)
                return floor($time_diff / 60) . "m";
            else if ($time_diff < 7200)
                return "1h";
            else if ($time_diff < 86400)
                return floor($time_diff / 3600) . "h";
            else
                return date('j M y', $created_time);
        }

        function render() {
            ?>
            <li class="tweet h-entry" data-tweet-id="<?php echo $this->id; ?>">
  
                <a class="u-url permalink customisable-highlight" href="https://twitter.com/<?php echo $this->user->screen_name; ?>/statuses/<?php echo $this->id; ?>" data-datetime="<?php echo $this->created_at; ?>" target="_blank"><time pubdate="" class="dt-updated" datetime="<?php echo $this->created_at; ?>" title="Time posted: <?php echo $this->created_at; ?>"><?php echo $this->formatted_posted_datetime(); ?></time></a>

                <div class="header h-card p-author">
                    <a class="u-url profile" href="https://twitter.com/intent/user?screen_name=<?php echo $this->user->screen_name; ?>" aria-label="<?php echo $this->user->name; ?> (screen name: <?php echo $this->user->screen_name; ?>)">
                        <img class="u-photo avatar" alt="" src="<?php echo $this->user->profile_image_url; ?>" width="48" height="48" />
                        <span class="full-name">
                            <span class="p-name customisable-highlight"><?php echo $this->user->name; ?></span>
                        </span>
                        <span class="p-nickname" dir="ltr">@<b><?php echo $this->user->screen_name; ?></b></span>
                    </a>
                </div>

                <div class="e-entry-content">
                    <p class="e-entry-title">
                        <?php echo $this->formatted_tweet_text(); ?>
                    </p>
                    <?php if ($this->retweet) { ?>
                    <div class="retweet-credit">
                        <i class="ic-rt"></i>Retweeted by <a class="profile h-card" href="https://twitter.com/intent/user?screen_name=<?php echo $this->retweeting_user->screen_name; ?>" title="@<?php echo $this->retweeting_user->screen_name; ?> on Twitter"><?php echo $this->retweeting_user->name; ?></a>
                    </div>
                    <?php } ?>
                </div>

                <div class="footer">
                    <ul class="tweet-actions">
                        <li><a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $this->id; ?>" class="reply-action web-intent" title="Reply"><i class="ic-reply ic-mask"></i><b>Reply</b></a></li>
                        <li><a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $this->id; ?>" class="retweet-action web-intent" title="Retweet"><i class="ic-retweet ic-mask"></i><b>Retweet</b></a></li>
                        <li><a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $this->id; ?>" class="favorite-action web-intent" title="Favorite"><i class="ic-fav ic-mask"></i><b>Favorite</b></a></li>
                    </ul>
                </div>
            </li>

        <?php
        }

    }
}

?>