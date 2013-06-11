
var MessageHandler = {};

MessageHandler.Alert = new Class({

	handle : function(message) {
		if ( message.text && message.type ) {
				message.text.alert(message.type);	
		}
	}
});

MessageHandler.Element = new Class({
	initialize : function(container) {
		this.container = container;
	},
	handle : function(message) {
		if ( !message.text || !message.type) {
			return
		}
		if ( document.id(this.container) ) {
			msg = new Element('div',{'class':'alert alert-'+message.type}).set('html', message.text);
			document.id(this.container).empty().adopt(msg);
		} else {
			new MessageHandler.Alert(message); 
		}		
	}
});

/**
 * Handling displaying ajax message notifications
 */
Class.refactor(Request.HTML, 
{	
	options  : {
		message : {
			handler :new MessageHandler.Element('flash-message') 
		}
	},
	//check the header
	onSuccess: function() 
	{
		var message = this.xhr.getResponseHeader('X-Message');
		message  = JSON.decode(message || '{}');
		this.options.message.handler.handle(message);
		return this.previous.apply(this, arguments);
	},
	onFailure: function() 
	{
		var message = this.xhr.getResponseHeader('X-Message');
		message  = JSON.decode(message || '{}');
		this.options.message.handler.handle(message);	
		return this.previous.apply(this, arguments);
	}
});

