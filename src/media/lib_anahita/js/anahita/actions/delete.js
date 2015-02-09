/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.fn.actionDelete = function () {
		
		var elem = $( this );
		var confirmModal = $('#an-modal');
		
		confirmModal.find('.modal-header').find('h3').text(StringLibAnahita.action.delete);
		confirmModal.find('.modal-body').text(StringLibAnahita.prompt.confirmDelete);
		
		var triggerBtn = confirmModal.find('.modal-footer').find('.btn-primary');
		
		triggerBtn.text(StringLibAnahita.action.delete);
		
		triggerBtn.on('click', function(event){

			$.ajax({
				
				method : 'post',
				url : elem.attr('href'),
				data : {
					action : elem.data('action')
				},
				
				beforeSend : function(){
					confirmModal.modal('hide');
				},
				
				success : function() {
					
					if(elem.data('redirect')){
						window.location.href = elem.data('redirect');
					} else {
						elem.closest('.an-entity').fadeOut();
					}
					
				}.bind(elem)
			});
		});
			
		confirmModal.modal('show');	
	};
	
	$( 'body' ).on( 'click', 'a.action-delete', function( event ) {
		
		event.preventDefault();
		
		$(this).actionDelete();
	});
	
}(jQuery, window, document));