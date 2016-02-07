/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    var input = document.getElementById('SearchNearby');
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function (){

        var place = autocomplete.getPlace();

        if (!place.geometry) {
          console.log("Autocomplete's returned place contains no geometry");
          return false;
        } else {
          $(input.form).trigger('SearchNearby');
        }

        return true;
    });

}(jQuery, window, document));
