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

			$(form).trigger('beforeFilterbox');

      $.ajax({
          method : 'get',
          url : form.attr('action'),
          data : form.serialize(),
          beforeSend : function (){
							form.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
          },
          success : function ( response ) {

							var items = $(response).filter('.an-entity');

							if(items.length == 0) {
								items = $(response).find('.an-entity');
							}

							if(form.siblings('[data-trigger="InfiniteScroll"]').length) {

								 var container = form.siblings('[data-trigger="InfiniteScroll"]');
								 $(container).data('fetched-items', items);
								 $(container).trigger('entities-reset-render');

								 return;
							}

							form.siblings('.an-entities').html(items);

							var pagination = $(response).filter('.pagination');

							if(pagination.length == 0) {
								pagination = $(response).find('.pagination');
							}

							form.siblings('.pagination').html(pagination);
          },
          complete : function () {

							form.fadeTo('fast', 1).removeClass('uiActivityIndicator');

							if(form.siblings('[data-trigger="InfiniteScroll"]').length) {
									var container = form.siblings('[data-trigger="InfiniteScroll"]');
									var newUrl = form.attr('action') + '&' + form.serialize();
              		$(container).data( 'newUrl',  newUrl ).trigger('urlChange');
							} else {
									var container = form.siblings('.an-entities');
							}

							$(container).trigger('afterFilterbox');
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
