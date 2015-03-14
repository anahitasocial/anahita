/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';

	var lastOpened = null;

	$.fn.StoryComment = function (action, options) {
		
		//default settings
		var settings = $.extend({
			
			entity : '.an-entity',
			comments : '.an-comments',
			actionOvertext : '.action-comment-overtext',
			overtextBox : '.comment-overtext-box'
		
		}, options );
		
		var elem = $(this);
		var parent = elem.closest(settings.entity);
		var comments = parent.find(settings.comments);
		var form = parent.find('form:last-child');
		
		if(!form)
			return;
		
		//show form
		this.ShowForm = function() {
			
			this.HideLastForm();	
			
			form.show();
			
			lastOpened = form;
			
			$('body').animate({ scrollTop: $(form).offset().top - 150 }, 
				700, 
				function(){
		    		form.find('textarea').focus();
		    	});
			
			return this;
		};
		
		//hide last form
		this.HideLastForm = function() {
			
			var hotspot = null;
			
			if ( parent.find( settings.actionOvertext ).length )
				var hotspot = parent.find( settings.overtextBox );
			
			if ( hotspot ) {
				hotspot.hide();
				form.data('hotspot', hotspot);
			}
			
			if ( lastOpened && lastOpened.attr('id') != form.attr('id') ) {
				
				if(lastOpened.data('hotspot'))
					lastOpened.data('hotspot').show();
				
				lastOpened.hide();
			}
			
			return this;
		};
		
		//actions for this plugin
		switch ( action ) {
		
			case 'hide':
				this.HideLastForm();
				break;
			
			case 'show':
			default:
				this.ShowForm();
		}
		
		return this;
	};
	
	$( 'body' ).on( 'click', 'a.action-comment, a.action-comment-overtext', function( event ) {
		
		event.preventDefault();

		$(this).StoryComment('show');
	});
	
	$( '#an-stories' ).on( 'clickoutside', function( event ) {
		
		$(this).StoryComment('hide');
	
	});
	
}(jQuery, window, document));
