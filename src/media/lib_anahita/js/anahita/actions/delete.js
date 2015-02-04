/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.actionDelete = function(type) {
		
		type = type || '';
		var elem = $(this);
		
		
	};
	
	$('body').on('click', 'a.action-delete', function( event ) {
		event.preventDefault();
		$(this).actionDelete();
	});
	
}(jQuery, window, document));