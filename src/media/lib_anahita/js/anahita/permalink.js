/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $( document ).ready(function(){
        var hash = window.location.hash.slice(1).split('=');
        
        if( hash[0] == 'scroll' ) {
            var comment = $('.cid-' + hash[1] )[0];
            
            $('html, body').animate({
                scrollTop: $(comment).offset().top - 100
            }, 1000, 'swing', function(){
                $(comment).addClass('an-highlight');
            });
        }
    });
    
}(jQuery, window, document));
