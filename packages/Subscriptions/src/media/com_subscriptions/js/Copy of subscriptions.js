/**
 * 
 */
//$Event('window domready', function(){
//	$$('label').each(function(el){
//		new Element('span',{html:'*', 'class':'an-se-label-required'}).inject(el);
//	});
//});
	
/**
 * Validates a coupon
 * 
 * @return
 */
$Event('#sbCouponValue blur', function(e, el){
	//check value. must at least be one charater
	
	e.stop();
	
	var value = document.id('sbCouponValue').value;
	if ( !value.match(/\S/) )
		return;
	var request = new Request.HTML({
		url 	: document.location + "",
		data	: {action:'validate_coupon', coupon_code:value},
		update  : 'an-sb-validation-msg'
	}).send();
});

$Event('.hkSbProcces click', function(e, el) {
	el.form.coupon_code.value = document.id('sbCouponValue').value;	
});

$Event('.hkLink click', function(e, el){
	document.location = el.get('href');
});

$Event('.hkSbPay click', function(e, el) {
	
	e.stop();
	el.form.ajaxRequest().send();
	
	//.set('send', {evalScripts:true, spinnerTarget:el.form});
	//el.form.send();
	
});


/**
 * Payment Selector
 * 
 */
function togglePayments(el)
{
	if ( !document.id('an-sb-payment-express').isDisplayed() && 
		 !document.id('an-sb-payment-creditcard').isDisplayed() 			 	
	) {
		if ( el.id == 'an-sb-payment-selector-express' )
			document.id('an-sb-payment-express').toggle();
		else
			document.id('an-sb-payment-creditcard').toggle();
	}
	else {			
		document.id('an-sb-payment-express').toggle();
		document.id('an-sb-payment-creditcard').toggle();
	}	
};

$Event({
	'.an-sb-payment-selection domready' : function(e, el){
		document.id('an-sb-payment-express').hide();
		document.id('an-sb-payment-creditcard').hide();
	},
	'.an-sb-payment-selector domready'  : function(e, el) {
		if ( el.checked )
			togglePayments(el);
	},	
	'input.an-sb-payment-selector change'   : function(e, el) {
		togglePayments(el);
	}	
});