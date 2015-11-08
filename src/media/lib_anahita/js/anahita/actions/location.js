/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    $.widget("anahita.locations", {

        options : {
            formContainer : '#location-form-container',
            locationsContainer : '#locations-container',
            entities : '.an-entities'
        },

        _create : function() {
          this.locationsContainer = $(this.options.locationsContainer);
          this.formContainer = $(this.options.formContainer);
          this.formContainer.hide();
          this._browse();
        },

        _browse : function() {

            var self = this;
            var entities = self.locationsContainer.find(this.options.entities);

            $.ajax({
                'method' : 'GET',
                url : entities.data('url'),
                success : function (response) {

                    var entity = $(response).filter('.an-entity');

                    if (entity.length) {

                        self.formContainer.hide();
                        self.locationsContainer.show();
                        $(entities).html(entity);

                    } else {

                        self.formContainer.show();
                        self.locationsContainer.hide();
                    }
                }
            });
        },

        _add : function() {

        }
    });

    $('body').on('click', 'a[data-toggle*="LocationSelector"]', function ( event ){

        event.preventDefault();

        var modal = $('#an-modal');
  			modal.find('.modal-footer').hide();

    		var header = modal.find('.modal-header').find('h3');
    		var body = modal.find('.modal-body');

        $.get($(this).attr('href'), function (response){

      			header.html($(response).filter('.modal-header').html());
      			body.html($(response).filter('.modal-body').html());
  					modal.modal('show');

            $(this).locations();
    		});
    });

    $.fn.anahitaLocatable = function ( action ) {

        if( action == 'addLocation' || action == 'deleteLocation' ) {

            var elem = $(this);

            var response = $.ajax({
                method : 'post',
                url : elem.attr('href'),
                data : {
                    action : elem.data('action'),
                    location_id : elem.data('location')
                },
                success : function (response) {

                    if ( elem.data('action') == 'deleteLocation' ) {
                        elem.closest('.an-entity').fadeOut();
                    } else {
                        window.location.reload();
                    }

                    return true;
                },
                error : function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    return false;
                }
            });

        }

        return false;
    };

    // Add Location to the locatable
    $( 'body' ).on( 'click', '[data-action="addLocation"]', function( event ) {
  		  event.preventDefault();
        $(this).anahitaLocatable('addLocation');
    });

    // Remove Location from the locatable
    $( 'body' ).on( 'click', '[data-action="deleteLocation"]', function( event ) {
  		  event.preventDefault();
        $(this).anahitaLocatable('deleteLocation');
    });

}(jQuery, window, document));

/*
;(function ($, window, document) {

    'use strict';

    $.widget("anahita.locationSelector", {

        options : {
            formContainer : '#location-form-container',
            locationsContainer : '#locations-container',
            entities : '.an-entities'
        },

        _create : function() {

            this.locations = null;

            this.locationsContainer = $(this.options.locationsContainer);
            this.formContainer = $(this.options.formContainer);
            this.formContainer.hide();

            this.entities = this.locationsContainer.find(this.options.entities);

            this._browse();
        },

        _browse : function() {

  					var self = this;

  					$.ajax({
                'method' : 'GET',
                url : $(self.entities).data('url'),
                success : function (response) {

  									var entities = $(response).filter('.an-entity');

  									if (entities.length) {

  											self.formContainer.hide();
  											self.locationsContainer.show();

  									} else {

  											self.formContainer.show();
  											self.locationsContainer.hide();
  									}

                    $(self.entities).html(entities);
                }
            });

        }
    });

    $( 'body' ).on( 'click', '[data-action="addLocation"],[data-action="deleteLocation"]', function( event ) {
  		  event.preventDefault();

        var elem = $(this);
        var action = elem.data('action');
        var location = elem.data('location');

        $.ajax({
            method : 'post',
            'url' : elem.attr('href'),
            'data' : {
                action : action,
                location_id : location
            },
            success : function () {

                if ( action == 'deleteLocation' ) {
                    elem.closest('.an-entity').fadeOut();
                }
            }
        });
  	});

  	$('body').on('click', 'a[data-toggle*="LocationSelector"]', function ( event ){

        event.preventDefault();

        var modal = $('#an-modal');

  			modal.find('.modal-footer').hide();

    		var header = modal.find('.modal-header').find('h3');

    		var body = modal.find('.modal-body');

        $.get($(this).attr('href'), function (response){

      			header.html($(response).filter('.modal-header').html());

      			body.html($(response).filter('.modal-body').html());

  					modal.modal('show');

            var selector = $("[data-behavior='LocationSelector']").locationSelector();
    		});
    });

}(jQuery, window, document));
*/
