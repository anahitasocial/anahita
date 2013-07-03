Delegator.register('click', {
	
	'Invite' : function(event, el, api) {
		event.stop();
		
		var form = document.id('email-invites');
		
		document.getElements('.email').each(function(input, index){
			
			if(input.value.length > 3 && form.get('validator').validateField(input))
			{
				var req = new Request.HTML({
					method: 'post',
					url: form.action,
					data: 'email=' + input.value,
					onComplete: function(){
						input.setProperty('disabled', true);
					}
				}).send();
			}
		});
	}
	
});