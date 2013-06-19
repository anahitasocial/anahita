
/*
---
description: Adds an instance of Form.Validator.Inline to any form with the class .form-validator.
provides: [Behavior.FormValidator]
requires: [Behavior/Behavior, More/Form.Validator.Inline, More/Object.Extras]
script: Behavior.FormValidator.js
name: Behavior.FormValidator
...
*/



Delegator.register('blur','ValidateCoupon', function(event, el) {
	alert('d');
	var value = el.value;
	if ( !value.match(/\S/) )
		return;
	el.spin();
	return;
	this.ajaxRequest({
		url 	: document.location + "",
		data	: {action:'validate_coupon', coupon_code:value},
		update  : 'an-sb-validation-msg'
	}).send();
})

document.addEvent('click:relay(.confirm-tos)', function(){
    var allChecked = true;
    document.getElements('.confirm-tos').each(function(el){
        if ( allChecked && !el.checked ) {
            allChecked = false;
        }
    });
    document.id('proceed').removeClass('disabled');
    if ( !allChecked ) {
        document.id('proceed').addClass('disabled');
    }    
});

document.addEvent('click:relay(#proceed)', function(e){
   el = e.target;
   if ( el.hasClass('disabled') ) {
       e.stop();
   }
});