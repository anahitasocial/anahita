/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.widget("anahita.search", {
		
		options : {
			searchForm : 'form[data-trigger="SearchRequest"]',
			sortOption : 'select[data-trigger="SortOption"]',
			commentOption : 'input[data-trigger="SearchOption"]',
			scope : '[data-trigger="ChangeScope"]',
			results : '#an-search-results',
			searchScopes : '.search-scopes'
		},
		
		_create : function() {
		
			this.form = $(this.options.searchForm); 

			var elemSort = $(this.options.sortOption);
			var elemComment = $(this.options.commentOption);
			var elemScope = $(this.options.scope);
			
			this.searchOptions = {
				layout : 'results',
				sort : $(elemSort).find('option:selected').val(),
				'search_comments' : $(elemComment).is(':checked'),
				scope : $(elemScope).data('scope')
			};
			
			//search form
			this._on(this.form, {
				submit : function( event ) {
					event.preventDefault();
					this.submit(this.form);
				}
			});
			
			//sort options recent/relevant
			this._on( elemSort, {
				change : function ( event ) {
					event.preventDefault();
					this.searchOptions.sort = $(event.currentTarget).find('option:selected').val();
					this.submit($(event.currentTarget));
				}
			});
			
			//sort options search comments
			this._on( elemComment, {
				change : function ( event ) {
					event.preventDefault();
					this.searchOptions.search_comments = $(event.currentTarget).is(':checked');
					this.submit($(event.currentTarget));
				}
			});
			
			this._initScopes();
		},
		
		_initScopes : function() {
			
			//change scope
			this._on( $(this.options.scope) , {
				
				click : function ( event ) {
					
					event.preventDefault();
	
					this.searchOptions.scope = $(event.currentTarget).data('scope');
					
					this.submit($(event.currentTarget));
				}
			});
		},
		
		submit : function( currentTarget ) {
			
			var self = this;

			$.ajax({
				method : 'get',
				url : this.form.attr('action') + '?' + this.form.serialize(),
				data : this.searchOptions,
				beforeSend : function () {
					currentTarget.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response, b, c ) {
					
					response = $(response);
					$(self.options.results).html(response.filter('.an-entity'));
					$(self.options.searchScopes).replaceWith(response.filter(self.options.searchScopes));
					self._initScopes();
					
				},
				complete : function () {
					
					currentTarget.fadeTo('fast', 1).removeClass('uiActivityIndicator');
					var newUrl = self.form.attr('action') + '?' + self.form.serialize() + '&' + $.param(self.searchOptions);
					$(document).data( 'newUrl',  newUrl ).trigger('urlChange');
				}
			});
		}
	});
	
	$('body').search();
	
}(jQuery, window, document));