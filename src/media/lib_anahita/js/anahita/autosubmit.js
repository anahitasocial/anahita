/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.AnAutosubmit = function() {
		
		var elem = $(this);
		
		var form = $(this.closest('form'));
		
		$.ajax({
			
			type : 'post',
			
			url : form.attr( 'action' ),
			
			data : form.serialize(),
			
			beforeSend : function() {
			
				elem.fadeTo( 'fast', 0.3 );
			
			}.bind( elem ),
	
			success : function ( response ) {
				
				elem.fadeTo( 'fast', 1 );
			
			}
		});
	};
	
	$('body').on( 'change', 'select.autosubmit, input.autosubmit', function( event ) {
		
		event.preventDefault();
	
		$(this).AnAutosubmit();
	
	});
	
}(jQuery, window, document));