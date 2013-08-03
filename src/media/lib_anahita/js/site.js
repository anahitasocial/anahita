//@depends vendors/mootools-core.js
//@depends vendors/mootools-more.js
//@depends vendors/clientcide.js
//@depends vendors/bootstrap/bootstrap.js
//@depends vendors/Scrollable.js
//@depends vendors/purr.js
//@depends anahita.js
//@depends libs/Popup.js
//@depends libs/Popover.js
//@depends libs/Alert.js
//@depends libs/Submit.js
//@depends libs/Request.Message.js
//@depends libs/Comment.js
//@depends libs/ElementInsert.js
//@depends libs/Paginator.js
//@depends libs/InfinitScroll.js
//@depends libs/MasonryLayout.js
//@depends libs/ScrollLoader.js

/**
 * String Alert using Purr
 */
String.implement({
	prompt : function(options) {
		var options = {					
				body    : '<h3>' + this.translate() + '</h3>',
				buttons : [
				   {name: 'Action.cancel'.translate(), dismiss:true},
				   {name: 'Action.yes'.translate(), dismiss:true, click:options.onConfirm, type: 'btn-danger'}
				]
		};
		return new Bootstrap.Popup.from(options).show();	
	}
});

/**
 * Editable Behavior
 */
Behavior.addGlobalFilter('Editable',{
	defaults : {
		prompt 		: 'Prompt.inlineEdit'.translate(),
		inputType	: 'textfield'
	},
	setup : function(el, api)
	{
		var prompt 	       = api.getAs(String, 'prompt'),
			inputType      = api.getAs(String, 'inputType'),
			url	   	       = api.getAs(String, 'url'),
			inputName      = api.getAs(String, 'name'),
			dataValidators = api.getAs(String, 'dataValidators')
			;
			
		el.store('prompt', '<span class="an-ui-inline-form-prompt">'+ prompt +'</span>');
		
		if ( !el.get('text').test(/\S/) ) {
			el.set('html', el.retrieve('prompt'));
		}
		
		el.addEvent('click', function(el, inputType, url,inputName) 
		{
			var prompt = el.retrieve('prompt');
			if ( el.retrieve('state:edit') ) {
				return;
			}
			el.store('state:edit', true);
			el.hide();
			var form 	   = new Element('form', {method:'post', 'action':url,'class':'inline-edit', 'data-behavior':'FormValidator'});			
			var cancelBtn  = new Element('button', {text:'Action.cancel'.translate(),'class':'btn'});
			var saveBtn    = new Element('button', {text:'Action.save'.translate(),  'class':'btn btn-primary'});
			var value	   = el.getElement('span') ? '' : el.get('text');
			
			
			if ( inputType == 'textarea' )
				var inputText = new Element('textarea', {'cols':'5', 'rows':'5'});
			else
				var inputText  = new Element('input', {type:'text'});
			
			inputText.set({name:inputName, value:value.trim(), 'class':'input-block-level'});
			
			if(dataValidators)
				inputText.set({'data-validators':dataValidators});
			
			form.show();
			form.adopt(new Element('div', {'class':'control-group'}).adopt(new Element('div', {'class':'controls'}).adopt(inputText)));
			form.adopt(new Element('div', {'class':'form-actions'}).adopt(cancelBtn).appendText(' ').adopt(saveBtn));
			
			cancelBtn.addEvent('click', function(e){
				e.stop();
				el.store('state:edit', false);
				el.show();
				form.destroy();
			});
			
			saveBtn.addEvent('click', function(e){
				e.stop();
				el.store('state:edit', false);
				
				if(!form.get('validator').validate())
					return;
				
				form.ajaxRequest({
					onSuccess : function() {
						el.set('html', inputText.get('value') || prompt);
						el.show();
						form.hide();					
					}
				}).send();
			});
			
			el.getParent().adopt(form);
		}.bind(null,[el,inputType, url,inputName]));
	}
});

/**
 * Embeding Video
 */
Behavior.addGlobalFilter('EmbeddedVideo', {
	setup : function(el, api) 
	{
		var img = Asset.image(el.getElement('img').src, {
			onLoad: function (img)
			{
				var width = Math.min(img.width, el.getSize().x);
				var height = Math.min(img.height, el.getSize().y);

				var styles = {'width':width, 'height':height};
				var span = new Element('span');
				span.setStyles(styles);
				span.inject(el, 'top');
				
	    		window.addEvent('resize', function(){
	    			el.getElement('span').setStyle('width', Math.min(img.width, el.getSize().x));
    				el.getElement('span').setStyle('height', Math.min(img.height, el.getSize().y));
	    		}.bind(this));
				
				el.addEvent('click:once', function(){
					
					var options = api._getOptions();					

					if ( Browser.Engine.trident )
						options.wMode   = '';
					
					var object = new Swiff(options['url']+'&autoplay=1', {
						width: width,
						height: height,
						params : options
					});
					
					img.set('tween',{
						duration 	: 'short',
						onComplete	: function() {
							el.empty().adopt(object);
						}
					});
					img.fade(0.7);
				});
			}
		});
	}		
});

