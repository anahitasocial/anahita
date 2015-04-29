/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    'use strict';
    
    $.widget('anahita.inputAlert', {
        
        _create : function () {
            
            this.controlGroup = this.element.closest('.control-group');
            this.clear();
        },
        
        error : function ( msg ) {
            
            this.controlGroup.addClass( 'error' );
            this._addMessage( msg );
        },
        
        success : function ( msg ) {
            
            this.controlGroup.addClass( 'success' );
            this._addMessage( msg );
        },
        
        info : function ( msg ) {
            
            this.controlGroup.addClass( 'info' );
            this._addMessage( msg );
        },
        
        warning : function ( msg ) {
            
            this.controlGroup.addClass( 'warning' );
            this._addMessage( msg );
        },
        
        clear : function () {
            
            this.controlGroup.removeClass('error');
            this.controlGroup.removeClass('success');
            this.controlGroup.removeClass('info');
            this.controlGroup.removeClass('warning');
            
            if( this.controlGroup.find('.help-inline').length ) {
                this.controlGroup.find('.help-inline').remove();
            }
        },
        
        _addMessage : function ( msg ) {
            
            if( msg != '' ) {
                $( '<span class="help-inline">' + msg + '</span>' ).insertAfter(this.element);
            }
        }
    });
    
}(jQuery, window, document));
