/*
---

name: Behavior.BS.Tooltip

description: Instantiates Bootstrap.Tooltip based on HTML markup.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - /Bootstrap.Tooltip
 - Behavior/Behavior
 - More/Object.Extras

provides: [Behavior.BS.Twipsy, Behavior.BS.Tooltip]

...
*/
(function(){
	var filter = {
		defaults: {
			location: 'above', //below, left, right
			animate: true,
			delayIn: 200,
			delayOut: 0,
			onOverflow: false,
			offset: 0,
			trigger: 'hover' //focus, manual
		},
		delayUntil: 'mouseover,focus',
		returns: Bootstrap.Tooltip,
		setup: function(el, api){
			var options = Object.cleanValues(
				api.getAs({
					onOverflow: Boolean,
					location: String,
					animate: Boolean,
					delayIn: Number,
					delayOut: Number,
					fallback: String,
					override: String,
					html: Boolean,
					offset: Number,
					trigger: String
				})
			);
			options.getTitle = Function.from(api.get('content') || el.get('title'));
			var tip = new Bootstrap.Tooltip(el, options);
			api.onCleanup(tip.destroy.bind(tip));
			if (api.event) tip.show();
			return tip;
		}
	};
	Behavior.addGlobalFilters({
		'BS.Tooltip': filter,
		'BS.Twipsy': filter
	});
})();