/**
 * Delegates
 */
Delegator.register('click', {
	'ViewSource' : function(event, el, api) {
		event.stop();
		var element = api.getAs(String, 'element');		
		element = el.getElement(element);
		yWindow = window.open('','','resizable=no,scrollbars=yes,width=800,height=500');
		var codes = [];
		element.getElements('pre').each(function(line){
			codes.push(line.get('text').escapeHTML());
		});
		yWindow.document.body.innerHTML = '<pre>' + codes.join("\n") + '</pre>';		
	},
	'Remove' : function(event, handle, api) {
		event.stop();		
		var options = {
			'confirmMsg'	  : api.get('confirm-message') || 'Prompt.confirmDelete'.translate(),
			'confirm'		  : true,
			'parent'          : api.get('parent') || '!.an-removable',
			'form'			  : api.get('form')
		};
		var parent  = handle.getElement(options.parent);		
		var submit  = function(options) 
		{
			if ( !options.form )
				var data    = handle.get('href').toURI().getData();
				var url 	= handle.get('href');
			
			if ( parent ) 
			{
				parent.ajaxRequest({url:url, data:data,onSuccess:function(){parent.destroy()}}).post();
			} 
			else 
			{
				var form = (options.form || 
					Element.Form({
						method  : 'post',
						url 	: url,
						data	: data
					}));
				if ( instanceOf(options.form, String) )
				{
					form = handle.getElement(options.form);
				}
				form.submit();
			}
			if ( handle.retrieve('modal') ) {
				handle.retrieve('modal').destroy();
			}
		}.pass(options);
		
		if ( options.confirm )
		{
			options = {
					body    : '<h3>' + options.confirmMsg + '</h3>',
					buttons : [
					   {name: 'Action.cancel'.translate(), dismiss:true},
					   {name: 'Action.delete'.translate(), dismiss:true, click:function(){submit()}, type: 'btn-danger'}					   
					]
			};
			if ( !handle.retrieve('modal') ) {
				handle.store('modal', Bootstrap.Popup.from(options));
			}
			
			handle.retrieve('modal').show();								
		}
		else submit();		
	},
	'VoteLink' : function(event, el, api) {
		event.stop();
		el.ajaxRequest({
			method    : 'post',
			onSuccess : function() {
				el.getParent().hide();
				document.id(api.get('toggle')).getParent().show();
				var box = document.id('vote-count-wrapper-' + api.get('object')) ||
				          el.getElement('!.an-actions ~ .story-comments  .vote-count-wrapper ')
				if ( box ) 
				{
					box.set('html', this.response.html);
					if ( this.response.html.match(/an-hide/) )
						box.hide();
					else
						box.show();
				}
			}
		}).send();		
	}
});

Request.Options = {};

Behavior.addGlobalFilter('Pagination', {
	defaults: {
		'container' : '!.an-entities'
	},
	
	setup : function(el, api) {
		var container = el.getElement(api.get('container'));
		var links = el.getElements('a');
		links.addEvent('click', function(e){
			e.stop();
			if ( this.getParent().hasClass('active') || this.getParent().hasClass('disabled') )
				return;
			var uri   	= this.get('href').toURI();
			var current	= new URI(document.location).getData();				
			//only add the queries to hash that are different 
			//from the current
			var hash = {};
			Object.each(uri.getData(), function(value, key) {
				//if not value skip
				if ( !value )
					return;				
				//if the value is either option,layout,view skip
				if ( ['layout','option','view'].contains(key) ) {
					return;
				}
				//no duplicate value
				if ( current[key] != value ) {
					hash[key] = value;
				}
 			});
			
			document.location.hash = Object.toQueryString(hash);
			
			this.ajaxRequest({			
				method 	  :  'get',
				onSuccess : function() {
					var html = this.response.html.parseHTML();
					
					html.getElements('.pagination').replaces(document.getElements('.pagination'));
					html.getElement('.an-entities').replaces(document.getElement('.an-entities'));
					var scrollTop = new Fx.Scroll(window).toTop();
				}
			}).send();
		})
	}
});


