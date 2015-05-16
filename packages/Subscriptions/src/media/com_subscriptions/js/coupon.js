/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 * 
 * JQuery plugin that determines the validity of a coupon code by asking the server
 */

;(function($, window) {
    
    'use strict';
    
    $('body').on('change', 'input[name="coupon"]', function( event ){
        
        event.preventDefault();
        
        var element = $(this);
        
        element.inputAlert();
        element.inputAlert('clear');
        
        var ajax = $.ajax({
            
            method : 'post',
            url : element.data('url'),
            data : {
                action : 'validate',
                key : 'code',
                value : element.val() || ''
            },
            headers: { 
                accept: 'application/json'
            },
            beforeSend : function(){
                element.attr('disabled', true);
            },
            complete : function ( xhr, textStatus ) {
                
                element.attr('disabled', false);
                
                var validation = $.parseJSON( xhr.getResponseHeader('validation') );
                
                if ( textStatus == 'error' ) {
   
                   element.inputAlert('error', validation['errorMsg'] );
                   
                   return;
                
                } else {
                
                    element.inputAlert('success', validation );
                    
                    return;
                }
            }
        });
    });
    
}(jQuery, window)); 