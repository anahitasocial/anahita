Delegator.register(['click'],'Comment', {
	handler  : function(event, el, api) {
		event.stop();
		var textarea = el.form.getElement('textarea');
		if ( textarea.setContentFromEditor )
			textarea.setContentFromEditor();
		if ( Form.Validator.getValidator('required').test(el.form.getElement('textarea')) )
			window.delegator.trigger('Request',el,'click');
	}
});