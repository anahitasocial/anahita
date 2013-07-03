/*
---

name: Delegator.BS.ShowPopup

description: Shows a hidden popup.

authors: [Aaron Newton]

license: MIT-style license.

requires:
 - Behavior/Delegator
 - Behavior/Behavior

provides: [Delegator.BS.ShowPopup]

...
*/

(function(){



	Delegator.register('click', 'BS.showPopup', {

		require: ['target'],
		handler: function(event, link, api){
			var target = link.getElement(api.get('target'));
			event.preventDefault();
			if (!target) api.fail('Could not find target element to activate: ', api.get('target'));
			//api.getBehavior().apply(target);
			target.getBehaviorResult('BS.Popup').show();
		}

	});

})();