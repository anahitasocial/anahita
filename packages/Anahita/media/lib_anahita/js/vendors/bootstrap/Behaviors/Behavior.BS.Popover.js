/*
---

name: Behavior.BS.Popover

description: Instantiates Bootstrap.Popover based on HTML markup.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - /Bootstrap.Popover
 - Behavior/Behavior
 - More/Object.Extras

provides: [Behavior.BS.Popover]

...
*/
Behavior.addGlobalFilters({
	'BS.Popover': {
		defaults: {
		  onOverflow: false,
			location: 'right', //below, left, right
			animate: true,
			delayIn: 200,
			delayOut: 0,
			offset: 10,
			trigger: 'hover' //focus, manual
		},
		delayUntil: 'mouseover,focus',
		returns: Bootstrap.Popover,
		setup: function(el, api){
			var options = Object.cleanValues(
				api.getAs({
					onOverflow: Boolean,
					location: String,
					animate: Boolean,
					delayIn: Number,
					delayOut: Number,
					html: Boolean,
					offset: Number,
					trigger: String
				})
			);
			options.getContent = Function.from(api.get('content'));
			options.getTitle = Function.from(api.get('title') || el.get('title'));
			var tip = new Bootstrap.Popover(el, options);
			if (api.event) tip._enter();
			api.onCleanup(tip.destroy.bind(tip));
			return tip;
		}
	}
});