/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2016 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

 ;(function ($, window, document) {

 	'use strict';

  var startIndex = null;
  var url = null;

  $('[data-behavior="orderable"]').sortable({
      items: '> tr',
      handle: 'a.js-orderable-handle',
      axis: 'y',
      cursor: 'move',
      start: function ( event, ui ) {
          startIndex = ui.item.context.rowIndex;
          url = ui.item.data('url');
      },
      stop: function ( event, ui) {

          var change = ui.item.context.rowIndex - startIndex;

          if (change > 0) {
            change++;
          }

          $.ajax({
            method : 'post',
            url : url,
            data : {
                ordering : change
            },
            complete : function() {
              startIndex = null;
              url = null;
            }
          });
      }
  });

}(jQuery, window, document));
