/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2016 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

 ;(function ($, window, document) {

 	'use strict';

  var startIndex = 0;

  $('[data-behavior="orderable"]').sortable({
      items: '> tr',
      handle: 'a.js-orderable-handle',
      axis: 'y',
      cursor: 'move',
      start: function(event, ui) {
          startIndex = ui.item.index();
      },
      stop: function ( event, ui) {

          var ordering = 0;
          var url = ui.item.data('url') + '.json';
          var direction = ui.item.index() - startIndex;
          var rows = $(event.target).children();

          // if direction > 0, item is moving up on the list
          if( direction > 0 ) {
              ordering = $(ui.item).prev().data('ordering');
          } else {
              ordering = $(ui.item).next().data('ordering');
          }

          if ( ordering == 0 ) {
             ordering = ui.item.index() + 1;
          }

          $.ajax({
            method : 'post',
            url : url,
            data : {
                ordering : ordering
            },
            complete : function(response) {
              $.ajax({
                url: window.location.href,
                data : {
                    format : 'json'
                },
                success : function(response) {
                    var entities = response.data;
                    rows.each(function(index, row) {
                        $(row).data('ordering', entities[index].ordering);
                    });
                }
              });
            }
          });
      }
  });

}(jQuery, window, document));
