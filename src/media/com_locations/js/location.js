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
            modal : '#an-modal',
            formContainer : '#location-form-container',
            selectorLocationsContainer : '#locations-container',
            locatableLocations : '.an-locations',
            entities : '.an-entities',
            entity : '.an-entity'
        },

        _create : function() {

            var self = this;
            this.modal = $(this.options.modal);
            this.selectorLocationsContainer = $(this.options.selectorLocationsContainer);
            this.locatableLocations = this.closest(this.options.locatableLocations);
            this.formContainer = $(this.options.formContainer);
            this.formContainer.hide();
            this._browse();

            //listen to the filter box. If no locations are available, show the form
            this._on( $(document) , {
                'afterFilterbox' : function( event ) {
                    var entity = self.selectorLocationsContainer.find(this.options.entity);
                    if(entity.length == 0){
                        self._showForm();
                    }
                }
            });

            this._on('body [data-action="addLocation"]', {
              click : function ( event ) {
                 event.preventDefault();
                 this._addLocationToLocatable();
              }
            });

            this._on('body [data-action="deleteLocation"]', {
              click : function ( event ) {
                 event.preventDefault();
                 this._deleteLocationFromLocatable();
              }
            });
        },

        _browse : function() {

            var self = this;
            var entities = self.selectorLocationsContainer.find(this.options.entities);

            $.ajax({
                'method' : 'GET',
                url : entities.data('url'),
                success : function (response) {
                    var entity = $(response).filter(self.options.entity);
                    if (entity.length) {
                        self._hideForm();
                        $(entities).html(entity);
                    } else {
                        self._showForm();
                    }
                }
            });
        },

        _add : function(){
            var self = this;
            var form = self.formContainer.find('form');

            $.ajax({
        				method : 'post',
        				url : form.attr('action'),
        				data : form.serialize(),
        				beforeSend : function (){
        					form.find(':submit').button('loading');
        				},
        				success : function ( response ) {
                  $('document').anahitaLocatable('refresh');
                  self.modal.modal('hide');
        				},
        				complete : function ( xhr, status ) {
        				    form.find(':submit').button('reset');
        				}
      			});
        },

        _showForm : function() {

            var self = this;
            this.formContainer.show();
            this.selectorLocationsContainer.hide();

            var form = this.formContainer.find('form');

            this._on(form, {
                submit : function(event){
                    event.preventDefault();
                    self._add();
                }
            });

            if ($('#an-search-query').val()) {
                $(form).find('input[name="name"]').val($('#an-search-query').val());
            }
        },

        _hideForm : function() {
            this.formContainer.hide();
            this.selectorLocationsContainer.show();
        },

        _addLocationToLocatable : function() {

        },

        _deleteLocationFromLocatable : function() {

        },

        _refreshLocations : function() {

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

        var elem = $(this);
        var modal = $('#an-modal');
        var container = elem.closest('.an-locations');

        console.log(elem);

        if ( action == 'refresh' ) {
            //container.load(container.data('url'));

            $.ajax({
              url : container.data('url'),
              dataType : 'json',
              success : function (response) {
                  console.log(response);
              }
            });
        }

        if( action == 'addLocation' || action == 'deleteLocation' ) {

            var response = $.ajax({
                method : 'post',
                url : elem.attr('href'),
                data : {
                    action : elem.data('action'),
                    location_id : elem.data('location')
                },
                success : function (response) {

                    if ( elem.data('action') == 'deleteLocation' ) {
                        elem.closest('li').fadeOut();
                    } else if ( elem.data('action') == 'addLocation' ) {
                        modal.modal('hide');
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

}(jQuery, window, document));
