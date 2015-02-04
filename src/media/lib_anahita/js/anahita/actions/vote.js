/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.actionVote = function(type) {
		
		type = type || '';
		var elem = $(this);
		var voteCountWrapper = $('#vote-count-wrapper-' + elem.data('nodeid'));

		$.ajax({
			type : 'POST',
			url : elem.attr('href'),
			data : {
				action : elem.data('action')
			},
			beforeSend : function(){
				elem.fadeTo('fast', 0.3);
			}.bind(elem),
			success : function(response){
				
				if(elem.data('action') == ('vote' + type))
				{
					elem.data('action', 'unvote' + type);
					elem.text(StringLibAnahita.action.unvote);
				}	
				else if(elem.data('action') == ('unvote' + type))
				{
					elem.data('action', 'vote' + type);
					elem.text(StringLibAnahita.action.vote);
				}	
				
				elem.toggleClass('action-vote' + type).toggleClass('action-unvote' + type);
				elem.fadeTo('fast', 1);
				voteCountWrapper.html(response);
					
			}.bind(elem)
		});
		
		return this;
	};
	
	$('body').on('click', 'a.action-vote', function( event ) {
		event.preventDefault();
		$(this).actionVote();
	});
	
	$('body').on('click', 'a.action-unvote', function( event ) {
		event.preventDefault();
		$(this).actionVote();
	});
	
	$('body').on('click', 'a.action-votecomment', function( event ) {
		event.preventDefault();
		$(this).actionVote('comment');
	});
	
	$('body').on('click', 'a.action-unvotecomment', function( event ) {
		event.preventDefault();
		$(this).actionVote('comment');
	});
	
}(jQuery, window, document));