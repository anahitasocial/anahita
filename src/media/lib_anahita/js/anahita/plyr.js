/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2016 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    if ( $('[data-trigger="video-player"]').length ) {
        plyr.setup('.an-media-video');
    }

    $(document).ajaxSuccess(function() {
        if ( $('[data-trigger="video-player"]').length ) {
            plyr.setup('.an-media-video');
        }
    });

}(jQuery, window, document));
