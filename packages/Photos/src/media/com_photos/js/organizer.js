var SetOrganizer = new Class({
	
	Implements :[Options],
	
	options : {
		mediums		: 'set-mediums',
		selector	: 'medium-selector',
		cover		: 'set-cover',
		form		: 'set-form'
	},
	
	initialize : function(options) 
    {
		this.setOptions(options);
		this.inUse = false;
		
		this.slide 			= new Fx.Slide(this.options.selector);
		this.sortableItems 	= '#' + this.options.selector + ' .media-grid, #' + this.options.mediums + ' .media-grid';
		this.droppables 	= '.' + this.options.mediums + ' .media-grid';
		
		this.setId 			= document.id(this.options.selector).get('set_id');
		this.oid 			= document.id(this.options.selector).get('oid');
		this.baseURL 		= 'index.php?option=com_photos';
		
		if(this.setId)
			this.slide.hide();
		else
			this.updateSortables();
    },
    
    hide : function()
    {
    	document.id(this.options.mediums).removeClass('an-highlight');
    	this.slide.slideOut();
    	this.refreshSet();
    	this.inUse = false;
    },
    
    show : function()
    {
    	if(this.inUse)
    		return;
    	
    	var req = new Request.HTML({
    		method  : 'get',
    		url		: this.baseURL + '&view=photos&oid=' + this.oid + '&layout=selector&exclude_set=' + this.setId,
    		update	: this.options.selector,
    		onSuccess : function(){
    			this.updateSortables();
    			this.slide.slideIn();
    			this.inUse = true;
    		}.bind(this)
    	}).send();
    },
    
    updateSortables : function()
    {
    	document.getElements('#' + this.options.selector + ' .media-grid a').each(function(item, index){
			item.addEvent('click', function(e, el){
				e.stop();
			}).setStyle('cursor', 'move');
		});
		
    	document.getElements('#' + this.options.mediums + ' .media-grid a').each(function(item, index){
			item.addEvent('click', function(e, el){
				e.stop();
			}).setStyle('cursor', 'move');
		});
		
		this.sortables = new Sortables(this.sortableItems, {
			clone: true,
			revert: true,
		    opacity: 0.7,
		    dragOptions: {
				'droppables': this.droppables
		    },
		});
		
		document.id(this.options.mediums).addClass('an-highlight');
    },
    
    refreshSet : function()
    {
    	document.id(this.options.mediums).getParent().load(this.baseURL + '&view=set&layout=photos&id=' + this.setId);
    	document.id(this.options.cover).getParent().load(this.baseURL + '&view=set&layout=cover&id=' + this.setId);
    },
    
    addSet: function(el)
    {
    	var form = document.id(this.options.form);
    	var photoCount = 0;
    	
    	document.getElements('#' + this.options.mediums + ' .thumbnail-wrapper').reverse().each(function(item, index){
			var inputField = new Element('input', {
				type: 'hidden',
				name: 'photo_id[]',
				value: item.get('mid')
			})
			
			inputField.inject(form, 'top'); 
			photoCount++;
		}.bind(this));
		
    	if(form.get('validator').validate() && photoCount)
    		form.submit();
    },
    
    updateSet: function(el)
    {
    	var data = '';
		var photo_ids = new Array();
		var photoCount = 0;
		
		document.getElements('#' + this.options.mediums + ' .thumbnail-wrapper').each(function(item, index){
			data = data + '&photo_id[]=' + item.get('mid');
			photoCount++;
		});
		
		if(photoCount)
		{
			el.ajaxRequest({
				method: 'post',
				url: this.baseURL + '&view=set&id=' + this.setId,
				data: 'action=updatephotos' + data,
				onComplete: function(){	
					this.hide();
				}.bind(this)
			}).send();
		}
    },
    
    coverSelect: function()
    {
    	if(this.inUse)
    		return;
    	
    	var req = new Request.HTML({
    		method  : 'get',
    		url		: this.baseURL + '&view=set&oid=' + this.oid + '&layout=cover_edit&id=' + this.setId,
    		update	: this.options.selector,
    		onSuccess : function(){
    			
    			document.getElements('#' + this.options.mediums + ' .media-grid a').each(function(item){
    				
    				item.addEvent('click', function(e){
    					e.stop();
    					
    					item.ajaxRequest({
    						method			: 'post',
    						url				: this.baseURL + '&view=set&id=' + this.setId,
    						data			: 'action=updatecover&id=' + this.setId + '&photo_id=' + item.getParent().get('mid'),
    						onComplete		: function (){
    							document.id(this.options.cover).getParent().load(this.baseURL + '&view=set&layout=cover&id=' + this.setId);    							
    							var scrollTop = new Fx.Scroll(window).toTop();
    						}.bind(this)
    					}).send();
    					
    				}.bind(this));
    			}.bind(this));
    			
    			document.id(this.options.mediums).addClass('an-highlight');
    			this.slide.slideIn();
    			this.inUse = true;
    			
    		}.bind(this)
    	}).send();
    },
});

var organizer = new SetOrganizer();

Delegator.register('click', {
	
	'Organize' : function(event, el, api) {
		event.stop();
		organizer.show();
	},
	
	'Close' : function(event, el, api) {
		event.stop();
		organizer.hide();
	},
	
	'Update' : function(event, el, api){
		event.stop();
		organizer.updateSet(el);
	},
	
	'Add' : function(event, el, api)
	{
		event.stop();
		organizer.addSet(el);
	},
	
	'Cancel' : function(event, el, api)
	{
		event.stop();
		window.location = el.href;
	},
	
	'ChangeCover' : function(event, el, api){
		event.stop();
		organizer.coverSelect();
	}
});

Behavior.addGlobalPlugin('InfinitScroll', 'Sortable',
	function(el, api,instance){
    	var paginator = el.retrieve('paginator');
 		if ( paginator ) {
    		paginator.addEvent('pageReady', function(page){
	 			organizer.updateSortables();
        	});
 		}
	}
);