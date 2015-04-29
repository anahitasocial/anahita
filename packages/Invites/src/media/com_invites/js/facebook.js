/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.widget('anahita.invitesFacebook', {
        
        options : {
            
            appId : 0,
            subject : 'Message Subject',
            body : 'Message Body',
            appURL : 'http://',
            picture : ''
        },
        
        _create : function ( ) {
            
            var self = this;
            
            FB.init({ appId: this.options.appId, xfbml: true });
    
            this._on({
                'click [data-trigger="Invite"]' : function ( event ) {
                    
                    event.preventDefault();

                   $.ajax({
                       method : 'get',
                       headers: { 
                            accept: 'application/json'
                       },
                       url : 'index.php/invites/token/facebook',
                       success : function ( response ) {
                           self._openDialog( response.value );
                       }
                   });
                }
            });
        },
        
        _openDialog : function ( token ) {
            
            var self = this;
            var msgLink = this.options.appURL;
            msgLink += '?token=' + token ;
            
            //console.log( msgLink );
            
            FB.ui({
                
                display : 'iframe',
                method : 'send',
                link : msgLink,
                picture : this.options.picture,              
                name : this.options.subject,
                description : this.options.body
                
            },
            function(response) {
                
                if(response && response.success) {
                    
                    $.ajax({
                        method : 'post',
                        url : 'index.php/invites/token/facebook',
                        data : {
                            value : token
                        },
                        complete : function ( xhr, status ) {
                            if ( status == 'error' ) {
                            
                                //global alert
                                //console.log( 'error' );
                            
                            } else {
                            
                                //global alert
                                //console.log( 'success' );
                            
                            }
                        }
                    });
                }
                               
            }.bind(this));
        }

    });
    
}(jQuery, window, document));