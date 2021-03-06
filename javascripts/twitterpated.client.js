(function($) {

    TwitterpatedFeed = (function() {

        function TwitterpatedFeed($element, options) {
          this.$element = $element;
          this.options = options;
          this.getTweets();
        }

        TwitterpatedFeed.prototype.getTweets = function() {
            var _this = this;
            var ajaxurl = '/wp-admin/admin-ajax.php';
            var data = {
                action: 'twitterpated_get_timeline',
                count: this.options.count,
                screen_name: this.options.screen_name
            };

            $.get(ajaxurl, data, function(response) {
                // var $elem = $('<aside />').append(response);
                //_this.$element.append($elem);
                _this.$element.append(response);

                // var $scr = $('<script />');
                // $scr.attr('type', 'text/javascript');
                // $scr.attr('src', 'http://platform.twitter.com/widgets.js');
                // $('body').append($scr);
            });
        };

        return TwitterpatedFeed;

    })();

    $(function() {
        $.fn.twitterpated = function(options) {
            return this.each(function() {
                this.defaults = {
                    count: 1,
                    screen_name: ''
                };

                this.settings = {};
                this.$element = $(this);
                this.init = function() {
                    var twitterFeed;
                    this.settings = $.extend({}, this.defaults, options);
                    return twitterFeed = new TwitterpatedFeed(this.$element, this.settings);
                };
                return this.init();
            });
        };
    });

})(jQuery);