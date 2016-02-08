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

			this.form = $(this.options.searchForm);
			var term = this.form.find(this.options.term);
			var elemSort = $(this.options.sortOption);
			var elemComment = $(this.options.commentOption);
			var elemNearby = $(this.options.nearbyOption);
			var elemRange = $(this.options.rangeOption);
			var elemScope = $(this.options.scope);

			this.searchOptions = {
				layout : 'results',
				scope : $(elemScope).data('scope')
			};

			//search form
			this._on( term, {
				change : function( event ) {
					event.preventDefault();
					this.submit(this.form);
				}
			});

			//sort options recent/relevant
			this._on( elemSort, {
				change : function ( event ) {
					event.preventDefault();
					this.searchOptions.sort = $(event.currentTarget).val();
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

			//nearby options search
			this._on( this.form, {
				 'SearchNearby' : function ( event ) {

						elemRange.prop('disabled', false);
						elemSort.find('option[value="distance"]').prop('disabled', false);
						elemSort.val('distance');

						this.searchOptions.range = elemRange.val();
						this.searchOptions.search_nearby = elemNearby.val();
						this.searchOptions.sort = 'distance';
						this.submit(elemNearby);
				 }
			});

			//removing nearby options search
			this._on( elemNearby, {
				 change : function ( event ) {
					 	event.preventDefault();
						if($(event.currentTarget).val() == '') {
								elemRange.prop('disabled', true);
								elemSort.find('option[value="distance"]').prop('disabled', true);
								elemSort.val('relevant');

								this.searchOptions.search_nearby = null;
								this.searchOptions.search_range = null;
								this.searchOptions.sort = 'relevant';
								this.submit(this.form);
						}
				 }
			});

			//elem range
			this._on( elemRange, {
				change : function ( event ) {
					event.preventDefault();
					this.searchOptions.search_range = elemRange.val();
					this.submit($(event.currentTarget));
				}
			});
		},

		changeScope : function( target ) {

				this.searchOptions.scope = $(target).data('scope');

				this.submit($(target));
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

					$(self.options.results).data('fetched-items', response.filter('.an-entity'))

					$(self.element).trigger('masonry-reset-render');

					$(self.options.searchScopes).replaceWith(response.filter(self.options.searchScopes));

				},
				complete : function () {

					currentTarget.fadeTo('fast', 1).removeClass('uiActivityIndicator');

					var newUrl = self.form.attr('action') + '?' + self.form.serialize() + '&' + $.param(self.searchOptions);

					$(self.element).data( 'newUrl',  newUrl ).trigger('urlChange');
				}
			});
		}
	});

	var search = $('#an-search-results').search();

	$('body').on('click', '[data-trigger="ChangeScope"]', function ( event ) {
			event.preventDefault();
			search.search('changeScope', this);
	});

}(jQuery, window, document));
