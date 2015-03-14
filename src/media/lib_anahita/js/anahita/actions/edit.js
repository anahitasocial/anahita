/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';

	$.fn.ActionEdit = function (options) {
		
		//default settings
		var settings = $.extend({
			entity : '.an-entity',		
		}, options );
		
		var elem = $(this);
		var entity = elem.closest(settings.entity);
		
		$.ajax({
			method : 'get',
			url : elem.attr('href'),
			beforeSend : function () {
				entity.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
			},
			success : function ( response ) {
				entity.html(response).fadeTo('fast', 1).removeClass('uiActivityIndicator');
			}
		});
	};
	
	$('body').on('click', 'a[data-action="edit"]', function( event ) {
		event.preventDefault();
		event.stopPropagation();
		$(this).ActionEdit();
	});

}(jQuery, window, document));