/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2016 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

 ;(function ($, window, document) {

 	'use strict';

  $.widget("anahita.mediagrid", {

    options: {
      item : '.thumbnail-wrapper',
    },

    _create: function() {

      var self = this;
      this.items = new Array();
      this.total = 0;

      this.total = this.element.find(this.options.item).length;

      //when masonry event is triggered, render the items
      this._on( $(this.element) , {
          'entities-render' : function( event ) {
            self._render();
          },
          'entities-reset-render' : function ( event ) {
            self._reset();
            self._render();
          }
      });
    },

    _reset : function() {
      this.spans.empty();
      this.items = new Array();
      this.total = 0;
    },

    _render : function() {

      var self = this;
      var newItems = this.element.data('fetched-items');

      if(!newItems) {
        return;
      }

      $.each(newItems, function(index, item){
        self.element.append(item);
        self.items.push(item);
        self.total++;
      });

      this.element.data('fetched-items', null);
      $(this.element).trigger('entities-rendered');
		},

    _refresh : function(){

        this.element.empty();
        this.total = 0;

        var self = this;

        $.each(this.items, function(index, item){
            self.element.append(item);
            self.total++;
        });

        $(this.element).trigger('entities-rendered');
    }

  });

  var elements = $('.media-grid').mediagrid();

  $(document).ajaxSuccess(function() {
		var elements = $('.media-grid');
		$.each(elements, function( index, element ){
        if( !$(element).is(":data('anahita-mediagrid')") ) {
		      $(element).mediagrid();
		    }
		});
	});

}(jQuery, window, document));
