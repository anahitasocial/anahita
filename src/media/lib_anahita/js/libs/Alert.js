String.implement({
	alert  : function(type) {
		var div = new Element('div',{html:this});
		div.set('data-alert-type', type);
		window.behavior.applyFilter(div, Behavior.getFilter('Alert'));
	}
});
Behavior.addGlobalFilter('Alert', {
	defaults : {
		mode 		: 'bottom',
		position	: 'right',
		highlight   : false,
		hide 		: true,
		alert		: {
			
		}
	},
	returns	: Purr,
	setup 	: function(el, api) 
	{
		el.dispose();
		var options = api._getOptions();
		if ( api.getAs(Boolean, 'hide') === false) {			
			options.alert['hideAfter'] = false;
		}
		if ( this._purr )  {
			this._purr.wrapper.destroy();
		}
		this._purr = new Purr(options);
		var wrapper = new Element('div',{'class':'alert alert-'+api.get('type')}).set('html', el.get('html'));		
		this._purr.alert(wrapper, api._getOptions() || {});
		return this._purr;
	}
});
