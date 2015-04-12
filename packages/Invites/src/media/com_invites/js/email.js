/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.fn.invitesEmail = function ( action ) {
        
        var form = $(this);
        var inputs = form.find('input[type="email"]');
        var canSubmit = true;
          
        $.each(inputs, function ( index, input ) {
            
            var elem = $(input);
            
            if(elem.val() != '') {
                if(!elem[0].checkValidity()) {
                    canSubmit = false;
                }
            }
            
        });         
        
        if ( canSubmit ) {
            form.trigger('submit');
        }
       
        return false;
    };
    
    $('body').on('submit', 'form#invites-email', function ( event ){

        even.preventDefault();
        $(this).invitesEmail();
    });
    
}(jQuery, window, document));