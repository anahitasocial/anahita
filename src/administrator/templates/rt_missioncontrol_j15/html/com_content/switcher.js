var JSwitcher = new Class({

	toggler : null, //holds the active toggler
	page    : null, //holds the active page


	initialize: function(toggler, element, options)
	{
		this.setOptions(options);

		var self = this;

		togglers = toggler.getElements('a');
		for (i=0; i < togglers.length; i++) {
			togglers[i].addEvent( 'click', function() { self.switchTo(this); } );
		}

		//hide all
		elements = element.getElements('div[id^=page-]');
		for (i=0; i < elements.length; i++) {
			this.hide(elements[i])
		}

		this.toggler = toggler.getElement('a.active');
		this.page    = $('page-'+ this.toggler.id);

		this.show(this.page);
		
	},

	switchTo: function(toggler)
	{
		page = $chk(toggler) ? $('page-'+toggler.id) : null;
		if(page && page != this.page)
		{
			//hide old element
			if(this.page) {
				this.hide(this.page);
			}

			//show new element
			this.show(page);

			toggler.addClass('active');
			if (this.toggler) {
				this.toggler.removeClass('active');
			}
			this.page    = page;
			this.toggler = toggler;

		}
	},

	hide: function(element) {
		element.setStyle('display', 'none');
	},

	show: function (element) {
		element.setStyle('display', 'block');
	}
});

JSwitcher.implement(new Options);

document.switcher = null;
window.addEvent('domready', function(){
 	toggler = $('mc-article-tabs')
  	element = $('mc-article')
  	if(element) {
  		document.switcher = new JSwitcher(toggler, element);
  	}
});
