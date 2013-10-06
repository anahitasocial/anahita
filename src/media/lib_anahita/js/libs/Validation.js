
/**
 * Custom Form Validators
 */

Class.refactor(InputValidator, {
	getSuccess: function(field, props) {
		var msg = this.options.successMsg;
		if ($type(msg) == 'function') msg = msg(document.id(field), props||this.getProps(field));
		return msg;
	}
});

Class.refactor(Form.Validator, {
	options : {
		warningPrefix : '',
		errorPrefix	  : ''
	}
});

Class.refactor(Form.Validator.Inline, {
	
	initialize: function(form, options) 
	{
		this.parent(form, options);
		this.addEvent('onElementValidate', function(isValid, field, className, warn){
			var validator = this.getValidator(className);
			if (!isValid && validator.getError(field)) 
			{
				if (warn) field.addClass('warning');
				var error  = validator.getError(field);
				var advice = this.makeAdvice(className, field, error, warn);
				advice.set('html', error);				
				var cssClass = (warn) ? 'warning-advice' : 'validation-advice';
				advice.set('class', cssClass);
				this.insertAdvice(advice, field);
				if ( advice.getParent('.control-group') )
				    advice.getParent('.control-group').removeClass('success').addClass('error');
				this.showAdvice(className, field);
			} else if ( isValid && validator.getSuccess(field)) {
			    var succes = validator.getSuccess(field);
				var advice = this.makeAdvice(className, field, succes);
				advice.set('html', succes);
				advice.set('class', 'success-advice');				
				this.insertAdvice(advice, field);
				if ( advice.getParent('.control-group') )
				    advice.getParent('.control-group').removeClass('error').addClass('success');
				this.showAdvice(className, field);
			} else {
				this.hideAdvice(className, field);
			}
		});
	}
});

/**
 * Form Remote Validator 
 */
