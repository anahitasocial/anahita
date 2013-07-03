/*
---

name: Behavior.BS.Popup.FormRequest

description: Integrates FormRequest behavior into Popups.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - /Behavior.BS.Popup
 - More/Form.Request

provides: [Behavior.BS.Popup.FormRequest]

...
*/

Behavior.addGlobalPlugin("FormRequest", "Popup.FormRequest", {
	defaults: {
		closeOnSuccess: true
	},
	setup: function(element, api, instance){
		if (element.getParent('.modal')){
			var dismissed;
			var dismissals = element.getElements('input.dismiss, input.close').map(function(el){
				return el.addEvent('click', function(){
					dismissed = true;
				}).removeClass('dismiss').removeClass('close');
			});
			instance.addEvents({
				success: function(){
					var formRequestAPI = new BehaviorAPI(element, 'formrequest');
					if (formRequestAPI.getAs(Boolean, 'closeOnSuccess') !== false || api.get(Boolean, 'closeOnSuccess') !== false || dismissed){
						element.getParent('.modal').getBehaviorResult('BS.Popup').hide();
					}
				}
			});
		}
	}
});