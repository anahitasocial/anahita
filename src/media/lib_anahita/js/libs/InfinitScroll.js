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
			return null;
			
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
			scrollable: api.get('scrollable'),
			fixedheight: api.get('fixedheight'),
			onScroll: function() {
    			if ( this.isVisible() ) {
    				this.retrieve('paginator').showNextPage();	
    			}
    		}.bind(el)
		});
	}
});