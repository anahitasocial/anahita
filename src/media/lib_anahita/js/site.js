//@depends vendors/mootools-core.js
//@depends vendors/mootools-more.js
//@depends vendors/clientcide.js
//@depends vendors/bootstrap/bootstrap.js
//@depends vendors/Scrollable.js
//@depends vendors/purr.js
//@depends anahita.js
 
/**
 * Handling displaying ajax message notifications
 */
Class.refactor(Request.HTML, 
{	
	//check the header
	onSuccess: function() {
		var message 	= this.xhr.getResponseHeader('Redirect-Message');
		var messageType = this.xhr.getResponseHeader('Redirect-Message-Type') || 'success';
		if  ( message ) {
			message.alert(messageType);
		}
		return this.previous.apply(this, arguments);
	},
	onFailure: function() {
		var message 	= this.xhr.getResponseHeader('Redirect-Message');
		var messageType = this.xhr.getResponseHeader('Redirect-Message-Type') || 'error';
		if  ( message ) {
			message.alert(messageType);
		}
		return this.previous.apply(this, arguments);
	}
});

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
	},
	alert  : function(type) {
		var div = new Element('div',{html:this});
		div.set('data-alert-type', type);
		window.behavior.applyFilter(div, Behavior.getFilter('Alert'));
	}
});

