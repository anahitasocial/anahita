/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.fn.anahitaGist = function () {
        
        this.filter( this.selector ).each(function(){
           
            var placeholder = $(this);
        
            $.getJSON( placeholder.data('src') + 'on?callback=?', function( data ) {
     
                // replace script with gist html
                placeholder.replaceWith( $( data.div ) );
     
                // load the stylesheet, but only once…            
                var head = $('head');
     
                if ( head.find('link[rel="stylesheet"][href="'+ data.stylesheet +'"]').length < 1 ) {
                    head.append('<link rel="stylesheet" href="'+ data.stylesheet +'" type="text/css" />');
                }
            });
        });
    };
    
    if ( $('[data-trigger="LoadGist"]').length ) {
        $('[data-trigger="LoadGist"]').anahitaGist();
    }
    
    $(document).ajaxSuccess(function() {
        
        if ( $('[data-trigger="LoadGist"]').length ) {
            $('[data-trigger="LoadGist"]').anahitaGist();
        }
    });
    
}(jQuery, window, document));