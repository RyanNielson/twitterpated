(function($){
    $.popupcallback = function(options)
    {
        if (!options || !options.path) {
            throw new Error("options.path must not be empty");
        }
        options = $.extend({
            windowName: 'Popup', 
            windowOptions: 'location=0,status=0,width=800,height=400', 
            callback: function(){ window.location.reload(); }
        }, options);

        var oauthWindow   = window.open(options.path, options.windowName, options.windowOptions);
        var oauthInterval = window.setInterval(function() {
            if (oauthWindow.closed) {
                window.clearInterval(oauthInterval);
                options.callback();
            }
        }, 1000);
    };
})(jQuery);