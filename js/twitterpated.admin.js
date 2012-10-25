(function($) {
    $(document).ready(function(){

        function removeUrlParameter(url, parameter) {
            var urlparts= url.split('?');

            if (urlparts.length>=2) {
                var urlBase=urlparts.shift(); //get first part, and remove from array
                var queryString=urlparts.join("?"); //join it back up
                var prefix = encodeURIComponent(parameter)+'=';
                var pars = queryString.split(/[&;]/g);
                for (var i = pars.length - 1; i > 0; i--) {          //reverse iteration as may be destructive
                    console.log(i);
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {  //idiom for string.startsWith
                        pars.splice(i, 1);
                    }
                }
              url = urlBase+'?'+pars.join('&');
            }

            return url;
        }

        $('#twitter-sign-in-btn').click(function(){
            $.oauthpopup({
                path: $(this).attr('href'),
                callback: function(){
                    var url = removeUrlParameter(window.location.href, 'signout');
                    url = removeUrlParameter(url, 'redirect');
                    url = removeUrlParameter(url, 'callback');
                    window.location = url;
                }
            });

            return false;
        });
    });
})(jQuery);