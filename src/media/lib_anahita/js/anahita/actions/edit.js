/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
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
		
		$.get( elem.attr('href'), function (html) {
			entity.replaceWith($(html).fadeIn('slow'));
		});
	};
	
	$('body').on('click', 'a[data-action="edit"], a[data-action="editcomment"]', function( event ) {
		event.preventDefault();
		$(this).ActionEdit();
	});

}(jQuery, window, document));