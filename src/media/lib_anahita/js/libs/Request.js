
/**
 * Request constructor
 */
Request.from = function(element, options) {
	options = options || {};
	var spinnerTarget;
	if ( element.get('tag') == 'a' ) 
	{
		spinnerTarget = element.getParent('ul') || this;
		Object.add(options, {
			method : 'get',
			url	   : element.get('href')
		});
		if ( options.method != 'get' ) 
		{
			//legacy
			var data = element.get('href').toURI().getData();
			if ( element.get('data-data') ) {
				data = JSON.decode(element.get('data-data'));
			}
			Object.add(options, {data : data});
		}
	} else 
	{
		if ( element.get('tag') == 'form' ) 
			Object.add(options,{
				method : element.get('method') || 'get',
				form : element
			});
		else if ( element.form ) {
			Object.add(options,{
				form : element.form
			});				
		}
		
		if ( options.form ) {
			Object.add(options,{
				url  	: options.form.get('action'),
				data 	: options.form,
				method	: options.form.get('method')
			});
		}
	}
	
	if ( instanceOf(options.url, Function) ) {
		options.url = options.url.apply(element)
	}
	
	Object.add(options,{
	    fireSubmitEvent : true,
		useSpinner	    : true,
		spinnerTarget   : spinnerTarget || options.form || element
	});
	
	if ( element.retrieve('request') ) 
		element.retrieve('request').cancel();
	
	var request = null;
	
	//if json request create a json object
	if ( (options.url && options.url.toURI().getData('format') == 'json') || options.format == 'json' )
	    request = new Request.JSON(options);
	else 
		request = new Request.HTML(options);
	
	element.store('request', request);
	
	if ( options.form && options.fireSubmitEvent ) 
	{
		var event = {
			_stop   : false,
			mock	: true,
			request : request,
			stop    : function() {
				event._stop = true;
			},
			preventDefault : function() {
			
			}
		}
		options.form.fireEvent('submit', [event]);
		if ( event._stop ) 
		{
			Object.append(request, {
				send : function() {
					return false;
				}
			});
			return request;
		}
	}
	
	if (  options.form && options.form.retrieve('validator') ) 
	{
		var validator = options.form.retrieve('validator');
		var send 	  = request.send.bind(request);
		if ( options.form.get('remoteValidators').isPending() )
		{
			options.form.addEvent('validationSuccessful', function(){
				send();
			});
		}		
		Object.append(request, {
			send : function() {
				if  ( !validator.validate() ) {
					return false;
				}
				else return send();
			}
		});
	}
	
	return request;	
}

/**
 * Creates an ajax request with the element as the spiner 
 */
Element.implement(
{
	/**
	 * Returns a request object associated with a element, canceling an exsiting one,
	 * it will set the element itself as a spinner target
	 * 
	 * @param  options
	 * @return Request
	 */
	ajaxRequest : function(options) {
		return Request.from(this, options);
	}
});

/**
 * Request Delegagor. Creates a AJAX request 
 */
(function() {
	var getOptions = function(el) 
	{
		if ( !el.retrieve('raw-options') ) 
		{
			var rawOption = el.get('data-request-options') || '{}';
			el.set('data-request-options','{}');
			el.store('raw-options', rawOption);						
		}
		return JSON.decode.bind(el).attempt(el.retrieve('raw-options'));		
	}
	var request = function(el, api) 
	{
		if ( !el.retrieve('raw-options') ) 
		{
			var rawOption = el.get('data-request-options') || '{}';
			el.set('data-request-options','{}');
			el.store('raw-options', rawOption);						
		}
		var options   = getOptions(el);
		//if option is a function then call it
		if ( instanceOf(options, Function) ) {
			options = options.apply(el);
		}
		
		['form','replace','update','remove'].each(function(name){
			var value = api.getAs(String, name) || options[name];
			if ( value ) options[name] = value			
			if ( instanceOf(options[name], String) ) {
				options[name] = el.getElement(options[name]) || document.getElement(options[name]);
			}
		});	
						
		var autoFollow = api.getAs(Boolean, 'redirect') || false;
		
		Object.add(options,{
			onTrigger : Function.from()
		});
		
		var request = el.ajaxRequest(options),
		uri		    = new URI();
		options.onTrigger.apply(el, [request]);
		request.addEvent('success', function() {			
			if ( autoFollow ) 
			{
				location = this.xhr.getResponseHeader('Content-Location') ||
					this.xhr.getResponseHeader('Location');
				
				if ( location ) {
					window.location = location;
				} 
			}
			
		});
		request.send();
	};
	Behavior.addGlobalFilter('Request', {
    	setup : function(el, api)
    	{
    		var options = getOptions(el);    		
    		var form    = document.getElement(api.get('form') || el);
    		
    		if ( !form ) { 
    			return;
    		}
			
    		var sendRequest = function() {
				request(el, api);
			}
    		if ( el != form ) {
    			el.addEvent('click', sendRequest);
    		}
    		
    		form.addEvent('submit', function(e){
    			if ( !e.mock )
    				e.stop();
    		});
    		form.addEvent('keyup', function(e) {    			
    			if ( e.key == 'enter' ) {
    				sendRequest();
    			}
    		});    		
    	}
	});
	Delegator.register(['click'],'Request', 
	{
		handler  : function(event, el, api) 
		{
			event.stop();
			request(el,api);
		}
	});	
})();
