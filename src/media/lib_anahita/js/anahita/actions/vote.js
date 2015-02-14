/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.AnActionVote = function(type) {
		
		type = type || '';
		var elem = $(this);
		var voteCountWrapper = $('#vote-count-wrapper-' + elem.data('nodeid'));

		$.ajax({
			type : 'post',
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
				voteCountWrapper.html(response);
					
			}.bind(elem),
			complete : function(){
				elem.fadeTo('fast', 1);
			}
		});
		
		return this;
	};
	
	//vote
	$('body').on('click', 'a.action-vote, a.action-unvote', function( event ) {
		event.preventDefault();
		$(this).AnActionVote();
	});
	
	//unvote
	$('body').on('click', 'a.action-votecomment, a.action-unvotecomment', function( event ) {
		event.preventDefault();
		$(this).AnActionVote('comment');
	});
	
	//show voters in a modal
	$('body').on('click', 'a[data-toggle*="Voters"]', function ( event ){
		
		event.preventDefault();
		
		var votersModal = $('#an-modal');
		var header = votersModal.find('.modal-header').find('h3');
		var body = votersModal.find('.modal-body');

		$.get($(this).attr('href'), function (response){
			
			header.html($(response).filter('.modal-header').html());
			body.html($(response).filter('.modal-body').html());
			
			votersModal.modal('show');
		}); 
	});
	
}(jQuery, window, document));