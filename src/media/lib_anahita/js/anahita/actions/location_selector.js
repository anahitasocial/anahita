/**
* Author: Rastin Mehr
* Email: rastin@anahitapolis.com
* Copyright 2015 rmdStudio Inc. www.rmdStudio.com
* Licensed under the MIT license:
* http://www.opensource.org/licenses/MIT
*/

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

          this.formContainer = this.element.find(this.options.formContainer);
          this.formContainer.hide();

          this.locationsContainer = this.element.find(this.options.locationsContainer);

          this._browse();
      },

      _browse : function() {

					var self = this;

					$.ajax({
              'method' : 'GET',
              url : $(this.locationsContainer).data('url'),
              success : function (response) {

									var entity = $(response).find('.an-entity');

									if(entity.length){
											self.formContainer.hide();
											self.locationsContainer.show();

									} else {
											self.formContainer.show();
											self.locationsContainer.hide();
									}
              }
          });

      }
  });

  //show voters in a modal
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
