/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.anahitaActor = function ( action ) {
		
		var elem = $( this );
		
		var modal = $('#an-modal');
		var mHeader = modal.find('.modal-header').find('h3');
		var mBody = modal.find('.modal-body');
		var mFooter = modal.find('.modal-footer');
		
		if ( action == 'socialgraph' )
		{
			$.ajax({
				method : 'post',
				url : elem.attr('href'),
				data : {
					'action' : elem.data('action'),
					'actor' : elem.data('actor')
				}, 
				beforeSend : function () {
					elem.addClass('disabled');
				},
				success : function (response){
					
					var listEntity = elem.closest('.an-entity');
					
					if ( $(listEntity).length ) {
						elem.closest('.an-entity').replaceWith($(response));
					} else {
						window.location.href = elem.attr('href');
					}
				}
			});
			
			return this;
		}	
		
		if ( action == 'notifications' ) {
			
			$.get(this.attr('href'), function ( response ) {

				mHeader.html( $(response).filter('mheader').html() );
				mBody.html( $(response).filter('mbody').html() );
				mFooter.html( $(response).filter('mfooter').html() );
				modal.modal('show');
			});
			
			return this;
		}
		
		if ( action == 'addadmin' ) {
			
			var form = $(this);
			var adminId = form.find(':input[name="adminid"]').val();

			$.ajax({
				method : 'post',
				url : form.attr('action'),
				data : form.serialize(),
				beforeSend : function () {
					form.find(':submit').attr('disabled', true);
				},
				success : function () {
				
					form.trigger('reset');
					form.find(':submit').attr('disabled', false);
					window.location.reload();
				}
			});
			
			return this;
		}
		
		if ( action == 'addadmin' || action == 'removeadmin' ) {
			
			$(this).attr('disabled', true);
			
			$.ajax({
				method : 'post',
				url : this.href,
				data : $(this).data(),
				success : function () {
					window.location.reload();
				}
			});
			
			return this;
		}
		
		if ( action == 'manageapps' ) {
			
			$.ajax({
				method : 'post',
				url : elem.href,
				data : elem.data(),
				beforeSend : function () {
					elem.toggleClass('disabled');
				},
				success : function () {
					
					elem.toggleClass('disabled').toggleClass('btn-primary');
					
					if ( elem.attr('data-action') == 'addapp' ) {
					
						elem.attr('data-action', 'removeapp').text(StringLibAnahita.action.disable);
					
					} else {
					
						elem.attr('data-action', 'addapp').text(StringLibAnahita.action.enable);
					
					}
				}
			});
			
			return this;
		}
		
		if ( action == 'delete' ) {
			
			mHeader.text(StringLibAnahita.action.delete);
			mBody.text(StringLibAnahita.prompt.deleteActor);
			
			var triggerBtn = $('<button class="btn btn-danger"></button>').text(StringLibAnahita.action.delete);
			
			mFooter.append( triggerBtn );
			
			modal.modal('show');
			
			triggerBtn.on('click', function ( event ) {
				
				triggerBtn.attr('disabled', true);
				elem.closest('form').trigger('submit');
				
			});
			
			return this;
		}
		
		if ( action == 'delete-file' ) {
			
			var form = elem.closest('form');
			
			form.find(':file').attr('value', null);
			
			form.submit();
			
			return this;
		}
		
		if ( action == 'add-file' ) {
			
			var form = elem.closest('form');
			
			elem.inputAlert();
            elem.inputAlert('clear');
            
            var limit = elem.data('limit') * 1024 * 1024;
            var size = form.find(':file')[0].files[0].size;

			if( size > limit )
			{
			    elem.inputAlert('error', 'This file is too large' );
			    return false;
			}
			
			elem.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
			
			form.submit();
			
			return this;
		}
		
		return false;
	};
	
	//Social Graph
	var socialgraphSelectors = 
		'[data-action="confirmrequest"],' +
		'[data-action="ignorerequest"],' +
		'[data-action="addrequest"],' +
		'[data-action="deleterequest"],' +
		'[data-action="follow"],' +
		'[data-action="unfollow"],' +
		'[data-action="block"],' +
		'[data-action="unblock"],' +
		'[data-action="lead"],' +
		'[data-action="unlead"]';
	
	$( 'body' ).on( 'click', socialgraphSelectors, function( event ) {
	
		event.preventDefault();
		$(this).anahitaActor( 'socialgraph' );
	
	});
	
	//notifications settings	
	$('body').on( 'click', '[data-action="notifications-settings"]', function ( event ) {
		
		event.preventDefault();
		$(this).anahitaActor('notifications');
		
	});

	//manage admins
	$.fn.actorTypeahead = function () {
		
		var form = this;
		var actors = [];
		var map = {};
		
		form.find('input:text').typeahead({
			minLength : 3,
			source : function ( query, process ) {

				var self = this;
				
				return $.ajax({
					method : 'get',
					url : form.attr('action'),
					headers: { 
	                    accept: 'application/json'
	                },
	                dataType : 'json',
					data : {
						get : 'candidates',
						value : query
					},
					beforeSend : function () {
						self.$element.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
					},
					success : function ( response ) {
						
						var actors = [];

						$.each(response, function(i, actor) {
				            map[actor.value] = actor;
				            actors.push(actor.value);
				        });

						process(actors);
						
						self.$element.fadeTo('fast', 1).removeClass('uiActivityIndicator');
					}
				});
			},
			updater: function(item) {
		        form.find('[name="adminid"]').attr('value', map[item].id);
		        return item;
		    }
		});
	};
	
	//Admin Candidate Typeahead
	if( $('form#an-actors-search').length ) {
	   
	   $('form#an-actors-search').actorTypeahead();    
	}
	
	
	//Add Admin
	$('body').on('submit', 'form#an-actors-search', function( event ){
		
		event.preventDefault();
		$(this).anahitaActor('addadmin');
		
	});
	
	//Remove Admin
	$('body').on('click', '[data-action="removeadmin"]', function () {
		
		event.preventDefault();
		$(this).anahitaActor('removeadmin');
	});
	
	//manage apps
	$('body').on('click', '[data-action="addapp"], [data-action="removeapp"]', function ( event ) {
		
		event.preventDefault();
		$(this).anahitaActor('manageapps');

	});
	
	//Delete Actor
	$('body').on('click', '[data-trigger="DeleteActor"]', function ( event ) {
		
		event.preventDefault();
		$(this).anahitaActor('delete');
	});
	
	//Delete Avatar
	$('body').on('click', '[data-trigger="DeleteAvatar"], [data-trigger="DeleteCover"]', function ( event ) {
		
		event.preventDefault();
		$(this).anahitaActor('delete-file');
	});
	
	//Add Avatar
	$('form#actor-avatar,form#actor-cover').on('change', ':file', function ( event ) {
		
		event.preventDefault();
		$(this).anahitaActor('add-file');
	});
	
	//show actors in a modal
    $('body').on('click', 'a[data-trigger="Actors"]', function ( event ){
        
        event.preventDefault();

        var actorsModal = $('#an-modal');
        
        var header = actorsModal.find('.modal-header').find('h3');
        var body = actorsModal.find('.modal-body');

        $.get( $(this).attr('href'), function (response){
            
            header.html($(response).filter('.modal-header').html());
            body.html($(response).filter('.modal-body').html());
            
            actorsModal.modal('show');
        }); 
    });
	
}(jQuery, window, document));