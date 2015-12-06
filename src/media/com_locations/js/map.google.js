/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

  'use strict';

  $.fn.mapRender = function () {

      this.each(function(index, elem){

          $(elem).css('height', $(elem).closest('.an-entity').width() / 2);

          var map = new google.maps.Map( elem, {
                  mapTypeId: google.maps.MapTypeId.ROADMAP
              }
          );

          $(elem).data('map', map);

          var bounds = new google.maps.LatLngBounds();
          var dataLocations = $(elem).data('locations');

          $.each(dataLocations, function(index, dataLocation){

              var location = new google.maps.LatLng(
                  dataLocation.latitude,
                  dataLocation.longitude
              );

              bounds.extend(location);

              var marker = new google.maps.Marker({
                  position: location,
                  title: dataLocation.name
              }).setMap(map);
          });

          map.setCenter(bounds.getCenter());

          if(dataLocations.length == 1){
              map.setZoom(18);
          } else {
              map.fitBounds(bounds);
          }

      });

  };

  $('document').ready(function(){
      $('.entity-map').mapRender();
  });

}(jQuery, window, document));
