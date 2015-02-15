/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.CommentPost = function (options) {
		
		//default settings
		var settings = $.extend({
			comments : '.an-comments',		
		}, options );
		
		var form = $(this);
		var formAction = form.find('input[name="action"]').val();
		var comments = form.prev(settings.comments);
		
		$.ajax({
			
			method : 'post',
			
			url : form.attr( 'action' ),
			
			data : form.serialize(),
			
			beforeSend : function () {
				form.fadeTo( 'fast', 0.7 );
			},
			
			success : function ( html ) {
				
				form.fadeTo( 'fast', 1 ).trigger('reset');
				
				if ( formAction == 'addcomment') {
				
					comments.append($(html).fadeIn('slow'));
				
				} else if ( formAction == 'editcomment') {
					
					form.replaceWith($(html).fadeIn('slow'));
				}
			}
		});
	};
	
	$('.an-comments-wrapper').on('submit', 'form', function(event){
		event.preventDefault();
		$(this).CommentPost();
	});
	
}(jQuery, window, document));