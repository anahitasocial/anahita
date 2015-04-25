/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.anahitaEntity = function ( action ) {
		
		var entity = $(this).closest('.an-entity');
		
		//read entity
		if ( action == 'read' ) {
			
			var url = $(this).attr('href');
			
			$.ajax({
				method : 'get',
				url : url,
				beforeSend : function () {
					entity.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					
					if( !$(response).length ) {
						response = $(response).html();
					}
						
					entity.html( response ).fadeTo('fast', 1).removeClass('uiActivityIndicator');
				}
			});
			
			return this;
		}
		
		//edit enable
		if ( action == 'enable' ) {
		    
		    var url = $(this).attr('href'); 
		    var action = $(this).data('action');
		    
		    $.ajax({
		        method : 'post',
		        url : url,
		        data : {
		            action : action
		        },
		        beforeSend : function () {
                    entity.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
                },
                success : function ( response ) {
                    entity.replaceWith( response );
                }
		    });
		}
		
		//edit entity
		if ( action == 'edit' ) {
			
			var form = $(this);
			
			$.ajax({
				method : 'post',
				url : form.attr('action') + '?' + 'layout=list',
				data : form.serialize(),
				beforeSend : function () {
					form.find(':submit').attr('disabled', true);
					entity.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					entity.replaceWith($(response));
				}
			});
			
			return this;
		}
		
		//add entity
		if ( action == 'add' ) {
			
			var form = $(this);
			var entities = $('.an-entities');
			
			$.ajax({
				method : 'post',
				url : form.attr('action') + '?' + 'layout=list',
				data : form.serialize(),
				beforeSend : function () {
					form.find(':submit').attr('disabled', true);
					entity.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					form.trigger('reset');
					entities.prepend( response );
				}
			});
			
			return this;
		}
		
		if ( action == 'delete' ) {
			
			var entity = $( this );
			
			var confirmModal = $('#an-modal');
			var header = confirmModal.find('.modal-header').find('h3');
			var body = confirmModal.find('.modal-body');
			var footer = confirmModal.find('.modal-footer'); 
			
			header.text(StringLibAnahita.action.delete);
			body.text(StringLibAnahita.prompt.delete);
			
			var triggerBtn = $('<button class="btn btn-danger"></button>').text(StringLibAnahita.action.delete);
			
			footer.append(triggerBtn);
			
			triggerBtn.on('click', function ( event ) {
				
				$.ajax({
					method : 'post',
					url : entity.attr('href'),
					data : {
						action : entity.data('action')
					},
					beforeSend : function(){
						confirmModal.modal('hide');
					},
					success : function() {
						if(entity.closest('.an-entities').length){
							entity.closest('.an-entity').fadeOut();
						} else {
							window.location.href = entity.data('redirect');
						}	
					}
				});
			});
			
			confirmModal.modal('show');
			
			return this;
		}
		
		//add comment entity
		if ( action == 'addcomment' ) {
			
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
					form.find(':submit').attr('disabled', false);
					form.trigger('reset').fadeTo( 'fast', 1 );
					comments.append($(response).fadeIn('slow'));
				},
				complete : function ( xhr, status ) {
				    
				    if (status == 'error') {
				        form.find(':submit').attr('disabled', false);
				        form.fadeTo( 'fast', 1 );
				    }
				    
				}
			});
			
			return this;
		}	
	};
	
	var readSelectors = 
		'a[data-action="edit"],' +
		'a[data-action="cancel"],' +
		'a[data-action="editcomment"],' +
		'a[data-action="cancelcomment"]';	
	
	//Read Entity Actions
	$('body').on('click', readSelectors, function ( event ) {
		event.preventDefault();
		$(this).anahitaEntity('read');
	});
	
	$('body').on('click', 'a[data-action="enable"], a[data-action="disable"]', function ( event ) {
	    event.preventDefault();
        $(this).anahitaEntity('enable');
	});
	
	//Edit Entity Action
	$('body').on('submit', '.an-entity > form', function ( event ) {
		event.preventDefault();
		$(this).anahitaEntity('edit');
	});
	
	//Add Entity Action
	$('body').on('submit', '#entity-form-wrapper > form', function ( event ) {
		event.preventDefault();
		$(this).anahitaEntity('add');
	});
	
	//Delete Entity Action
	$( 'body' ).on( 'click', 'a[data-action="delete"], a[data-action="deletecomment"]', function( event ) {
		event.preventDefault();
		$(this).anahitaEntity('delete');
	});
	
	//Show/Hide Add Form
	$('body').on('click', '[data-trigger="ReadForm"], [data-trigger="CancelAdd"]', function ( event ) {
		event.preventDefault();
		$('#entity-form-wrapper').slideToggle();
	});
	
	//Add Comment Action
	$('body').on('submit', '.an-comments-wrapper > form', function ( event ) {
		event.preventDefault();
		$(this).anahitaEntity('addcomment');
	});
	
}(jQuery, window, document));