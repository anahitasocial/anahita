/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.anahitaAdmin = function ( action ) {
		
		if ( action == 'save' || action == 'apply' ) {
		
			var form = $('.-koowa-form');
			
			$.ajax({
				method : 'post',
				url : form.attr('action'),
				data : form.serialize(),
				success : function () {
					
					if ( action == 'apply' ) {
						window.location.reload();
					} else {
						window.history.back();
					}
					
				}
			});
			
			return this;
		}
		
		if ( action == 'cancel' ) {
			
			window.history.back();
		}
		
		return false;
	};
	
	var selectors = 
	'a[data-action="save"], ' + 
	'a[data-action="apply"], ' + 
	'a[data-action="cancel"] ';
		
	$('body').on('click', selectors, function ( event ){
		event.preventDefault();
		$(this).anahitaAdmin($(this).data('action'));
	});
	
}(jQuery, window, document));	

