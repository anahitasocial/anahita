/**
 * Author: Nick Swinford
 * Email: NicholasJohn16@gmail.com
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
    'use strict';
    
    $.fn.notificationsCounter = function () {
    	
    	var counter = $(this);
    	
    	var pulse = function () {
            
            $.ajax({
            
                method : 'get',
                url : counter.data('url'),
                headers: { 
                        accept: 'application/json'
                },
                success : function ( response ) {
                    
                    counter.html( response.new_notifications );
                    Tinycon.setBubble( response.new_notifications ); 
                    
                    if ( response.new_notifications > 0 ) {
                        counter.addClass('badge-important');
                    }
                    
                    setTimeout(pulse, counter.data('interval'));
                }     
            });
    	};
    	
    	pulse();
    };
    
    //counter
    if( $('#notifications-counter').length ) {
        $('#notifications-counter').notificationsCounter();
    }
    
    //popover
    $('body').on('click', 'a[data-trigger*="notifications-popover"]', function ( event ) {
    	
    	event.preventDefault();
    	
    	var elem = $(this);
    	
    	$.get(elem.attr('href'), function (response){

    		var notifications = $(response);
    		var title = notifications.filter('.popover-title').html();
    		var content = notifications.filter('.popover-content').html();
 
    		elem.popover({
    			title : title,
    			content: content,
    			html : true,
    			placement: 'bottom'
    		}).popover('show');
    	});
    });
	
}(jQuery, window, document));