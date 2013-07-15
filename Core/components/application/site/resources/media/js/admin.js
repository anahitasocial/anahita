/**
 * Adds a validator for the Koowa.Form 
 */
Behavior.addGlobalPlugin('FormValidator','Koowa.Form', {
	setup: function(element, api, instance) {
		var controller = element.retrieve('controller');		
		if ( controller ) {
			controller.addEvent('validate', function(){
				return instance.validate();
			});
		}
	}
})

window.addEvent('domready', function(){
    if ( $('limit') )
        $('limit').addEvent('change', function(e) {
            try {
                submitform();
            }catch(Exception) {
                new URI().setData('limit', e.target.value).go();
            }        
        });
});