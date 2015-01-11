/*
---

name: Behavior.BS.FormValidator

description: Integrates FormValidator behavior into Bootstrap.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - More-Behaviors/Behavior.FormValidator

provides: [Behavior.BS.FormValidator]

...
*/

Behavior.addGlobalPlugin("FormValidator", "BS.FormValidator", {
	setup: function(element, api, instance){
		var original = {
            showError: instance.options.showError,
            hideError: instance.options.hideError
		};
		instance.setOptions({
			showError: function(){},
			hideError: function(){}
		});
		instance.warningPrefix = '';
		instance.errorPrefix = '';
		instance.addEvents({
			showAdvice: function(field, advice, className){
				var inputParent = field.getParent('.controls'),
				    clearfixParent = inputParent.getParent('.control-group');
				if (!inputParent || !clearfixParent){
					original.showError(advice);
				} else {
					field.addClass('error');
					var help = inputParent.getElement('div.advice');
					if (!help){
						inputParent.getElements('span.help-inline').setStyle('display', 'none');
						help = new Element('span.help-inline.advice.auto-created', {
							html: advice.get('html')
						}).inject(inputParent);
					}
					help.removeClass('hide');
					help.set('title', advice.get('html'));
					clearfixParent.addClass('error');
				}
			},
			hideAdvice: function(field, advice, className){
				var inputParent = field.getParent('.controls'),
				    clearfixParent = inputParent.getParent('.control-group');
				if (!inputParent || !clearfixParent){
					original.hideError(advice);
				} else {
					field.removeClass('error');
					var help = inputParent.getElement('span.advice');
					if (help.hasClass('auto-created')) help.destroy();
					else help.set('html', '');
					inputParent.getElements('span.help-inline').setStyle('display', '');
					clearfixParent.removeClass('error');
				}
			}
		});
	}
});