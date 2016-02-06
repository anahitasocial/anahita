/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2016 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

 ;(function ($, window, document) {

 	'use strict';

  $.widget("anahita.masonry", {

    options: {
      item : '.an-entity',
      row : '.row',
      span : 'div[class*="span"]',
      columns : 2,
      mobileWidth : 620
    },

    _create: function() {

      var self = this;
      this.items = new Array();
      this.row = null;
      this.spans = null;
      this.total = 0;

      this._setGrid();

      this.total = this.element.find(this.options.item).length;

      //when masonry event is triggered, render the items
      this._on( $(document) , {
          'masonry' : function( event ) {
            self._render();
          }
      });

      //refresh the layout after window resize
      $(window).one("resize", function () {
        self._refresh();
      });
    },

    _setGrid : function() {
      if (this.element.find(this.options.row).length == 0) {

          this.element.append('<div class="row"></div>');
          this.row = this.element.find(this.options.row);

          var columns = this.options.columns;

          if( this.element.width() < this.options.mobileWidth ) {
            columns = 1;
          }

          var span = 12 / columns;

          for(var i = 0; i < columns; i++ ) {
             this.row.append('<div class="span' + span + '"></div>');
          }

          this.spans = this.row.find('.span' + span);

      } else {

        this.row = this.element.find(this.options.row);
        this.spans = this.row.find(this.options.span);
        this.total = this.row.find(this.options.item).length;

        if(this.total > 0){

          var self = this;
          var columns = new Array();
          $.each(this.spans, function(index, span) {
            columns.push($(span).find(self.options.item).toArray());
          });

          for(var i=0; i < this.total; i++) {
            var column = columns[ i % columns.length ];
            this.items.push(column.shift());
          }
        }
      }
    },

    _render : function() {

      var self = this;
      var newItems = this.element.data('fetched-items');
      var columns = this.spans.length;

      $.each(newItems, function(index, item){
        var span = self.spans[ self.total % columns];
        $(span).append(item);
        self.items.push(item);
        self.total++;
      });

      this.element.data('fetched-items', null);
		},

    _refresh : function(){

        this.element.empty();
        this.total = 0;
        this._setGrid();

        var self = this;
        var columns = this.spans.length;

        $.each(this.items, function(index, item){
            var span = self.spans[ self.total % columns];
            $(span).append(item);
            self.total++;
        });
    }

  });

  var elements = $('.masonry').masonry();

}(jQuery, window, document));
