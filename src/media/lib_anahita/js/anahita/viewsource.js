/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    $('body').on('click', '[data-trigger="ViewSource"]', function ( event ) {
        
        event.preventDefault();
        
        var codes = $(this).closest('.an-code').find('pre');
        
        var content = '';

        $.each(codes, function ( index, value ) {
            content += $(value).html() + "\n";
        });
        
        sourceWindow = window.open('','','resizable=no,scrollbars=yes,width=800,height=600');
        
        sourceWindow.document.body.innerHTML = '<pre>' + content + '</pre>';
    });
    
}(jQuery, window, document));