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

        //selector's modal
        modal : '#an-modal',

        //selector's form container
        formContainer : '#location-form-container',

        //selector's locations container
        locationsContainer : '#locations-container',

        //selector's search box
        searchQuery : '#an-search-query',

        //location entities
        entities : '.an-entities',

        //location entity
        entity : '.an-entity',

        //locatable's location list
        locationsList : '.an-locations'
      },

      _create : function () {

        var self = this;
        this.modal = $(this.options.modal);

        this.formContainer = null;
        this.locationsContainer = null;
        this.currentList = null;
        this.locatableId = null;

        //show selector event
        this._on('a[data-toggle*="LocationSelector"]', {
          click : function ( event ) {
            event.preventDefault();
            self.locatableId = $(event.currentTarget).data('locatable');
            self._showSelector( event.currentTarget );
          }
        });

        //listen to the filter box. If no locations are available, show the form
        this._on( $(document), {
          'afterFilterbox' : function( event ) {
            var entity = self.locationsContainer.find(this.options.entity);
            if (entity.length == 0) {
                self._showForm();
            } else {
              self._init();
            }
          }
        });
      },

      _init : function () {

          console.log('initiated');

          var self = this;

          //add a location to locatable
          this._on('a[data-action="add-location"]', {
            click : function ( event ) {
              event.preventDefault();
              self._addLocation( event.currentTarget );
            }
          });

          //delete location from locatable
          this._on('a[data-action="delete-location"]', {
            click : function ( event ) {
              event.preventDefault();
              self._deleteLocation( event.currentTarget );
            }
          });
      },

      _showSelector : function ( actionLink ) {

        var self = this;

        this.modal.find('.modal-footer').hide();

        var header = this.modal.find('.modal-header').find('h3');

    		var body = this.modal.find('.modal-body');

        $.get( $(actionLink).attr('href'), function (response) {

            header.html($(response).filter('.modal-header').html());

            body.html($(response).filter('.modal-body').html());

            self.modal.modal('show');

            self.formContainer = $(self.options.formContainer);

            self.locationsContainer = $(self.options.locationsContainer);

            self.searchQuery = $(self.options.searchQuery);

            self.formContainer.hide();

            self._browse();
    		});
      },

      _browse : function () {

        var self = this;
        var entities = self.locationsContainer.find(this.options.entities);

        $.ajax({
            method : 'GET',
            url : entities.data('url'),
            success : function (response) {

                var entity = $(response).filter(self.options.entity);

                if (entity.length) {
                    self._hideForm();
                    $(entities).html(entity);
                    self._init();
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
          method : 'POST',
          url : form.attr('action'),
          data : form.serialize(),
          beforeSend : function (){
            form.find(':submit').button('loading');
          },
          success : function ( response ) {
            self._refresh();
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

        this.locationsContainer.hide();

        var form = this.formContainer.find('form');

        this._on(form, {
            submit : function(event){
                event.preventDefault();
                self._add();
            }
        });

        if ( this.searchQuery.val() ) {
            $(form).find('input[name="name"]').val(this.searchQuery.val());
        }
      },

      _hideForm : function() {

        this.formContainer.hide();
        this.locationsContainer.show();
      },

      _addLocation : function ( actionLink ) {

        var self = this;
        var elem = $(actionLink);

        $.ajax({
          method : 'POST',
          url : elem.data('url'),
          data : {
            action : 'addlocation',
            location_id : elem.data('location')
          },
          success : function ( response ) {
            self.modal.modal('hide');
            self._refresh();
          }
        });
      },

      _deleteLocation : function ( actionLink ) {

        var self = this;
        var elem = $(actionLink);

        $.ajax({
          method : 'POST',
          url : elem.data('url'),
          data : {
            action : 'deletelocation',
            location_id : elem.data('location')
          },
          success : function ( response ) {
            elem.closest('li').fadeOut();
          }
        });
      },

      _refresh : function () {

        var self = this;
        var locationList = $('#locations-' + this.locatableId);

        $.ajax({
          method : 'GET',
          url : locationList.data('url'),
          success : function ( response ) {
             locationList.html(response);
             self._init();
          }
        });
      }
    });

    var locationsWidget = null;

    $(document).ready(function ( event ){
      console.log('document loaded');
      $('.an-locations').each(function(index, list){
        $.ajax({
          url : $(list).data('url'),
          success : function ( response ) {
            $(list).html(response);

            if (!locationsWidget) {
              locationsWidget = $('body').locations();
            }
          }
        });
      });
    });



}(jQuery, window, document));