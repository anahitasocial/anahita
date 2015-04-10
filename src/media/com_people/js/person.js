/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.widget('anahita.person', {
        
        options : {
            
        },
        
        _create : function () {
            
            var self = this;
            
            this.current = [];
            this.current['username'] = this.element.find(':input[name="username"]').val();
            this.current['email'] = this.element.find(':input[name="email"]').val();
            
            //validate uesrname
            this._on({
                
                'change [data-validate="username"], [data-validate="email"], [data-validate="password"] ' : function ( event ) {
                    
                    var elem = $(event.currentTarget);
                    
                    //clear prompt messages
                    this._prompt( elem );
                    
                    this._validate( elem ); 
                }
            }); 
         
        },
        
        _validate : function ( elem ) {
            
            var self = this;
            var type = elem.attr('name');
            var validity = elem[0].validity;
            
            //if no password is entered there is not need for validation
            if ( type == 'password' && elem.val() == '' ) {
                
                validity.valid = true;
                
                return;
            }
            
            //validate too short
           if( validity.tooShort ) {
               
               this._prompt( elem, StringLibAnahita.prompt[type].tooShort, 'error' );
               
               return;
           }
           
           //validate too long
           if( validity.tooLong ) {
               
                this._prompt( elem, StringLibAnahita.prompt[type].tooLong, 'error' );
               
                return;
            }
   
           //validate pattern mismatch     
           if( validity.patternMismatch ) {
               
               this._prompt( elem, StringLibAnahita.prompt[type].patternMismatch, 'error' );
               
               return;
           }
           
           var remoteVerification = ( type == 'username' || type == 'email' ) ? true : false;
           
           //remote validation    
           if ( remoteVerification && elem.val() !== '' && this.current[type] != elem.val() ) {
                
                $.ajax({
                               
                    method : 'post',
                    url : elem.data('url'),
                    data : {
                        action : 'validate',
                        key : type,
                        value : elem.val() || ''
                    },
                    headers: { 
                        accept: 'application/json'
                    },
                    complete : function ( xhr, state ) {
                        
                        if ( state == 'error' ) {
    
                           self._prompt( elem, StringLibAnahita.prompt[type].invalid, 'error' );
                           
                           validity.valid = true;
                           
                           return;
                        
                        } else {
                        
                            self._prompt( elem, StringLibAnahita.prompt[type].valid, 'success' );
                            
                            return;
                        }
                    }
                });
                
            }
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
    
    $('#person-form').person();
    
}(jQuery, window, document));