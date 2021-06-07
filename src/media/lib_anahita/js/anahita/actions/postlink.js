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
        var params = elem.attr('href').split(/\?|\&/);     
        
        form.attr('action', params.shift()).attr('method', 'post');
         
        $.each(params, function(index, param){
            var pair = param.split('=');
            var input = $(document.createElement('input'));
            input.attr('type', 'hidden').attr('name', pair[0]).attr('value', pair[1]);  
            form.append(input);
        });
        
        form.appendTo('body').submit();
    };
    
    $('body').on('click', 'a[data-trigger="PostLink"]', function ( event ) {
        
        event.preventDefault();
        $(this).anahitaPostlink();
        
    });
    
}(jQuery, window, document));