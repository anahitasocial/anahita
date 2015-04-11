/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.fn.anahitaPostlink = function () {
        
        var elem = $(this);
        var form = $(document.createElement('form'));
 
        form.attr('action', elem.attr('href'));
        form.attr('method', 'post');
        form.trigger('submit');
    };
    
    $('body').on('click', 'a[data-trigger="PostLink"]', function ( event ) {
        
        event.preventDefault();
        $(this).anahitaPostlink();
    });
    
}(jQuery, window, document));