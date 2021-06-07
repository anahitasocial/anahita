/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';

	$.widget('anahita.entityEditable', {
		
		options : {
			container : '.entity-description-wrapper'
		},
		
		_create : function () {
			
			var self = this;
			this.container = this.element.find(self.options.container);
			
			this._on( this.element, {
				'click .entity-title, .entity-description' : function ( event ) {
					event.preventDefault();
					self._read('edit');
				}
			});
			
			this._on( this.element, {
				'click [data-trigger="EditableCancel"]' : function ( event ) {
					event.preventDefault();
					self._read('default');
				}
			});
			
			this._on( this.element, {
				'submit form' : function ( event ) {
					event.preventDefault();
					self._edit();
				}
			});
		},
		
		_read : function (layout) {
			
			var self = this;
			
			$.ajax({
				method : 'get',
				url : this.element.data('url'),
				data : {
					layout : layout
				},
				beforeSend : function () {
					self.container.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					
					if(layout != 'edit')
						response = $(response).find(self.options.container).html();
					
					self.container.html(response).fadeTo('fast', 1).removeClass('uiActivityIndicator');
				}
			});
		},
		
		_edit : function () {
			
			var self = this;
			var form = this.container.find('form');
	
			$.ajax({
				method : 'post',
				url : form.attr('action'),
				data : form.serialize(),
				beforeSend : function () {
					self.container.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					response = $(response).find(self.options.container).html();
					self.container.html(response).fadeTo('fast', 1).removeClass('uiActivityIndicator');
				}
			});
			
		}
	});
	
	if ( $('.an-entity.editable').length ) {
	  $('.an-entity.editable').entityEditable();  
	}
	
    $(document).ajaxSuccess(function() {
        
        var elements = $('.an-entity.editable');
        
        $.each(elements, function( index, element ){
            
            if( !$(element).is(":data('anahita-entityEditable')") ) {
            
              $(element).entityEditable();
            
            }
        });
    });

}(jQuery, window, document));