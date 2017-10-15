/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
    'use strict';
    
    $.fn.anahitaModal = function ( action ) {
      
        var elem = $(this);
        var modal = $('#an-modal');
        
        var header = modal.find('.modal-header').find('h3');
        var body = modal.find('.modal-body');
        var footer = modal.find('.modal-footer');
                
        var url = elem.data('url');

        $.get( url, function ( response ) {
   
            footer.find('button[type="submit"]').remove();
            
            header.text( $(response).filter('.modal-header').find('h3').text() );
            body.html( $(response).filter('.modal-body').html() ) ;
            footer.append( $(response).filter('.modal-footer').html() );
            
            modal.modal('show');
            
            var triggerBtn = footer.find('button[type="submit"]');
            var form = body.find('form');
            
            triggerBtn.on('click', function( event ) {
                $(this).button('loading');
                form.submit();
            });
        });
        
    };
    
    $('body').on('click', '[data-trigger="OpenModal"]', function ( event ) {
        event.preventDefault();
        $(this).anahitaModal();
    });
    
    $('#an-modal').bind('hidden', function () {
    	  $(this).find('.modal-footer').find('button').remove();
    });
	
    $('body').on('click', 'a[data-trigger*="Actors"]', function(event) {
        event.preventDefault();

        var actorsModal = $('#an-modal');
        var header = actorsModal.find('.modal-header').find('h3');
        var body = actorsModal.find('.modal-body');

        $.get($(this).attr('href'), function(response) {
            header.html($(response).filter('.modal-header').html());
            body.html($(response).filter('.modal-body'));

            actorsModal.modal('show');
        });
    });
    
}(jQuery, window, document));
