/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.widget('anahita.commentEdit', {
				
		_create : function () {
			
			var self = this;
			
			this._on( this.element, {
				'click [data-action="editcomment"], [data-action="cancelcomment"]' : function ( event ) {
					event.preventDefault();
					event.stopPropagation();
					self._read( event.currentTarget.href );
				}
			});

			this._on( this.element, {
				'submit' : function ( event ) {
					event.preventDefault();
					event.stopPropagation();
					self._edit( event.target );
				}
			});
		},
		
		_read : function ( url ) {
			
			var self = this;

			$.ajax({
				method : 'get',
				url : url,
				beforeSend : function () {
					self.element.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					
					if ( $(response).is('.an-comment') )
						response = $(response).html();
						
					self.element.html( response ).fadeTo('fast', 1).removeClass('uiActivityIndicator');
				}
			});
		},
		
		_edit : function ( form ) {
			
			var self = this;
			
			$.ajax({
				method : 'post',
				url : $(form).attr('action'),
				data : $(form).serialize(),
				beforeSend : function () {
					$(form).find(':submit').attr('disabled', true);
					self.element.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					self.element.html( $(response).html() );
					self.element.html( response ).fadeTo('fast', 1).removeClass('uiActivityIndicator');
				}
			});
		}
	});
	
	$(document).ajaxSuccess(function() {
		$('.an-comment').commentEdit();
	});
	
	$('.an-comments-wrapper').on('submit', '> form', function() {
		
		event.preventDefault();
		event.stopPropagation();
		
		var form = $(this);
		var comments = form.prev('.an-comments');
		
		$.ajax({
			method : 'post',
			url : form.attr('action'),
			data : form.serialize(),
			beforeSend : function (){
				form.find(':submit').attr('disabled', true);
				form.fadeTo('fast', 0.3);
			},
			success : function ( response ) {
				
				form.trigger('reset').fadeTo( 'fast', 1 );
				comments.append($(response).fadeIn('slow'));
			}
		});
		
	});
	
}(jQuery, window, document));