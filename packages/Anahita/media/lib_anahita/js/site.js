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
