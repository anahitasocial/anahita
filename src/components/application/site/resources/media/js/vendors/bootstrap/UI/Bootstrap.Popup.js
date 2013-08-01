/*
---

name: Popup

description: A simple Popup class for the Twitter Bootstrap CSS framework.

authors: [Aaron Newton]

license: MIT-style license.

requires:
 - Core/Element.Delegation
 - Core/Fx.Tween
 - Core/Fx.Transitions
 - More/Mask
 - More/Elements.From
 - More/Element.Position
 - More/Element.Shortcuts
 - More/Events.Pseudos
 - /CSSEvents
 - /Bootstrap

provides: [Bootstrap.Popup]

...
*/

Bootstrap.Popup = new Class({

	Implements: [Options, Events],

	options: {
		/*
			onShow: function(){},
			onHide: function(){},
			animate: function(){},
			destroy: function(){},
		*/
		persist: true,
		closeOnClickOut: true,
		closeOnEsc: true,
		mask: true,
		animate: true
	},

	initialize: function(element, options){
		this.element = document.id(element).store('Bootstrap.Popup', this);
		this.setOptions(options);
		this.bound = {
			hide: this.hide.bind(this),
			bodyClick: function(e){
				if (!this.element.contains(e.target)){
					this.hide();
				}
			}.bind(this),
			keyMonitor: function(e){
				if (e.key == 'esc') this.hide();
			}.bind(this),
			animationEnd: this._animationEnd.bind(this)
		};
		if ((this.element.hasClass('fade') && this.element.hasClass('in')) ||
		    (!this.element.hasClass('hide') && !this.element.hasClass('fade'))){
			if (this.element.hasClass('fade')) this.element.removeClass('in');
			this.show();
		}
	},

	_checkAnimate: function(){
		var check = this.options.animate !== false && Browser.Features.getCSSTransition() && (this.options.animate || this.element.hasClass('fade'));
		if (!check) {
			this.element.removeClass('fade').addClass('hide');
			this._mask.removeClass('fade').addClass('hide');
		} else if (check) {
			this.element.addClass('fade').removeClass('hide');
			this._mask.addClass('fade').removeClass('hide');
		}
		return check;
	},

	show: function(){
		if (this.visible || this.animating) return;
		var hide = this.bound.hide;
		this.element.addEvent('click:relay(.close, .dismiss)', function(e){
			e.stop();
			hide();
		});
		if (this.options.closeOnEsc) document.addEvent('keyup', this.bound.keyMonitor);
		this._makeMask();
		this._mask.inject(document.body);
		this.animating = true;
		if (this._checkAnimate()){
			this.element.offsetWidth; // force reflow
			this.element.addClass('in');
			this._mask.addClass('in');
		} else {
			this.element.show();
			this._mask.show();
		}
		this.visible = true;
		this._watch();
	},

	_watch: function(){
		if (this._checkAnimate()) this.element.addEventListener(Browser.Features.getCSSTransition(), this.bound.animationEnd);
		else this._animationEnd();
	},

	_animationEnd: function(){
		if (Browser.Features.getCSSTransition()) this.element.removeEventListener(Browser.Features.getCSSTransition(), this.bound.animationEnd);
		this.animating = false;
		if (this.visible){
			this.fireEvent('show', this.element);
		} else {
			this.fireEvent('hide', this.element);
			if (!this.options.persist){
				this.destroy();
			} else {
				this._mask.dispose();
			}
		}
	},

	destroy: function(){
		this._mask.destroy();
		this.fireEvent('destroy', this.element);
		this.element.destroy();
		this._mask = null;
		this.destroyed = true;
	},

	hide: function(event, clicked){
		if (!this.visible || this.animating) return;
		this.animating = true;
		if (event && clicked && clicked.hasClass('stopEvent')){
			event.preventDefault();
		}
		document.id(document.body).removeEvent('click', this.bound.hide);
		document.removeEvent('keyup', this.bound.keyMonitor);
		this.element.removeEvent('click:relay(.close, .dismiss)', this.bound.hide);

		if (this._checkAnimate()){
			this.element.removeClass('in');
			this._mask.removeClass('in');
		} else {
			this.element.hide();
			this._mask.hide();
		}
		this.visible = false;
		this._watch();
	},

	// PRIVATE

	_makeMask: function(){
		if (this.options.mask){
			if (!this._mask){
				this._mask = new Element('div.modal-backdrop', {
					events: {
						click: this.bound.hide
					}
				});
				if (this._checkAnimate()){
					this._mask.addClass('fade');
				}
			}
		} else if (this.options.closeOnClickOut){
			document.id(document.body).addEvent('click', this.bound.hide);
		}
	}

});