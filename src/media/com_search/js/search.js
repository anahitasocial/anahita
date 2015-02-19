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
			
			this.formData = [];
			
			this.formData['layout'] = 'results';
			this.formData['sort'] = $(elemSort).find('option:selected').val();
			this.formData['search_comments'] = $(elemComment).is(':checked');
			this.formData['scope'] = $(elemScope).data('scope');
			
			//search form
			this._on(this.form, {
				submit : function( event ) {
					event.preventDefault();
					this.submit(this.form);
				}
			});
			
			//sort options
			this._on( elemSort, {
				change : function ( event ) {
					event.preventDefault();
					this.formData['sort'] = $(event.currentTarget).find('option:selected').val();
					this.submit($(event.currentTarget));
				}
			});
			
			//sort options
			this._on( elemComment, {
				change : function ( event ) {
					event.preventDefault();
					this.formData['search_comments'] = $(event.currentTarget).is(':checked');
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
					this.formData['scope'] = $(event.currentTarget).data('scope');
					this.submit($(event.currentTarget));
				}
			});
		},
		
		submit : function( currentTarget ) {
			
			var self = this;

			$.ajax({
				method : 'get',
				action : this.form.attr('action'),
				data : {
					
					'layout' : 'results',
					'scope' : this.formData['scope'],
					'search_comments' : this.formData['search_comments'],
					'sort' : this.formData['sort']
				
				},
				beforeSend : function () {
					currentTarget.fadeTo('fast', 0.3).addClass('uiActivityIndicator');
				},
				success : function ( response ) {
					
					response = $(response);
					$(self.options.results).html(response.filter('.an-entity'));
					$(self.options.searchScopes).replaceWith(response.filter(self.options.searchScopes));
					self._initScopes();
				},
				complete : function () {
					currentTarget.fadeTo('fast', 1).removeClass('uiActivityIndicator');
				}
			});
			
		}
	});
	
	var search = $('body').search();
	
}(jQuery, window, document));		

/*
(function(){	
	var search_form    = null;
	var search_options = {};
	var submit_form = function() 
	{
		search_options['layout'] = 'results';	
		//console.log(search_options);
		var url = search_form.get('action').toURI().setData(search_options);		
		search_form.ajaxRequest({
			url : url.toString(),
			evalScripts : false,
			onSuccess : function() {
				var updates = ['.search-scopes','.an-entities'];
				var html  = this.response.html.parseHTML();			
				
				updates.each(function(selector){
					var newEl = html.getElement(selector);
					var oldEl = document.getElement(selector);
					if ( oldEl ) {
						newEl ? newEl.replaces(oldEl) : oldEl.remove();
					}
				});
			}
		}).send();		
	}

	'form[data-trigger="SearchRequest"]'.addEvent('domready', function(){		
		search_form = this;
	});
	
	'form[data-trigger="SearchRequest"]'.addEvent('submit', function(e){
		e.stop();
		search_form = this;
		submit_form();		
	});	

	Delegator.register('change',{'SortOption': function(event, el, api) {
		search_options[el.name] = el.options[el.selectedIndex].value;		
		submit_form();
	}});
	
	Delegator.register('change',{'SearchOption': function(event, el, api) {
		search_options[el.name] = el.checked ? el.value : 0;
		submit_form();
	}});
	
	Delegator.register('click','ChangeScope', function(event, el, api) {
			
		event.stop();			
		el.getParent('ul').getElements('li').removeClass('active');
		el.getParent('li').addClass('active');
		search_options['scope'] = el.get('href').toURI().getData('scope');
		search_options['layout'] = 'results';
		submit_form();
	});	
})()
*/