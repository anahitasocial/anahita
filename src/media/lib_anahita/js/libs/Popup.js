(function(){
	Class.refactor(Bootstrap.Popup, {
		options : {
			animate: false			
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
					this.element.addClass('hide');
					this._mask.dispose();
				}
			}
		},
	});
	var parse = function(section, html, sections) {
		var sectionReg    = new RegExp('<popup:'+section+'>([\\s\\S]*?)<\/popup:'+section+'>');
		var matches       = sectionReg.exec(html);
		sections[section] = matches ? matches[1] : null;
		return html.replace(sectionReg, '');
	}
	Bootstrap.Popup.implement({
		setContent : function(sections) 
		{
			if ( typeOf(sections) == 'string' ) 
			{
				var html = sections;
				sections = {};
				//check if the html has popup tag
				html = parse('header', html, sections);
				html = parse('footer', html, sections);
				if ( !html.match(/<popup:/)) {
					html = '<popup:body>' + html + '</popup:body>';
				}
				html = parse('body', html, sections);
			}	
			//get the content from a remote URL
			if ( sections.url ) 
			{
				if ( this.url == sections.url ) {
					this.show();
					return;
				} 
				if ( !this.visible ) {
					this.setContent({
						body   : '<div class="uiActivityIndicator">&nbsp;</div>'
					});					
				}
				this.url = sections.url;					
				this.show();
				var req = new Request.HTML({
					url : this.url,
					onSuccess : function(nodes, tree, html) {
						this.setContent(html);					
					}.bind(this)
				}).get();									
			}
			else 
			{
				Object.add(sections, {header:'',footer:'',body:''});
				['header','footer','body'].each(function(section){
					var element = this.element.getElement('.modal-' + section);
					var content = sections[section];
					if ( content ) 
					{
						element.show();
						element.set('html', content);
						//if header doesn't have a close
						//then add it
						if ( section == 'header' && !element.getElement('.close') ) 
						{
							var close = new Element('button',{'class':'close','aria-hidden':true,'html':'&times;'});
							close.inject(element, 'top');
						}
												
					} else {
						element.hide();
					}					
					
				}.bind(this));
				var buttons = (sections.buttons || []).map(function(button) {
					Object.set(button, {
						click 	: Function.from(),
						type	: ''
					});
					var btn  = new Element('button', {
						html	: button.name, 
						'class' : 'btn'
					});
					btn.addClass(button.type);			
					btn.addEvent('click', button.click.bind(this));
					if ( button.dismiss ) {
						btn.addClass('dismiss stopEvent');
					}			
					return btn;
				});
				if ( buttons.length ) {
					this.element.getElement('.modal-footer').adopt(buttons);
					this.element.getElement('.modal-footer').show();
				}				
			}			
		}
	});
	Bootstrap.Popup.from = function(data) {
		data = data || {};
		html = '<div class="modal-header"></div>' + 
			   '<div class="modal-body"></div>'+
			   '<div class="modal-footer"></div>';
		element = new Element('div', {'html':html,'class':'modal fade'});
		element.inject(document.body, 'bottom');
		var modal = new Bootstrap.Popup(element, data.options || {});
		modal.setContent(data);
		return modal; 
	};
})();

(function() {
	var popup;	
	Delegator.register('click', 'BS.showPopup', {
		handler: function(event, link, api) {
			var target, url;
			url = link.get('href');
			event.preventDefault();
			if ( api.get('target') ) {
				target = link.getElement(api.get('target'));
			} 
			if ( api.get('url') ) {			
				url	   = api.get('url');
			}
			if ( !url && !target ) {
				api.fail('Need either a url to the content or can\'t find the target element');
			}
						
			if ( target )								
				target.getBehaviorResult('BS.Popup').show();
			else 
			{
				if ( popup && 
						popup.url == url
						
				) {
					popup.show();
					return;
				}				
				if ( !popup ) {
					popup =  Bootstrap.Popup.from(); 
				}								
				popup.setContent({url:url});
				return;
				popup.url = url;					
				popup.show();
				var req = new Request.HTML({
					url : url,
					onSuccess : function(nodes, tree, html) {
						popup.setContent(html);					
					}
				}).get();
			}
		}

	}, true);

})();