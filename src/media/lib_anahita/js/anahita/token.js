/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
   $.widget('anahita.token', {
       
       _create : function () {
           
           var self = this;
           var form = this.element;
           
           this._on({
               'submit' : function ( event ) {
                   
                   event.preventDefault();
                   self._send( form );
               }
           });
           
       },
       
       _send : function ( form ) {
            
            var self = this;
            var elem = form.find('input[type="email"]');
            
            elem.inputAlert();
            elem.inputAlert('clear');
            
            $.ajax({
                method : 'post',
                url : form.attr('action'),
                data : form.serialize(),
                complete : function ( xhr, state ) {
                    
                    if ( state == 'error' ) {
                        
                        elem.inputAlert('error', StringLibAnahita.prompt.token.unavailable );
                        
                    } else {
                       
                       elem.attr('disabled', true).addClass('disabled');
                       elem.inputAlert('success', StringLibAnahita.prompt.token.available );

                    }
                    
                }
            });
        }
   });
   
   
   if( $('#token-form').length ) {
      $('#token-form').token(); 
   } 
    
}(jQuery, window, document));