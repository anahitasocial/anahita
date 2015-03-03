/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function($, window) {
    
    'use strict';
    
    $.widget('anahita.setOrganizer', {
    
    	_create : function () {
    		
    		var self = this;
    		
    		this.element.hide();
    		
    		//open organizer
    		this._on('body', {
    			'click a[data-action="organize"]' : function ( event ) {
    				event.preventDefault();
    				$.get( event.currentTarget.href , function( response ){
    	    			self.element.html(response).slideDown();
    	    		});
    			}
    		});
    		
    		//close organizer 
    		this._on( this.element, {
    			'click a[data-trigger="ClosePhotoSelector"]' : function ( event ) {
    				event.preventDefault();
    				self.element.slideUp('normal', function(){
    					self.element.empty();
    				});
    			}
    		});
    		
    		//update set
    		this._on( this.element, {
    			'click a[data-trigger="UpdateSet"]' : function ( event ) {
    				event.preventDefault();
    				self._update();
    			}
    		});
    	},
    	
    	_update : function () {
    		console.log('Update Set');
    	}
    });
    
    $('#photo-selector').setOrganizer();
    
}(jQuery, window));  