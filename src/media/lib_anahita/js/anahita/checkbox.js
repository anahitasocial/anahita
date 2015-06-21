/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
	
	'use strict';
	
	$.widget("anahita.checkbox", {
		
		options : {
			toggleClass : 'selected'
		},
		
		_create : function(){

			this.cb = $(document.createElement( "input" ));
			this.element.append(this.cb);
			
			this.cb.attr({
				type : 'checkbox',
				name : this.element.data('checkbox-name'),
				value : this.element.data('checkbox-value')
			}).hide();
			
			this._on(
					this.element, { 
					click : function (event){
						this.toggle();
					}
			});
		},
		
		toggle : function(){
			
			if(this.cb.attr('checked'))
				this.uncheck();
			else
				this.check();
		},
		
		check : function(){
			this.cb.attr('checked', true);
			this.element.addClass(this.options.toggleClass);
		},
		
		uncheck : function(){
			this.cb.attr('checked', false);
			this.element.removeClass(this.options.toggleClass);
		}
	});
	
	var bindCheckbox = function (){
	    
	    var elements = $("[data-behavior='Checkbox']");
        
        $.each(elements, function( index, element ){
            
            if( !$(element).is(":data('anahita-checkbox')") ) {
            
              $(element).checkbox();
            
            }
        });  
	};
	
	bindCheckbox();

	$(document).ajaxSuccess(function() {
        bindCheckbox();
    });
	
}(jQuery, window, document));