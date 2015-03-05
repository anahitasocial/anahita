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
    
    	options : {
    		
    		selector : '#photo-selector-list',
    		photos : $('#set-photos').find('.media-grid') 
    	},
    	
    	_create : function () {
    		
    		var self = this;
    		
    		this.element.hide();
    		this.selector = null;
    		this.photoList = null;
    		
    		//open organizer
    		this._on('body', {
    			'click a[data-action="organize"]' : function ( event ) {
    				event.preventDefault();
    				$.get( event.currentTarget.href , function( response ){
    	    			
    					self.element.html(response).slideDown();
    					
    	    			self.selector = $(self.options.selector).sortable({
    	    				connectWith : $(self.options.photos),
    	    				scroll: false
    	    			});
    	    			
    	    			self.photoList = $(self.options.photos).sortable({
    	    				connectWith : $(self.options.selector)
    	    			});
    	    			
    	    		});
    			}
    		});
    		
    		this._on('body', {
    			'click a.thumbnail-link' : function ( event ) {
    				event.preventDefault();
    			}
    		});
    		
    		//close organizer 
    		this._on( this.element, {
    			'click a[data-trigger="ClosePhotoSelector"]' : function ( event ) {
    				event.preventDefault();
    				self.element.slideUp('normal', function(){
    					
    					self.selector.sortable('destroy');
    					self.photoList.sortable('destroy');
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
    		
    	}
    });
    
    $('#photo-selector').setOrganizer();
    
}(jQuery, window));  