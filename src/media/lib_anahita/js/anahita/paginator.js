/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function($, window) {
    
    'use strict';
    
    $.widget('anahita.paginator',{
        
        options: {
          scrollToTop : true,
          limit : 20,
          entities : '.an-entities'
        },
        
        _getCreateOptions: function() {
            return this.element.data('paginationOptions');
        },
       
        _create: function() {
            
        	this._on(this.element, {
                "click a": function ( event ) {
                    
                	event.preventDefault();
                    
                	var li = $( event.target ).parent();
                    
                    if(!li.hasClass('disabled') && !li.hasClass('active')) {
                        this._getPage(event);
                    } 
                }
            })
        },
        
        _getPage: function(event) {
            
        	var a = $(event.target);
            var self = this;
            var currentEntities = $(this.element).prev(this.options.entities);
            
            $.ajax({
            	method : 'get',
            	url : a.attr('href'),
            	beforeSend : function (){
            		$(self.element).fadeTo('fast', 0.3);
            	},
            	success : function ( response ) {
            		
            		//self._updateHash(a.attr('href'));
            		
            		var entities = $(response).filter('.an-entities');
                    var pagination = $(response).filter('.pagination');
                    
                    pagination.paginator();
            		
                    $(currentEntities).replaceWith(entities);
                    $(self.element).replaceWith(pagination);
                    
                    if(self.options.scrollToTop) {
                        $('body,html').animate({scrollTop:'0px'},'slow');
                    }
            	}
            });
            
        },
        
        _updateHash: function(url) {
            var hash = url.split('?')
            window.location.hash = hash[1];
        }
        
    });
    
    $('[data-behavior*="pagination"]').paginator();
    
}(jQuery, window));