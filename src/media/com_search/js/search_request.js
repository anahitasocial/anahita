(function(){	
	var search_form    = null;
	var search_options = {};
	var submit_form = function() 
	{
		search_options['layout'] = 'results_scopes';		
		//console.log(search_options);
		var url = search_form.get('action').toURI().setData(search_options);		
		search_form.ajaxRequest({
			url : url.toString(),
			evalScripts : false,
			onSuccess : function() {
				var updates = ['.search-scopes','.an-entities-wrapper'];
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
		search_options = {scope:el.get('href').toURI().getData('scope')};
		
		el.ajaxRequest({
			data : search_options,
			spinnerTarget : el,
			evalScripts : false,
			onSuccess : function() {				
				document.getElement('.an-entities-wrapper')
					.set('html', this.response.html)
			}
		}).send();
	});	
})()