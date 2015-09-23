/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.AnFilterbox = function() {
	    
	    var form = $(this);

        $.ajax({
            method : 'get',
            url : form.attr('action'),
            data : form.serialize(),
            beforeSend : function (){
                form.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
            },
            success : function ( response ) {
                
                if( $(response).filter('.an-entity').length ) {
                    
                  form.siblings('.an-entities').html($(response).filter('.an-entity'));
                  form.siblings('.pagination').html($(response).filter('.pagination'));  
                    
                } else {
                  
                  form.siblings('.an-entities').html($(response).find('.an-entity'));
                  form.siblings('.pagination').html($(response).find('.pagination'));
                    
                }
            },
            complete : function () {
                form.fadeTo('fast', 1).removeClass('uiActivityIndicator');
                var newUrl = form.attr('action') + '&' + form.serialize();
                $(document).data( 'newUrl',  newUrl ).trigger('urlChange');
            }
        });
	    
	};
	
	$('body').on('submit', '#an-filterbox', function( event ){
		event.preventDefault();
		$(this).AnFilterbox();
	});
	
	$('body').on('change', '#an-filterbox select, #an-filterbox input[type=checkbox]', function( event ){
	    event.preventDefault();
        var form = $(this).closest('form');
        form.AnFilterbox();
	});
	
}(jQuery, window, document));	