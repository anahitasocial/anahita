/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.widget("anahita.infinitscroll", {
		
		options: {
			record : '.an-entity',
			scrollable : window,
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
			
			this.records = $(this.element.children(this.options.record));
			
			if(this.options.debug)
				console.log(this.records);
			
			this.start = this.records.length;

			this._getNextRecords();

			var scrollable = $(this.options.scrollable);
			var self = this;
			
			scrollable.scroll(function(){
				if (self.element.is(':visible') && scrollable.scrollTop() >= $(document).height() - scrollable.height() )
					self._getNextPage();
			});
		},
		
		_getNextRecords: function(){
			
			var self = this;
			
			$.ajax({
				url : this.options.url,
				data : {
					start : this.records.length,
					limit : this.options.limit * this.options.preload,
				},
				success : function(html){
					
					var html = $(html);
					
					if(this.options.debug)
						console.log(html.filter(this.options.record));
					
					this.records = $.merge(this.records, html.filter(this.options.record));
				}.bind(this)
			});
		},
		
		_getNextPage: function(){
			
			if ( this.start < this.records.length ) {
				
				for ( var i = 0; i < this.options.limit; i++ ){
					this.element.append(this.records[ this.start + i ]);
				}

				this.start += this.options.limit;
			}
			
			this._getNextRecords();
		}
	});
	
}(jQuery, window, document));