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
			scope : 'a[data-trigger="ChangeScope"]'
		},
		
		_create : function() {
		
			this.form = $(this.options.searchForm); 
			this.sort = $(this.options.sortOption);
			this.comment = $(this.options.commentOption);
			this.scope = $(this.options.scope);
			
			//search form
			this._on(this.form, {
				submit : function( event ) {
					event.preventDefault();
					console.log(this.form);
				}
			});
			
			//sort options
			this._on(this.sort, {
				change : function ( event ) {
					event.preventDefault();
					console.log(this.sort);
				}
			});
			
			//sort options
			this._on(this.comment, {
				change : function ( event ) {
					event.preventDefault();
					console.log(this.comment);
				}
			});
			
			//change scope
			this._on(this.scope, {
				click : function ( event ) {
					event.preventDefault();
					console.log(this.scope.attr('href'));
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