window.addEvent('domready',
(function(){
	var uri = document.location.toString().toURI();
	if ( uri.getData('start', 'fragment') ) {
		uri.setData(uri.getData(null, 'fragment'), true);
		uri.set('fragment','');
		uri.go();
	}
	else if ( uri.getData('permalink', 'fragment') ) {
		uri.setData({permalink:uri.getData('permalink', 'fragment')}, true);
		uri.set('fragment','');
		uri.go();
	} else if ( uri.getData('scroll', 'fragment') ) {
		window.addEvent('domready', function() {
			var selector = uri.getData('scroll', 'fragment');
			var element  = document.getElement('[scroll-handle="'+selector+'"]') || document.getElement(selector);
			if ( element )
				new Fx.Scroll(window).toElement(element).chain(element.highlight.bind(element));
		});
	}	
}));

Behavior.addGlobalFilter('PlaceHolder', {
    defaults : {
        element  : '.placeholder'
    },
    setup : function(element, api) 
    {
        var placeholder = element.getElement(api.getAs(String, 'element'));        
        element.store('placeholder:element', placeholder);
        Object.append(element,  {
            setContent      : function(content) 
            {
                element.store('placeholder:content', content);
                element.adopt(content);
                element.showContent();                
            },
            toggleContent   : function(event) 
            {
                event = event || 'click';
                element.addEvent(event,  function(e) {
                    e.eventHandled = true;                    
                    element.showContent();
                });
                var area = element.getElement(api.getAs(String,'area')) || element;
                area.onOutside(event, function(e){
                    if ( !e.eventHandled )
                        element.hideContent();
                });
            },
            showContent     : function() 
            {
                var content = element.retrieve('placeholder:content'), 
                placeholder = element.retrieve('placeholder:element'); 
                placeholder.hide();
                content.fade('show').show();
            },
            hideContent : function() 
            {
                var content = element.retrieve('placeholder:content'), 
                placeholder = element.retrieve('placeholder:element');
                content.get('tween').chain(function(){
                    content.hide();
                    placeholder.show();
                });
                content.fade('out');                
            }
        });
    }
});

/**
 * Fixes Bootrap Drop down
 */

Class.refactor(Bootstrap.Dropdown, {
			
    _handle: function(e){
        var el = e.target;
        var open = el.getParent('.open');
        if (!el.match(this.options.ignore) || !open) this.hideAll();
        if (this.element.contains(el)) {
            var parent = el.match('.dropdown-toggle') ? el.getParent() : el.getParent('.dropdown-toggle');
            if (parent) {
                e.preventDefault();
                if (!open) this.show(el.getParent('.dropdown,.btn-group') || parent);
            }
        }
    }
});

Delegator.register(['click'],'Checkbox', {
	defaults : {
		'toggle-element' : null,
		'toggle-class'	 : 'selected'
	},
	handler  : function(event, el, api) 
	{		
		var target = el;
		if ( api.get('toggle-element') ) {
			target = el.getElement(api.get('toggle-element'));
		}				
		if ( !el.retrieve('checkbox') ) 
		{			
			var checkbox = new Element('input',{
				type   : 'checkbox',
				value  : api.getAs(String,'value'),
				name   : api.getAs(String,'name')
			});			
			el.adopt(checkbox);
			checkbox.hide();
			if ( checkbox.form ) {
				checkbox.form.addEvent('reset', function(){
					target.removeClass(api.get('toggle-class'));
				});
			}
			el.store('checkbox', checkbox);
		}

		var checkbox 	   = el.retrieve('checkbox');
		checkbox.checked   = !checkbox.checked;
		target.toggleClass(api.get('toggle-class'));
		el.fireEvent('check');
	}
});

var EditEntityOptions = function() {
	return {
		replace : this.getParent('form'),
		url		: function() {
			var url = this.form.get('action').toURI().setData({layout:'list'}).toString();
			return url;
		}
	}
}


var EntityHelper = new Class({
	
	initialize: function(){
		this.form = document.id('entity-form');
	},
	
	resetForm : function(){
		this.form.title.value = '';
		this.form.description.value = '';
	},
	
	add : function(){
		
		if(this.form.title.value.clean().length < 3)
			return false;
		
		var url = this.form.get('action').toURI().setData({layout:'list'}).toString();
		this.form.ajaxRequest({
			method : 'post',
			url  : url,
			data : this.form,
			inject : {
				element : document.getElement('.an-entities'),
				where   : 'top'
			},
			onSuccess : function(form){
				var element = document.getElement('.an-entities').getElement('.an-entity');
				this.resetForm();
			}.bind(this)
		}).send();
	}
});

Behavior.addGlobalFilter('Scrollable',{
	defaults : {
	
	},
	returns : Scrollable,
    setup   : function(el, api)
    {
    	var container = el;
    	if ( api.getAs(String,'container') ) {
    		container = el.getElement(api.getAs(String,'container'));
    	}
		return new Scrollable(container);    
    }
})