(function() {
	
	Class.refactor(Form.Validator, {
		validate: function(event) 
		{
			var result = this.previous(event);
			var remoteValidators = this.element.get('remoteValidators');			
			//if all validations passed and there are remote validators
			//pending
			if ( result 
					&& remoteValidators.isPending()
					&& event ) 
			{
				event.preventDefault();
				if ( !this.validationSuccess ) 
				{
					this.validationSuccess  = function() {
						this.element.submit();
					}.bind(this);
					this.validationComplete = function() {
						this.element.removeEvent('validationSuccessful', this.validationSuccess);
					}.bind(this);
				}
				this.validationComplete();				
				this.element.addEvent('validationSuccessful', this.validationSuccess);
				this.element.addEvent('validationComplete', this.validationComplete);
				return result;
			}
			
			result = result && remoteValidators.isSuccess();
			
			if ( result ) {
				this.element.fireEvent('validationSuccessful');
			}
			
			return result;
		}
	});
	Element.Properties.remoteValidators = {
		get  : function() {
			if ( !this.retrieve('remoteValidators') ) {
				var validators = new Array();
				var element    = this;
				Object.merge(validators, {
					isSuccess   : function() {
						return this.length == 0 || this.every(function(validator){
							return validator.isSuccess();
						});
					},
					validate  : function() {
						this.each(function(validator){
							validator.validate();
						});
					},
					isPending : function() {						
						return this.some(function(validator){
							return validator.isPending();
						});
					},
					add : function(validator) {
						validators.include(validator);						
						validator.addEvent('onValidationComplete', function() {
							//validation is complete
							//lets call the form validator in order to 
							//get it to show the messsage
							element.get('validator').validateField(validator.element);
							if ( !validators.isPending() ) {
								//no more pending								
								if ( validators.isSuccess() ) {
									element.fireEvent('validationSuccessful');
								}
								element.fireEvent('validationComplete');
							}
						});
					}
				});
				this.store('remoteValidators', validators);
			}
			return this.retrieve('remoteValidators');
		}
	};

	Element.Properties.remoteValidator = {
		get  : function() {
			if ( !this.retrieve('remoteValidator') ) {
				this.set('remoteValidator', {});
			}
			return this.retrieve('remoteValidator');
		},
		set : function(props) {
			if ( ! this.retrieve('remoteValidator') ) {
				this.store('remoteValidator', new RemoteValidator(this, props));
				this.form.get('remoteValidators').add(this.retrieve('remoteValidator'));				
			} else {
				this.retrieve('remoteValidator').props = props;
			}
			return this;
		}
	};
	var RemoteValidatorResponse = new Class({
		initialize : function(value, type, msg) {
			this.value = value;
			this.type  = type;
			this.msg   = msg;
		}
	});
	var RemoteValidator = new Class({
		Implements : [Events],
		initialize : function(element, props) {
			this.element = element;
			this.props 	 = props || {};
			this.status  = null;
			this.result  = {};
			this.responses	 = {};
		},
		isPending : function() {
			return this.status == 'pending';
		},
		isSuccess : function() {
			return this.status == 'success';
		},
		isFailed  : function() {
			return this.status == 'error';
		},
		validate  : function() {
			if ( Form.Validator.getValidator('IsEmpty').test(this.element) ) {
				this.status = 'success';
				return true;
			}
			
			var self    = this;
			var requestUrl  = this.props.url || this.element.form.get('action');
			var value   = this.element.get('value');
			var hash    = value + ' @ ' + requestUrl + ' @ ' + JSON.stringify(this.props);
			
			if ( this.responses[hash] ) {
				this.result = this.responses[hash];
				this.status = this.result.status;
				return;
			}
			
			this.status  = 'pending';
			this.request = new Request({
				url    : requestUrl,
				method : 'post',
				data   : {action:'validate','key':this.props.key || this.element.get('name'),'value':value},
				onFailure  : function() {
					self.status = 'error';
					self.result = JSON.decode(this.getHeader('Validation') || '{}') || {};
					Object.add(self.result,{
						errorMsg : self.props.errorMsg
					});
					self.result.status	  = self.status;
					self.responses[hash]  = self.result;
					self.fireEvent('validationComplete');
				},
				onSuccess : function() {
					self.status = 'success';
					self.result = JSON.decode(this.getHeader('Validation') || '{}') || {};
					Object.add(self.result,{
						successMsg : self.props.successMsg
					});					
					self.result.status	   = self.status;
					self.responses[hash]   = self.result;
					self.fireEvent('validationComplete');
				},
				async : true
			});
			this.request.send();
		},
		getErrorMsg   : function() {
			return this.result.errorMsg;
		},
		getSuccessMsg : function() {
			return this.result.successMsg;
		}
	});
	Form.Validator.add('validate-passwod', {
		errorMsg : function(element, props) {
			return 'Passwords will contain at least (1) upper case letter <br/>' +  
			'Passwords will contain at least (1) lower case letter<br/>' +
			'Passwords will contain at least (1) number or special character<br/>' + 
			'Passwords will contain at least (8) characters in length<br/>' +
			'Password maximum length should not be arbitrarily limited ';
		},
		test 	: function(element, props) {
			var value = element.get('value');
			return true;
			return value.match(/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/);
		}
	});
	Form.Validator.add('validate-username', {
		errorMsg : function(element, props) {
			return 'Username can only contain letters and numbers'.translate();
		},
		test 	: function(element, props) {
			return element.value.match(/^[A-Za-z0-9][A-Za-z0-9_-]*$/);			
		}
	});	
	Form.Validator.add('validate-remote', {
		successMsg : function(element, props) {
			var remoteValidator = element
			.set('remoteValidator', props)
			.get('remoteValidator');
			
			return remoteValidator.getSuccessMsg();
		},	
		errorMsg: function(element, props) {
			var remoteValidator = element
				.set('remoteValidator', props)
				.get('remoteValidator');
			
			return remoteValidator.getErrorMsg();
		},
		test 	: function(element, props) {
			var remoteValidator = element
				.set('remoteValidator', props)
				.get('remoteValidator');
			
			remoteValidator.validate();
			if ( !remoteValidator.isPending() ) {
				return !remoteValidator.isFailed();
			} else {
				return true;
			}
		}
	});	
})();
