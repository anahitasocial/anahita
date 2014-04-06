Delegator.register('click', {
	
	'Invite' : function(event, el, api) 
	{
		event.stop();		
		var form   = document.id('email-invites');
		var emails = [];
		var inputs = [];
		document.getElements('.email').each(function(input, index) {
			if(input.value.length > 3 && 
					form.get('validator').validateField(input)) {
				input.setProperty('disabled', true);
				emails.push(input.value);				
			}
		});
		var req = new Request.HTML({
			method: 'post',
			url: form.action.toURI().setData('layout','emails_sent').toString(),
			data: {email:emails},
			onSuccess: function()
			{
				"Invitations Sent".alert('success');
			},
			onComplete: function(text, tree, html) {
				window.behavior.apply(new Element('span').set('html', html));				
			}
		}).send();		
	}
	
});