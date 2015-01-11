Behavior.addGlobalFilter('Element.Inject', {
	defaults: {
		where: 'bottom'
	},
	setup: function (el, api) {		
		var container = document.getElement(api.get('container'));
		if (container) {
			el.inject(container, api.get('where'));
		}
	}
});