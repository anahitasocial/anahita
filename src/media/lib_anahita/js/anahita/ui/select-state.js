/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.widget('anahita.stateSelector', { 

        _create : function () {
          
          var self = this;
          this.states = [];
            
          this.states[0] = $('[country="us"]');
          this.states[1] = $('[country="canada"]');
          this.states[2] = $('[country="custom"]');
          
          self._hideAll();
          
          var country = this.element.find('options:selected').val();
          
          this._show( country );  
          
          this._on('#country-selector', {
              'change' : function ( event ) {
                 
                 self._hideAll();
                 
                 var country = $(event.currentTarget).find('option:selected').val();
                 
                 self._show(country);
              }
          });
        },
        
        _hideAll : function () {
            
            $.each(this.states, function (index, state) {
                state.hide().attr('disabled', true);
            });
        },
        
        _show : function ( country ) {
            
            var index = 0; 
            
            if (country == 'US') {
                     index = 0;
                 } else if( country == 'CA' ) {
                     index = 1;
                 } else {
                     index = 2;
                 }
            
            
            this.states[index].show().attr('disabled', false);
        }
    });

    $('body').stateSelector();
    
}(jQuery, window, document));