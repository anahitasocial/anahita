/*
---

name: Behavior.BS.Tabs

description: Instantiates Bootstrap.Tabs based on HTML markup.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - Behavior/Behavior
 - Clientcide/Behavior.Tabs

provides: [Behavior.BS.Tabs]

...
*/
(function(){

	var tabs = Object.clone(Behavior.getFilter('Tabs'));

	Behavior.addGlobalFilters({
		'BS.Tabs': tabs.config
	});

	Behavior.setFilterDefaults('BS.Tabs', {
		'tabs-selector': 'a:not(.dropdown-toggle)',
		'sections-selector': '+.tab-content >',
		'selectedClass': 'active',
		smooth: false,
		smoothSize: false
	});

	Behavior.addGlobalPlugin('BS.Tabs', 'BS.Tabs.CSS', function(el, api, instance){
		instance.addEvent('active', function(index, section, tab){
			el.getElements('.active').removeClass('active');
			tab.getParent('li').addClass('active');
			var dropdown = tab.getParent('.dropdown');
			if (dropdown) dropdown.addClass('active');
		});
	});

})();