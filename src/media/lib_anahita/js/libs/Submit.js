(function() {
	function submitter()
	{
		var form 	  = this;
		var validator = form.retrieve('validator');
		if ( validator )
		{
			event = {
				_stop : false,
				preventDefault : function() {
					event._stop = true; 
				}
			}
			if ( validator.validate(event) && 
					!event._stop ) 
			{
				form.submit();
			}
		} else {
			form.submit();			
		}
	}
	
	Behavior.addGlobalFilter('Submit', {
    	setup : function(el, api)
    	{
    		var form = document.getElement(api.get('form') || el);
    		
    		if ( !form ) { 
    			throw 'No form specified for the Submit Behaviort for element' + el
    		}
			var submit = submitter.bind(form);    		
    		if ( el != form ) {
    			el.addEvent('click', submit);
    		}
    		form.addEvent('keyup', function(e) {
    			if ( e.key == 'enter' ) {
    				submit();
    			}
    		});
    	}
	});	
	Delegator.register('click', {
		'Submit' : function(event, el, api) {
			event.stop();
			if ( el.hasClass('disabled') ) {
			    return false;
			}
			if ( api.get('form') || el.form ) 
			{
				var form   = api.get('form') ? document.getElement(api.get('form')) : el.form;
				var submit = submitter.bind(form);
			} else {
				var url    = api.get('url') || el.get('href');
				var data   = JSON.decode(el.get('data-data')) || el.get('href').toURI().getData();
				var target = api.get('target') || el.get('target');
				var form   = Element.Form({action:url, data:data});
				var spinner   = el.getElement(api.get('spinner')) || el;
				if ( target ) {
					form.set('target', target);
				}	
				var submit = function(){
					if ( spinner ) spinner.spin();
					form.inject(document.body, 'bottom');
					form.submit();			
				}
			}
			
			if ( api.get('promptMsg') ) {
				api.get('promptMsg').prompt({onConfirm:submit});
			}		
			else {			
				submit();
			}
		}		
	});
})();