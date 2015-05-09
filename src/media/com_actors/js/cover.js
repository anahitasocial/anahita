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
            
            this._resizeCover( this.maxHeight );
            
            this.image.load(function(){
                self._resizeCover( self._getHeight() );
                $(this).hide().fadeIn(500);
            });
            
            $( window ).resize( function() {
                self._resizeCover( self._getHeight() );
            });
        },
        
        _resizeCover : function ( height ) {
            
            this.element.height( height );
            
            this.profile.find('.span2').css('margin-top', this.element.height() - 100 );
            
            if( $( window ).width() < 767 ) {
                
                this.profile.find('.span6').css('margin-top', 0 );
                this.profile.find('.span4').css('margin-top', 0 );
                
            } else {
                
                this.profile.find('.span6').css('margin-top', this.element.height() - 40 );
                this.profile.find('.span4').css('margin-top', this.element.height() - 60 );
            }
            
        },
        
        _getHeight : function () {
            return Math.min( this.image.height(), this.maxHeight );
        }
        
    });
    
    if( $('.profile-cover').length ) {
       $('.profile-cover').cover(); 
    }
    
    /*
    $.fn.anahitaCover = function() {
      
        var self = $(this);
        var image = $(self.find('img'));
        var maxHeight = self.data('max-height');
      
        var resizeCover = function( height ) {
            
            self.height(height);
            
            var profile = $('#actor-profile');
            
            profile.find('.span2').css('margin-top', self.height() - 100 );
            
            if( $( window ).width() < 767 ) {
                
                profile.find('.span6').css('margin-top', 0 );
                profile.find('.span4').css('margin-top', 0 );
                
            } else {
                
                profile.find('.span6').css('margin-top', self.height() - 40 );
                profile.find('.span4').css('margin-top', self.height() - 60 );
            }
        };
      
        var getHeight = function() {
           return Math.min( image.height(), maxHeight );  
        }; 
      
        resizeCover( maxHeight );
      
        image.load(function(){
            resizeCover( getHeight() );
            image.hide().fadeIn(500);
        });
        
        $( window ).resize( function() {
            resizeCover( getHeight() );
        });
    };
    
    if( $('.profile-cover').exists() ) {
       $('.profile-cover').anahitaCover(); 
    }
    */
    
}(jQuery, window, document));