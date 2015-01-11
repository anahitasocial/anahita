/*
---

name: Bootstrap.Dropdown

description: A simple dropdown menu that works with the Twitter Bootstrap css framework.

license: MIT-style license.

authors: [Aaron Newton]

requires:
 - /Bootstrap
 - Core/Element.Event
 - More/Element.Shortcuts

provides: Bootstrap.Dropdown

...
*/
Bootstrap.Dropdown = new Class({

	Implements: [Options, Events],

	options: {
		/*
			onShow: function(element){},
			onHide: function(elements){},
		*/
		ignore: 'input, select, label'
	},

	initialize: function(container, options){
		this.element = document.id(container);
		this.setOptions(options);
		this.boundHandle = this._handle.bind(this);
		document.id(document.body).addEvent('click', this.boundHandle);
	},

	hideAll: function(){
		var els = this.element.getElements('.open').removeClass('open');
		this.fireEvent('hide', els);
		return this;
	},

	show: function(subMenu){
		this.hideAll();
		this.fireEvent('show', subMenu);
		subMenu.addClass('open');
		return this;
	},

	destroy: function(){
		this.hideAll();
		document.body.removeEvent('click', this.boundHandle);
		return this;
	},

	// PRIVATE

	_handle: function(e){
		var el = e.target;
		var open = el.getParent('.open');
		if (!el.match(this.options.ignore) || !open) this.hideAll();
		if (this.element.contains(el)) {
			var parent = el.match('.dropdown-toggle') ? el.getParent() : el.getParent('.dropdown-toggle');
			if (parent) {
				e.preventDefault();
				if (!open) this.show(parent);
			}
		}
	}
});