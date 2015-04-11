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
            
            //clear prompt messages
            this._prompt( elem );
            
            $.ajax({
                method : 'post',
                url : form.attr('action'),
                data : form.serialize(),
                complete : function ( xhr, state ) {
                    
                    if ( state == 'error' ) {
                        self._prompt( elem, StringLibAnahita.prompt.token.unavailable, 'error');
                    } else {
                       elem.attr('disabled', true).addClass('disabled');
                       self._prompt( elem, StringLibAnahita.prompt.token.available, 'success'); 
                    }
                    
                }
            });
        },
       
       _prompt : function ( elem, msg, status ) {
            
            msg = msg || '';
            status = status || '';
            
            var controlGroup = elem.closest('.control-group');
            
            controlGroup.removeClass('error').removeClass('success');
            
            controlGroup.addClass( status );
            
            if( controlGroup.find('.help-inline').is('.help-inline') ) {
                controlGroup.find('.help-inline').remove();
            }
            
            if( msg != '' ) {
                $( '<span class="help-inline">' + msg + '</span>' ).insertAfter(elem);
            }
        }
   });
    
   $('#token-form').token();
    
}(jQuery, window, document));