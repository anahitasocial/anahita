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
    var coord_lng = document.getElementById('coordLng');
    var coord_lat = document.getElementById('coordLat');
    
    var autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.addListener('place_changed', function (){

        var place = autocomplete.getPlace();
        
        
        if (!place.geometry) {
          console.log("Autocomplete's returned place contains no geometry");
          $(coord_lng).val('');
          $(coord_lat).val('');
          return false;
        } else {
          $(coord_lng).val(place.geometry.location.lng());
          $(coord_lat).val(place.geometry.location.lat());
          $(input.form).trigger('SearchNearby');
        }

        return true;
    });

}(jQuery, window, document));
