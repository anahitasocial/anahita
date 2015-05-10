/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.widget("anahita.infinitescroll", {
		
		options: {
			record : '.an-entity',
			window : window,
			scrollable : document,
			preload: 3,
			limit : 20,
			url : null
		},
		
		_create: function() {
			
			if (this.element.children(this.options.record).length < this.options.limit )
				return;

            this.url = this.element.data('url') || this.options.url;
            this.records = this.element.children(this.options.record);
            this.start = this.records.length;
            
            this._preload();

			var self = this;
			
			// listen to the "urlChange" event
            // if there is one, update the url and refresh records.
            this._on( $(document) , {
                
                'urlChange' : function( event ) {

                    self.element.data('url', $(document).data('newUrl'));
                    this.url = this.element.data('url');
                    this.records = this.element.children(this.options.record);
                    this.start = this.records.length;
                    
                    this._setNewLimit(null);
                    
                    this._preload();
                }
            });
			
			var scrollable = $(this.options.scrollable);
			
			scrollable.scroll(function(){
				
				if ( self.element.is(':visible') && $(window).scrollTop() + ($(window).height() * 1.3 ) >= $(scrollable).height()) {
					self._getNextPage();
				}
			});
		},
		
		_preload: function(){
			
			var limit = $.param({
				start : this.records.length,
				limit : this.options.limit * this.options.preload,	
			});
			
			if(!this._isNewLimit( limit )) {
			    return false;
			}
			
			this._setNewLimit( limit );
			
			$.ajax({
			    method : 'get',
			    url : this.url + '&' + limit,
			    success : function ( response ) {
			       
			       response = $(response);
                   this.records = $.merge( this.records, response.filter( this.options.record )); 

			    }.bind( this )
			});
		},
		
		_getNextPage: function() {
			
			if ( this.start < this.records.length ) {
				
				for ( var i = 0; i < this.options.limit; i++ ) {
					this.element.append(this.records[ this.start + i ]);
				}

				this.start += this.options.limit;
			}
			
			this._preload();
		},
		
		_isNewLimit : function ( limit ) {
		    return this._limit != limit;
		},
		
		_setNewLimit : function ( limit ) {
		    this._limit = limit;
		}
	});
	
	if ( $('[data-trigger="InfiniteScroll"]').length ) {
	   $('[data-trigger="InfiniteScroll"]').infinitescroll(); 
	}
	
	$(document).ajaxSuccess(function() {
		
		var elements = $('[data-trigger="InfiniteScroll"]');
		
		$.each(elements, function( index, element ){
		    
		    if( !$(element).is(":data('anahita-infinitescroll')") ) {
		    
		      $(element).infinitescroll();
		    
		    }
		});
	});
	
}(jQuery, window, document));