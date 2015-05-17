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
    
    $('body').on('change', 'input[name="coupon_code"]', function( event ){
        
        event.preventDefault();
        
        var element = $(this);
        var form = element.closest('form');
        
        element.inputAlert();
        element.inputAlert('clear');
        
        var ajax = $.ajax({
            
            method : 'post',
            url : form.attr('action'),
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
                   
                   $('#cc-form').find('input[name="coupon_code"]').val(null);
                   $('#paypal-form').find('input[name="coupon_code"]').val(null);
                   
                   return;
                
                } else {
                
                    element.inputAlert('success', validation );
                    
                    $('#cc-form').find('input[name="coupon_code"]').val( element.val() );
                    $('#paypal-form').find('input[name="coupon_code"]').val( element.val() );
                    
                    return;
                }
            }
        });
    });
    
}(jQuery, window)); 