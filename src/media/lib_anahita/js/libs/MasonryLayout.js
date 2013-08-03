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