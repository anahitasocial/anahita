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
            
            this.imageMedium = new Image();
            this.imageMedium.src = this.element.data('src-medium');
            
            this.imageLarge = new Image();
            this.imageLarge.src = this.element.data('src-large');
            
            this._on(this.imageMedium, {
                load: function (event) {
                    if ($(window).width() < this.options.mobileWidth) {
                        this._setBackgroundImage();
                    }
                }
            });

            this._on(this.imageLarge, {
                load: function (event) {
                    this._setBackgroundImage();
                }
            });

            this._on(window, {
                resize: function (event) {
                    this._setBackgroundImage();
                }
            });
        },

        _setBackgroundImage : function() {
            if ( $(window).width() < this.options.mobileWidth ) {
               var src = this.imageMedium.src
            } else {
               var src = this.imageLarge.src
            }

            var self = this;

            this.element.fadeTo('fast', 0, function() {
                self.element.css('background-image', 'url(' + src + ')').removeClass('uiActivityIndicator');
                self.element.parallax({ imageSrc: src });
            }).fadeTo('slow', 1);
        }
    });

    $(document).ready(function(){
        if( $('[data-trigger="Cover"]').length ) {
            $('[data-trigger="Cover"]').cover();
        }
    });


}(jQuery, window, document));
