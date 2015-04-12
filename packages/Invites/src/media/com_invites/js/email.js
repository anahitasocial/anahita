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
          
        $.each(inputs, function ( index, input ) {
            
            var elem = $(input);
            
            elem.inputAlert();
            elem.inputAlert('clear');
           
           if ( elem.val() != '' && elem[0].checkValidity()) {
               
               $.ajax({
                   
                   method : 'post',
                   url : form.attr('action'),
                   data : form.serialize(),
                   complete : function ( xhr, state ) {
                       
                       if ( state == 'error' ) {
                           
                           elem.inputAlert('error', StringLibAnahita.prompt.error );
                           
                       } else {
                            
                            elem.inputAlert('success', StringLibAnahita.prompt.email.inviteSent );
                            elem.attr('disabled', true);
                       }
                   }
               });
               
           }
            
        });         
        
        return false;
    };
    
    $('body').on('submit', 'form#invites-email', function ( event ) {

        event.preventDefault();
        $(this).invitesEmail();
    });
    
}(jQuery, window, document));