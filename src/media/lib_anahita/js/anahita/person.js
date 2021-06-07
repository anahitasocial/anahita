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
        
        _create : function () {
            
            var self = this;
            
            this.current = [];
            this.current['username'] = this.element.find(':input[data-validate="username"]').val();
            this.current['email'] = this.element.find(':input[data-validate="email"]').val();
            
            //validate uesrname
            this._on({
                
                'change [data-validate="username"], [data-validate="email"], [data-validate="password"] ' : function ( event ) {
                    
                    var elem = $(event.currentTarget);
                    
                    elem.inputAlert();
                    elem.inputAlert('clear');
                    
                    this._validate( elem ); 
                }
            });
        },
        
        _validate : function ( elem ) {
            
            var self = this;
            var type = elem.data('validate');
            var validity = elem[0].validity;
            
            //validate too short
           if( validity.tooShort ) {
               
               elem.inputAlert('error', StringLibAnahita.prompt[type].tooShort );
               
               return;
           }
           
           //validate too long
           if( validity.tooLong ) {
               
                elem.inputAlert('error', StringLibAnahita.prompt[type].tooLong );
               
                return;
            }
   
           //validate pattern mismatch     
           if( validity.patternMismatch ) {
               
               elem.inputAlert('error', StringLibAnahita.prompt[type].patternMismatch );
               
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
    
                           elem.inputAlert('error', StringLibAnahita.prompt[type].invalid );
                           
                           validity.valid = true;
                           
                           return;
                        
                        } else {
                        
                            elem.inputAlert('success', StringLibAnahita.prompt[type].valid );
                            
                            return;
                        }
                    }
                });
                
            }
        }
    });
    
    if( $('#person-form').length ) {
        $('#person-form').person();
    }
    
    $(document).ajaxSuccess(function() {
        
        var elements = $('#person-form');
        
        $.each(elements, function( index, element ){
            
            if( !$(element).is(":data('anahita-person')") ) {
            
              $(element).person();
            
            }
        });
    });
    
}(jQuery, window, document));