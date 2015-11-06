/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    $.fn.anahitaLocation = function () {

        var elem = $( this );
        var action = elem.data('action');

        $.ajax({
            method : 'post',
            'url' : elem.attr('href'),
            'data' : {
                action : action,
                location_id : elem.data('location')
            },
            success : function () {

                if ( action == 'deleteLocation' ) {
                    elem.closest('.an-entity').fadeOut();
                }
            }
        });

        return;
    };

    $( 'body' ).on( 'click', '[data-action="addLocation"],[data-action="deleteLocation"]', function( event ) {
  		  event.preventDefault();
  		  $(this).anahitaLocation();
  	});

}(jQuery, window, document));
