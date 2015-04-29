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
    
    $.fn.anSort = function() {
        
        var self = $(this);
        
        $.ajax({
           
        	url: self.attr('href'),
        	
        	beforeSend: function() {
        		self.parent().fadeTo('fast', 0.3).addClass('uiActivityIndicator');
        	},
           
           success: function(response) {
                              
               $('#an-entities-main').html($(response).find('.an-entity'));
               $('.pagination').html($(response).filter('.pagination'));
        	   
               self.parent().siblings().removeClass('active');
               self.parent().addClass('active');
           },
           complete: function() {
               self.parent().fadeTo('fast', 1).removeClass('uiActivityIndicator');
           }
        });
    };
    
    $('body').on('click','ul[data-behavior="sortable"] a', function(event) {
        event.preventDefault();
        $(this).anSort();
    });
    
}(jQuery, window, document));