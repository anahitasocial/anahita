/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';

	$.fn.ActionCancel = function () {
		
		var elem = $(this);
		
		$.get( elem.data('url'), function (html) {
			elem.closest('form').replaceWith($(html).fadeIn('slow'));
		});
	};
	
	$('body').on('click', 'button.action-cancel', function( event ) {
		event.preventDefault();
		$(this).ActionCancel();
	});

}(jQuery, window, document));