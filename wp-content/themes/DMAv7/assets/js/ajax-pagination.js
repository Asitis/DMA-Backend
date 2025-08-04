(function($) {

    var page = 2;
    var loadmore = 'on';
    jQuery(document).on('scroll resize', function() {
      if (jQuery(window).scrollTop() + jQuery(window).height() + 1200 > jQuery(document).height()) {
        if (loadmore == 'on') {
          loadmore = 'off';
          //jQuery('#spinner').css('visibility', 'visible');
          jQuery('#container').append(jQuery('<div class="pure-g albumdeck" id="p' + page + '">').load('/page/' + page + ' .albumdeck > album', function() {
            $(this).animate({opacity: 0}, 0);
            $("img").unveil(500);
            page++;
            $(this).animate({opacity: 1}, 1000);
            loadmore = 'on';
            //jQuery('#spinner').css('visibility', 'hidden');
          }));
        }
      }
    });
    jQuery( document ).ajaxComplete(function( event, xhr, options ) {
      ajaxLoad();
      if (xhr.responseText.indexOf('class="pure-g albumdeck"') == -1) {
        loadmore = 'off';
        console.log('exit');
      }
    });
})(jQuery);