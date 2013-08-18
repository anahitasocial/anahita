var Paginator = new Class({
    	
    	Implements : [Options, Events],
    	
    	options : {
	    	// onPageReady   : fn,
    	resultHandler : null
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
	}
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