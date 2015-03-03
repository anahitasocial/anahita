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
			url : null,
			debug : false
		},
		
		_create: function() {
			
			if (this.element.children(this.options.record).length < this.options.limit )
				return;
			
			if(this.options.debug)
				console.log('Instantiated');
			
			this.url = this.options.url || this.element.data('url');
			
			this.records = $(this.element.children(this.options.record));
			
			if(this.options.debug)
				console.log(this.records);
			
			this.start = this.records.length;

			this._getNextRecords();

			var scrollable = $(this.options.scrollable);
			
			var self = this;
			
			scrollable.scroll(function(){
	
				if ( self.options.debug ) {
					
					console.log($(window).scrollTop());
					console.log($(scrollable).height());
				
				}
				
				if ( self.element.is(':visible') && $(window).scrollTop() >= $(scrollable).height() - $(window).height()) {
					self._getNextPage();
				}
			});
			
			// listen to the "urlChange" event
			// if there is one, update the url and refresh records.
			this._on($(document), {
				'urlChange' : function( event ) {
					this.url = $(document).data('newUrl');
					this.records = $(this.element.children(this.options.record));
					this._getNextRecords();
				}
			});
		},
		
		_getNextRecords: function(){
			
			var limit = {
				start : this.records.length,
				limit : this.options.limit * this.options.preload,	
			};
			
			limit = $.param(limit);
			
			if(!this._isFreshLimit(limit))
				return false;
			
			this._rememberLimit(limit);
			
			var self = this;
			
			$.get( this.url + '&' + limit , function ( response ) {
				
				response = $(response);
				
				if(self.options.debug)
					console.log(response.filter(self.options.record));
				
				self.records = $.merge(self.records, response.filter(self.options.record));
			});
		},
		
		_getNextPage: function(){
			
			if ( this.start < this.records.length ) {
				
				for ( var i = 0; i < this.options.limit; i++ ) {
					this.element.append(this.records[ this.start + i ]);
				}

				this.start += this.options.limit;
			}
			
			this._getNextRecords();
		},
		
		_rememberLimit : function(url) {
			this._limit = url;
		},
		
		_isFreshLimit : function (limit) {
			return this._limit != limit;
		}
	});
	
	$(document).ajaxSuccess(function() {
			$('[data-trigger*="InfiniteScroll"]').infinitescroll({
				'debug' : false
			});
	});
	
}(jQuery, window, document));