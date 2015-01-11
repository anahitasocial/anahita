/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window, document) {
	
	'use strict';

	$.fn.gadget = function () {
		
		this.filter(".an-gadget").each(function(){
			
			var gadget = $(this);
			var content = gadget.find(".gadget-content");
			
			if(content.children('div').length === 0)
				content.load(gadget.data('url'));
		});
		
	};
	
	$(".an-gadget").gadget();
	
}(jQuery, window, document));


