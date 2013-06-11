/*
---

name: Behavior.Popup

description: Creates a bootstrap popup based on HTML markup.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - Behavior/Behavior
 - More/Object.Extras
 - Bootstrap.Popup

provides: [Behavior.BS.Popup]

...
*/

Behavior.addGlobalFilters({
	'BS.Popup': {
		defaults: {
			hide: false,
			animate: true,
			closeOnEsc: true,
			closeOnClickOut: true,
			mask: true,
			persist: true
		},
		returns: Bootstrap.Popup,
		setup: function(el, api){
			var popup = new Bootstrap.Popup(el,
				Object.cleanValues(
					api.getAs({
						persist: Boolean,
						animate: Boolean,
						closeOnEsc: Boolean,
						closeOnClickOut: Boolean,
						mask: Boolean
					})
				)
			);
			popup.addEvent('destroy', function(){
				api.cleanup(el);
			});
			if (!el.hasClass('hide') && !api.getAs(Boolean, 'hide') && (!el.hasClass('in') && !el.hasClass('fade'))) {
				popup.show();
			}
			return popup;
		}
	}
});