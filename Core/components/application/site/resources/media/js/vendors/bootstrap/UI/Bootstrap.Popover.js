/*
---

name: Bootstrap.Popover

description: A simple tooltip (yet larger than Bootstrap.Tooltip) implementation that works with the Twitter Bootstrap css framework.

authors: [Aaron Newton]

license: MIT-style license.

requires:
 - /Bootstrap.Tooltip

provides: Bootstrap.Popover

...
*/

Bootstrap.Popover = new Class({

	Extends: Bootstrap.Tooltip,

	options: {
		location: 'right',
		offset: 10,
		getTitle: function(el){
			return el.get(this.options.title);
		},
		content: 'data-content',
		getContent: function(el){
			return el.get(this.options.content);
		}
	},

	_makeTip: function(){
		if (!this.tip){
			this.tip = new Element('div.popover').addClass(this.options.location)
				 .adopt(new Element('div.arrow'))
				 .adopt(
				   new Element('div.popover-inner').adopt(
				     new Element('h3.popover-title', { html: this.options.getTitle.apply(this, [this.element]) || this.options.fallback })
				   ).adopt(
				     new Element('div.popover-content').adopt(
				       new Element('p', { html: this.options.getContent.apply(this, [this.element])})
				     )
				   )
				 );
			if (this.options.animate) this.tip.addClass('fade');
			if (Browser.Features.cssTransition && this.tip.addEventListener){
				this.tip.addEventListener(Browser.Features.transitionEnd, this.bound.complete);
			}
			this.element.set('alt', '').set('title', '');
		}
		return this.tip;
	}

});