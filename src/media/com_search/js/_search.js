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
			term : 'input[name=term]',
			sortOption : 'select[data-trigger="SortOption"]',
			commentOption : 'input[data-trigger="SearchOption"]',
			nearbyOption : 'input[data-trigger="SearchNearby"]',
			rangeOption : 'select[data-trigger="SearchRange"]',
			scope : '[data-trigger="ChangeScope"]',
			results : '#an-search-results',
			searchScopes : '.search-scopes'
		},

		_create : function() {

			var self = this;
			this.form = $(this.options.searchForm);
			this.term = this.form.find(this.options.term);
			this.sort = $(this.options.sortOption);
			this.comments = $(this.options.commentOption);
			this.nearby = $(this.options.nearbyOption);
			this.range = $(this.options.rangeOption);
			this.scope = $(this.options.scope);

			this.searchOptions = {
				scope : 'all',
				sort : 'recent',
				search_comments : false,
				search_nearby : null,
				search_range : null,
			};

			//search form
			this._on( this.term, {
				change : function( event ) {
					event.preventDefault();
					this.submit(this.form);
				}
			});

			//change scope
			this._on( this.scope, {
				click : function( event ) {
					 event.preventDefault();
					 $(this.options.searchScopes).find('li').removeClass('active');
	 				 $(event.currentTarget).parent().addClass('active');
					 this.submit($(event.currentTarget));
				}
			});

			//sort options recent/relevant
			this._on( this.sort, {
				change : function ( event ) {
					event.preventDefault();
					this.submit($(event.currentTarget));
				}
			});

			//sort options search comments
			this._on( this.comments, {
				change : function ( event ) {
					event.preventDefault();
					this.submit($(event.currentTarget));
				}
			});

			//nearby options search
			this._on( this.form, {
				 'SearchNearby' : function ( event ) {

						this.range.prop('disabled', false);
						this.sort.find('option[value="distance"]').prop('disabled', false);
						this.sort.val('distance');
						this.searchOptions.sort = 'distance';
						this.submit(this.nearby);
				 }
			});

			//removing nearby options search
			this._on( this.nearby, {
				 change : function ( event ) {
					 	event.preventDefault();
						if($(event.currentTarget).val() == '') {
								this.range.prop('disabled', true);
								this.sort.find('option[value="distance"]').prop('disabled', true);
								this.sort.val('relevant');

								this.searchOptions.search_nearby = null;
								this.searchOptions.search_range = null;
								this.searchOptions.sort = 'relevant';
								this.submit(this.form);
						}
				 }
			});

			//elem range
			this._on( this.range, {
				change : function ( event ) {
					event.preventDefault();
					this.submit($(event.currentTarget));
				}
			});
		},

		submit : function( currentTarget ) {

			var self = this;

			this.searchOptions = $.extend(this.searchOptions, {
					term : this.term.val(),
					sort : this.sort.val(),
					scope : currentTarget.data('scope'),
					search_comments : this.comments.is(':checked'),
					search_nearby : this.nearby.val(),
					search_range : (this.nearby.val()) ? this.range.val() : null,
			});

			$.ajax({
				url : this.form.attr('action'),
				data : this.searchOptions,
				beforeSend : function () {
					currentTarget.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response, b, c ) {

					response = $(response);

					$(self.options.results).data('fetched-items', response.filter('.an-entity'))

					$(self.element).trigger('entities-reset-render');
				},
				complete : function () {

					currentTarget.fadeTo('fast', 1).removeClass('uiActivityIndicator');

					var newUrl = self.form.attr('action') + '?' + $.param(self.searchOptions);

					$(self.element).data( 'newUrl',  newUrl ).trigger('urlChange');
				}
			});
		}
	});

	var search = $('#an-search-results').search();

}(jQuery, window, document));
