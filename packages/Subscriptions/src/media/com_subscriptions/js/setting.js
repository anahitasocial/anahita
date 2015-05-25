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
    
    $('body').on('click', 'a[data-trigger="DeleteSubscriber"]', function( event ){
        
        event.preventDefault();
        
        var form = $(this).closest('form');
        form.find('input[name="action"]').val('deletesubscriber');
        form.submit(); 
    });
    
}(jQuery, window)); 