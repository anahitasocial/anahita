
var ScrollLoader = new Class({

    Implements: [Options, Events],

    options: {
        // onScroll: fn,
        mode: 'vertical',
        fixedheight: 0,
        scrollable : window
    },
    initialize: function(options) {
        this.setOptions(options);
        this.scrollable = document.id(this.options.scrollable) || window; 
        this.bounds     = {
            scroll : this.scroll.bind(this)
        };
        this.attach();
    },
    attach: function() {
        this.scrollable.addEvent('scroll', this.bounds.scroll);
        return this;
    },
    detach: function() {
        this.scrollable.removeEvent('scroll', this.bounds.scroll);
        return this;
    },
    scroll: function() {
    	var orientation = ( this.options.mode == 'vertical' ) ? 'y' : 'x';
    	var scroll 		= this.scrollable.getScroll()[orientation];
    	var scrollSize	= this.scrollable.getScrollSize()[orientation];
    	var scrollWin   = this.scrollable.getSize()[orientation];    	
//    	console.log('scroll size: ' + scrollSize);
//    	console.log('fire :' + Math.floor(scrollSize * 0.6));
//    	console.log('fire: ' + Math.max(scrollSize - scrollWin * 2, 0));
//    	console.log('scroll: ' + scroll);
//    	console.log('---');    	
    	
    	/*@TODO this line added @kulbakin 
    	 * 
    	 * it works for some views but for stories in the dashboard view it doesn't work
    	 * scroll > scrollSize - scrollWin * 2
    	 */
    	if( (this.options.fixedheight && scroll < scrollSize)
    				|| scroll > scrollSize - scrollWin * 2 ) { 
    		console.log(scroll,scrollSize,scrollWin,scrollSize - scrollWin * 2);
    		this.fireEvent('scroll');
    	}
    		
    }
});