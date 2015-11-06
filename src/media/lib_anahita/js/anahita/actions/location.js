/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    $.fn.anahitaLocation = function ( action ) {

        var elem = $( this );

        if( action == 'delete' ) {

            $.ajax({
                method : 'post',
                'url' : elem.attr('href'),
                'data' : {
                    action : elem.data('action'),
                    location_id : elem.data('location')
                },
                success : function () {
                    elem.closest('.an-entity').fadeOut();
                }
            });

        }

        return;
    };

    $( 'body' ).on( 'click', '[data-action="deleteLocation"]', function( event ) {
  		  event.preventDefault();
  		  $(this).anahitaLocation('delete');
  	});

}(jQuery, window, document));
