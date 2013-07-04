
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
    	if ( !instance.options.animate ) 
    	{
    		instance.options.delayIn  = 0;
    		instance.options.delayOut = 0;
    	}    	
    	
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
        if ( instance.options.trigger == 'click') {
        	el.addEvent('click', function(e){e.stop()});        	
            instance._leave();
        }
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