(function(){
	Class.refactor(Bootstrap.Popup, {	
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
	Bootstrap.Popup.from = function(data) 
	{
		Object.set(data, {buttons:[], header:''});
		var html = '';
		if ( data.header )
			html += '<div class="modal-header">' + 
//						'<a href="#" class="close dismiss stopEvent">x</a>' + 
						'<h3>'+data.header+'</h3>' +
					'</div>';
					
		html +=	'<div class="modal-body"><p>' + data.body  + '</p>' + 
					'</div>' +
					'<div class="modal-footer">' +
					'</div>';			
		element = new Element('div', {'html':html,'class':'modal fade'});
		
		data.buttons = data.buttons.map(function(button) {
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
		 
		element.getElement('.modal-footer').adopt(data.buttons);
		element.inject(document.body, 'bottom');
		
		return new Bootstrap.Popup(element, data.options || {});	
	}
})();

Behavior.addGlobalFilter('Alert', {
	defaults : {
		mode 		: 'bottom',
		position	: 'right',
		highlight   : false,
		hide 		: true,
		alert		: {
			
		}
	},
	returns	: Purr,
	setup 	: function(el, api) 
	{
		el.dispose();
		var options = api._getOptions();
		if ( api.getAs(Boolean, 'hide') === false) {			
			options.alert['hideAfter'] = false;
		}
		if ( !this._purr ) {
			this._purr = new Purr(options);
		}
		var wrapper = new Element('div',{'class':'alert alert-'+api.get('type')}).set('html', el.get('html'));		
		this._purr.alert(wrapper, api._getOptions() || {});
		return this._purr;
	}
});

Class.refactor(Bootstrap.Popover, {
        
   initialize : function(el, options)
   {             
       return this.previous(el, options);       
   },
   _makeTip: function() 
   {
	  if ( !this.tip ) 
	  {
		 this.previous();
		 if ( this.options.tipclass )
			 this.tip.addClass(this.options.tipclass);
   	  }
   	  return this.tip;
   }, 
   _attach: function(method) 
   {
       this.parent(method);
       this.bound.event = this._handleEvent.bind(this);
       method = method || 'addEvents';
       if (this.options.trigger == 'click') 
       {		
       		[document,this.element].invoke(method,{
       			 click: this.bound.event
       		});
       }
       else if (this.options.trigger == 'hover')
       {
           this.options.delayOut = Math.max(50, this.options.delayOut);
           
           if ( this.tip )
           {
               this.tip[method]({
                   mouseover  : this.bound.enter,
                   mouseleave : this.bound.leave
               });               
           }
       }
   },
   _complete: function() 
   {
       if ( this.visible )
       {
           if ( this.options.trigger == 'hover' )
               this.tip['addEvents']({
                   mouseover  : this.bound.enter,
                   mouseleave : this.bound.leave
               }); 
       }
       return this.parent();       
   },
   _handleEvent : function(event)
   {
		var el = event.target;
		var contains = el == this.element || this.element.contains(el) || (this.tip && this.tip.contains(el));
		if ( !contains ) {
           this.bound.leave();
           clearTimeout(this.repositioner);
           this.repositioner = null;
		}
        else {
           this.bound.enter();
           if ( !this.repositioner ) {
           		this.repositioner = (function(){
           			this._reposition();
           		}).periodical(10, this);
           }
		}
   },
   _reposition : function()
   {
   		if ( !this.tip || !this.visible )
   			return;
		var pos, edge, offset = {x: 0, y: 0};
		switch(this.options.location){
			case 'below': case 'bottom':
				pos = 'centerBottom';
				edge = 'centerTop';
				offset.y = this.options.offset;
				break;
			case 'left':
				pos = 'centerLeft';
				edge = 'centerRight';
				offset.x = this.options.offset;
				break;
			case 'right':
				pos = 'centerRight';
				edge = 'centerLeft';
				offset.x = this.options.offset;
				break;
			default: //top
				pos = 'centerTop';
				edge = 'centerBottom';
				offset.y = this.options.offset;
		}
		if (typeOf(this.options.offset) == "object") offset = this.options.offset;
		this.tip.position({			
			relativeTo: this.element,
			position: pos,
			edge: edge,
			offset: offset
		});
   }
   
});

Behavior.addGlobalPlugin('BS.Popover','Popover', {
    setup : function(el, api, instance)
    {
    	instance.options.tipclass = api.getAs(String,'tipclass');    	
    /*
        var getContent   = instance.options.getContent;
        instance.options = Object.merge(instance.options,{
           getContent : function() {
               var content = getContent();
               //check if it's a selector
               if ( element = el.getElement(content) ) {
                   element.dispose();
                   return element.get('html');
               }
               return content;
           }
        });
        */
        if ( instance.options.trigger == 'click')
            instance._leave();
    }

});

Behavior.addGlobalFilter('RemotePopover', {
    defaults : {
        title   : '.popover-title',
        content : '.popover-content',       
        delay   : 0
    },
    setup : function(el, api) 
    {
        el.addEvent('click', function(e){e.stop()});
        var getData = function(popover) 
        {
            var req = new Request.HTML({
                method : 'get',
                async  : true,
                url    : url,
                onSuccess : function() {
					var html    = req.response.text.parseHTML();
            		var title   = html.getElement(api.get('title'));
           			var content = html.getElement(api.get('content'));
            		if ( content )
                		content = content.get('html');
            		if ( title )
                		title   = title.get('html');
                	if ( popover.tip )
                	{
                		if ( title )
				            popover.tip.getElement('.popover-title').set('html',   title);
			            popover.tip.getElement('.popover-content').set('html', content);
                	}
		        }
			}).send();
        }
        var clone = Object.clone(Bootstrap.Popover.prototype);
        Class.refactor(Bootstrap.Popover, {
            _leave : function()
            {
                (function()
                {
                    if ( !this.visible ) {
                        this.data = null;
                        if ( this.tip )
                            this.tip.dispose();
                        this.tip = null;                        
                    }
                }).delay(100,this);
                this.previous();
            },
            _enter : function()
            {
                if ( !this.data ) {
                	getData(this);
                	data  = {
                		title   : this.element.get(this.options.title)   || 'Prompt.loading'.translate(),
                		content : this.element.get(this.options.content) || '<p class="uiActivityIndicator">&nbsp;</p>'
                	}
                    this.data = data;
                }
                if ( !this.data.content )
                    this._leave();
                else
                {
                    this.options.getContent = Function.from(this.data.content);
                    this.options.getTitle   = Function.from(this.data.title);
                    this.previous();
                }
            }
        });
        
        window.behavior.applyFilter(el, Behavior.getFilter('BS.Popover'));
        var instance = el.getBehaviorResult('BS.Popover'),
            url      = api.getAs(String, 'url');
        
        Bootstrap.Popover.prototype = clone;
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
	'Submit' : function(event, el, api) {
		event.stop();
		if ( el.hasClass('disabled') )
		{
		    return false;
		}
		data = el.get('href').toURI().getData();
		var form = Element.Form({action:el.get('href'), data:data});
		if ( el.get('target') ) {
			form.set('target', el.get('target'));
		}
		var submit = function(){
			el.spin();
			form.inject(document.body, 'bottom');
			form.submit();			
		}
		if ( api.get('promptMsg') ) {
			api.get('promptMsg').prompt({onConfirm:submit});
		}		
		else {			
			submit();
		}
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

(function(){
	Delegator.register('click', 'BS.showPopup', {
		handler: function(event, link, api) {
			var target, url;	
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
			else {
				var popup = Bootstrap.Popup.from({
					header : 'Prompt.loading'.translate(),
					body   : '<div class="uiActivityIndicator">&nbsp;</div>',
					buttons : [{name: 'Action.close'.translate(), dismiss:true}]
				});
				popup.show();			
				var req = new Request.HTML({
					url : url,
					onSuccess : function(nodes, tree, html) { 
					    var title = html.parseHTML().getElement('.popup-header');
					    var body  = html.parseHTML().getElement('.popup-body');
					    if ( title ) {
					    	popup.element.getElement('.modal-header').empty().adopt(title);
					    }
					    if ( body ) {
					    	popup.element.getElement('.modal-body').empty().adopt(body);
					    }
					}
				}).get();
			}
		}

	}, true);

})();

Request.Options = {};

/**
 * Paginations
 */
(function()
{      
    /**
     * Populates entities in colums in the tiled view
     */
    var MasonryLayout = new Class ({
    	
    	Implements :[Options],
    	
    	options : {
    		container  		: null,
    		numColumns		: 3,
    		record			: null
    	},
    	
    	initialize : function(options) 
        {
    		this.setOptions(options);

    		this.currentColumn = 0;
    		this.columns = new Array();
    		
    		this.scaffold();
    		this.add(this.options.container.getElements(this.options.record));
        },
        
        scaffold : function()
        {
        	if(this.options.container.getSize().x > 767)
    		{
    			this.numColumns = this.options.numColumns;
    			
    			this.options.container.addClass('row');
    			
    			var spanClass = 'span' + Math.floor(this.options.container.getSize().x / (80 * this.numColumns));
    			
    			for(var i=0; i < this.numColumns; i++)
	    			this.columns[i] = new Element('div').addClass(spanClass).inject(this.options.container);
    		}	
    		else
    		{
    			this.numColumns = 1;
    			this.columns[this.currentColumn] = this.options.container;
    		}
        },
        
        add : function(entities)
        {
        	entities = entities || [];
        	entities.each(function(entity) {
        		this.columns[this.currentColumn].adopt(entity);
        		window.behavior.apply(entity);
        		if( this.numColumns > 1 ) {
        			this.currentColumn++;
        			this.currentColumn = this.currentColumn % this.numColumns;
        		}	
        	}.bind(this));        	
        },
        
        getEntities : function() 
        {
        	return this.options.container.getElements(this.options.record);
        },
        
        update: function()
        {        	
        	var columns = new Array();
        	var entities = new Array();
        	var total = this.options.container.getElements(this.options.record).length;
        	
        	for(var i=0; i<this.numColumns; i++)
        		columns[i] = this.columns[i].getElements(this.options.record);
        	
        	var currentColumn = 0;
        	for(var k=0; k<total; k++)
        	{
        		entities.push(columns[currentColumn].shift());
        		currentColumn++;
        		currentColumn = currentColumn %this.numColumns;
        	}
        	
        	this.reset();
        	this.scaffold();
        	this.add(entities);
        },
        
        reset : function()
        {
        	this.options.container.empty();
        	this.currentColumn = 0;
        	this.columns = new Array();
        }
    });
    
    
    var Paginator = new Class({
    	
    	Implements : [Options, Events],
    	
    	options : {
    		resultHandler : null,
    		/*
    		onPageReady   : $empty
    		*/
    	},
    	
    	/**
    	 * Initializes a paginator 
    	 * 
    	 * Hash options {}
    	 */
    	initialize : function(options) 
    	{
    		this.setOptions(options);
    		this.spinner   = options.spinner;    		
    		this.pages     = new Paginator.Pages(options.url, options);
    		//set the next page that's supposed to show
    		this.nextPage = 1;
    	},
    	
    	/**
    	 * Shows the next page
    	 */
    	showNextPage : function() 
    	{
    		this.pages.get(this.nextPage, function(page) {    			
    			//console.log('handling results for page ' + page.number)
    			this.fireEvent('pageReady',[page]);
        		this.nextPage++;
    		}.bind(this));
    	},
    });
    
    Paginator.Pages = new Class({
    	
    	Implements : [Options],
        
        /**
         * pages 
         */
        pages    : {},
        
        /**
         * Default options
         */
        options : {
        	limit	 	    : 20,
        	resultSelector  : null,
        	startImmediatly : true,
        	batchSize	    : 2
        },
        
        /**
         * Initalizes the a pagination request using a base URL
         * 
         * String  url   pages base url
         * int     limit limit per page
         */
        initialize : function(url, options) 
        {
        	//console.log('create pages for base url ' + url);
        	this.url 	 = new URI(url);
        	this.setOptions(options);        	
        	this.requests = new Request.Queue({
        		concurrent : this.options.batchSize
        	});
        	this.limit    = this.options.limit;
        	this.resultSelector  = this.options.resultSelector;
        	this.currentBatch = 0;
        	if ( this.options.startImmediatly )
        		this._getBatch();
        },
        
        get  : function(number, onsuccess) 
        {
        	var page = this._getPage(number);
        	
        	if ( onsuccess ) 
        	{
        		//if the request is still running then add a success event
        		if ( page.request.isRunning() ) 
        		{
        			if ( !page.request.registered ) {
        				page.request.addEvent('success', onsuccess.bind(null,[this.pages[number]]));
        				page.request.registered = true;
        			}
            	}
        		else 
        		{
        			//if the request has finished running and hasn't been registered
        			//then call on onsuccess
        			if ( !page.request.registered ) {
        				onsuccess(page);
        			}
            	}
        	}        	
        	
        	return page;
        },
        
        /**
         * Gets a next batch
         */
        _getBatch : function()
        {
        	var start = (this.options.batchSize * this.currentBatch) + 1;
        	var end = start + this.options.batchSize;
        	//console.log('getting a batch ' + start + ' to ' + end, this.options.batchSize, this.currentBatch);
        	//always create a batch of pages
        	for(i=start;i<=end;i++) {
        		this._getPage(i);
        	}
        },
        
        /**
         * Creates a page using a number. 
         *  
         */
        _getPage : function(number)
        {
        	//if a page doesn't exists then queue batchSize of pages
        	if ( !this.pages[number] ) 
        	{        		
        		
        		var self  = this;
        		var page  = {
            		number   : number,
            		entities : null,
            		request  : new Request({
                		url 	: Object.clone(this.url).setData({start:number * this.limit, limit:this.limit}, true).toString(),
                		method  : 'get',
                		onSuccess : function() {
                			self.pages[number].entities = this.response.text.parseHTML().getElements(self.resultSelector);
                			if ( self.pages[number].entities.length < self.limit ) {
                				self.stopPagination = true;
                			}
                	//		console.log('fetched page ' + number + ' with ' + self.pages[number].entities.length + ' entities');
                		}
                	})
            	};
        		this.pages[number] = page;
        		//console.log('fetching page ' + number );
        		if ( !this.stopPagination ) {
        			this.requests.addRequest(number, page.request).send(number);
        		}
        	}
        	return this.pages[number];
        }
        
    });
    
    Behavior.addGlobalFilter('InfinitScroll', {
    	defaults : {
    		record  	: '.an-entity',
    		numColumns 	: 3,
    		limit		: 20,
    		url			: null,
    		scrollable  : window,
    		fixedheight : false
    	},
    	
    	setup : function(el, api)
    	{    		
    		var masonry = new MasonryLayout({
    			container  : el,
    			numColumns : api.getAs(Number, 'numColumns'),
    			record	   : api.get('record')
    		});
    		
    		//only instantiate a paginator if
    		//if the current number of entities > limit
    		if ( masonry.getEntities().length < api.getAs(Number, 'limit') )
    			return null
    			
    		var paginator = new Paginator({
    			resultSelector 	  : api.get('record'),
    			url		  		  : api.get('url'),
    			limit			  : api.getAs(Number, 'limit'),
    			startImmediatly   : el.isVisible()
    		});
    		    		    		
    		paginator.addEvent('pageReady', function(page){
    			this.add(page.entities);
    		}.bind(masonry));
    		
    		this.resizeTo = null;
    		window.addEvent('resize', function(){
    			if(this.resizeTo)
    				clearTimeout(this.resizeTo);
    			
    			this.resizeTo = setTimeout(function(){
    				masonry.update();
    			}, 50);
    		}.bind(this));

    		el.store('paginator', paginator);
    		el.store('masonry', masonry);
    		
    		var scroller = new ScrollLoader({
                scrollable : api.get('scrollable'),
                fixedheight: api.get('fixedheight'),
                onScroll   : function() {
                	if ( this.isVisible() ) {
                		this.retrieve('paginator').showNextPage();	
                	}
                }.bind(el)
            });
    	}
    });
})()

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

Delegator.register(['click'],'Comment', {
	handler  : function(event, el, api) {
		event.stop();
		var textarea = el.form.getElement('textarea');
		if ( textarea.setContentFromEditor )
			textarea.setContentFromEditor();
		if ( Form.Validator.getValidator('required').test(el.form.getElement('textarea')) )
			window.delegator.trigger('Request',el,'click');
	}
});

var ScrollLoader = new Class({

    Implements: [Options, Events],

    options: {
    //     onScroll: fn,
        mode: 'vertical',
        fixedheight: 0,
        scrollable : window
    },
    initialize: function(options) 
    {
        this.setOptions(options);
        this.scrollable = document.id(this.options.scrollable) || window; 
        this.bounds     = {
            scroll : this.scroll.bind(this)
        }
        this.attach();
    },
    attach: function() 
    {
        this.scrollable.addEvent('scroll', this.bounds.scroll);
        return this;
    },
    detach: function()
    {
        this.scrollable.removeEvent('scroll', this.bounds.scroll);
        return this;
    },
    scroll: function()
    {
    	var orientation = ( this.options.mode == 'vertical' ) ? 'y' : 'x';
    	var scroll 		= this.scrollable.getScroll()[orientation];
    	var scrollSize	= this.scrollable.getScrollSize()[orientation];
    	
    	//console.log('scroll size: ' + scrollSize);
    	//console.log('fire :' + Math.floor(scrollSize * 0.6));
    	//console.log('scroll: ' + scroll);
    	//console.log('---');
    	
    	if( (this.options.fixedheight && scroll < scrollSize) || scroll > Math.floor(scrollSize * 0.6) )
    		this.fireEvent('scroll');
    }
});

var EditEntityOptions = function() {
	return {
		replace : this.getParent('form'),
		url		: function() {
			return this.form.get('action') + '&layout=list&reset=1';
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
		
		this.form.ajaxRequest({
			method : 'post',
			url : this.form.get('action') + '&layout=list&reset=1',
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