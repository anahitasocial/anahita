/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';
    
    $.fn.followPermission = function () {

      var form = $(this);

      var allowFollowRequest = form.find('input[name="allowFollowRequest"]');
      var access = form.find('select[name="access"]');

      if(access.val() != 'followers') {
        allowFollowRequest.prop('disabled', true);
      }

      form.find('select[name="access"]').on('change', function( event ) {
          if($(this).val() == 'followers'){
              allowFollowRequest.prop('disabled', false);
          } else {
              allowFollowRequest.prop('disabled', true);
          }
      });
    }

    $('form#profile-permissions').followPermission();

}(jQuery, window, document));
