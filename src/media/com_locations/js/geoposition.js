/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    $.fn.geoposition = function() {

      var self = $(this);

      navigator.geolocation.getCurrentPosition(
        function success( position ) {
          self.data('browser_coords', position.coords).trigger('geopositioned');
          $(document).trigger('geoposition');
        },
        function showError(error){
          console.log(error.message);
          self.data('browser_coords', null);
        },
        {
          enableHighAccuracy : true,
          timeout : 60000
        }
      );
    }

    $(document).ready(function ( event ){
        $(document).geoposition();
    });

}(jQuery, window, document));
