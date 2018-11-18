/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {

    'use strict';

    $.widget("anahita.cover", {
        options : {
            mobileWidth : 767
        },

        _create : function () {
            this.element.addClass('uiActivityIndicator');
            
            var size = ($(window).width() < this.options.mobileWidth) ? 'src-medium' : 'src-large';
            
            this.coverImage = new Image();
            this.coverImage.src = this.element.data(size);

            this._on(window, {
                load : function() {
                    this._setBackgroundImage();
                },
            });
        },

        _setBackgroundImage : function() {
            var self = this;
            this.element.fadeTo('fast', 0, function() {
                self.element
                .removeClass('uiActivityIndicator')
                .css('background-image', 'url(' + self.coverImage.src + ')');
            }).fadeTo('fast', 1);
        }
    });

    $(document).ready(function(){
        if( $('[data-trigger="Cover"]').length ) {
            $('[data-trigger="Cover"]').cover();
        }
    });


}(jQuery, window, document));
