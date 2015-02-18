/**
 * Author: Nick Swinford
 * Email: NicholasJohn16@gmail.com
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

;(function ($, window, document) {
    
    $.fn.sortable = function() {
        
        var a = $(this);
        
        $.ajax({
           url: a.attr('href'),
           beforeSend: function() {
               a.parent().fadeTo('fast', 0.3).addClass('uiActivityIndicator');
           },
           success: function(response) {
               var entities = $(response).filter('#an-entities-main');
               $('#an-entities-main-wrapper').html(entities);
               a.parent().siblings().removeClass('active');
               a.parent().addClass('active');
           },
           complete: function() {
               a.parent().fadeTo('fast', 1).removeClass('uiActivityIndicator');
           }
        });
    }
    
    $('body').on('click','ul[data-behavior="sortable"] a', function(event) {
        event.preventDefault();
        $(this).sortable();
    });
    
}(jQuery, window, document));