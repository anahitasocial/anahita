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
            profile : '#actor-profile',
            mobileWidth : 767 
        },
        
        _create : function () {

            this.profile = $(this.options.profile);
            
            this._setBackgroundImage();
            
            this._on( window, {
                'resize' : function ( event ){
                    this._setBackgroundImage();
                }
            });
        },
        
        _setBackgroundImage : function() {
            
            if ( $(window).width() < this.options.mobileWidth )
            {
               var src = this.element.data('src-medium'); 
            }   
            else
            {
               var src = this.element.data('src-large'); 
            }
                  
            this.element.css('background-image', 'url(' + src + ')' );    
        }
    });
    
    $(document).ready(function(){
        
        if( $('.profile-cover').length ) {
            $('.profile-cover').cover(); 
        }
        
    });
    
    
}(jQuery, window, document));