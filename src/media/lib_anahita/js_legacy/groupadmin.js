Behavior.addGlobalFilter('FollowerAutoComplete', {
	setup : function(element, api) {
		
		element.form.addEvent('submit', function(e){
			e.stop();
		});
		
		var options = {
			ajaxOptions : {
				method : 'get'
			}
		}
		
		var aaj = new Autocompleter.Ajax.Json(element, element.getData('autocomplete-url'), options);
		
		Object.merge(aaj, {			
			update: function(tokens) {
				this.choices.empty();
				this.cached = tokens;
				if (!tokens || !tokens.length) {
					this.hideChoices();
				} else {
					if (this.options.maxChoices < tokens.length && !this.options.overflow) tokens.length = this.options.maxChoices;
					tokens.each(this.options.injectChoice || function(token){
						var choice = new Element('li', {'html': this.markQueryValue(token.value)});
						choice.inputValue = token.value;
						choice.store('token', token);
						this.addChoiceEvents(choice).inject(this.choices);
					}, this);
					this.showChoices();
				}
			}
		});
		
		aaj.addEvent('choiceConfirm', function(choice) {
			this.element.value = '';
			this.element.form.adminid.value = choice.retrieve('token').id;
			this.element.form.spin().submit();
		});
	}
});