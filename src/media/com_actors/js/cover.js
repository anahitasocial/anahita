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
        
        _create : function () {
            
            var self = this;
            this.image = this.element.find('img');
            this.maxHeight = this.element.data('max-height');
            this.profile = $('#actor-profile');
            
            this.image.hide();
            this._resetImageSrc();
            this._resizeCover( self._getHeight() );
            this.image.fadeIn(500);
            
            this._on( window, {
                'resize' : function ( event ){
                    self._resetImageSrc();
                    self._resizeCover( self._getHeight() );
                }
            });
        },
        
        _resizeCover : function ( height ) {
            
            this.element.height( height );
            
            this.profile.find('.span2').css('margin-top', this.element.height() - 140 );
            
            if( $( window ).width() < 767 ) {
                
                this.profile.find('.span6').css('margin-top', 0 );
                this.profile.find('.span4').css('margin-top', 0 );
                
            } else {
 
                this.profile.find('.span6').css('margin-top', this.element.height() - 40 );
                this.profile.find('.span4').css('margin-top', this.element.height() - 60 );
            }
            
        },
        
        _getHeight : function () {
            
            if( this.image.height() && this.image.height() < this.maxHeight )
            {
               return this.image.height(); 
            }
                
            return this.maxHeight;
        },
        
        _resetImageSrc : function () {

            var src = this.image.attr('src');
                    
            if( $( window ).width() < 767 ) {
                src = src.replace("original", "medium");    
            } else {
                src = src.replace("medium", "original");
            } 
            
            this.image.attr('src', src);
        }
        
    });
    
    if( $('.profile-cover').length ) {
       $('.profile-cover').cover(); 
    }
    
}(jQuery, window, document));