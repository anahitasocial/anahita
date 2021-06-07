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

    'use strict';

    $.fn.AnActionSubscribe = function () {
        var a = $(this);

        $.ajax({
            type: 'POST',
            url: a.attr('href'),
            data: {
                action: a.data('action')
            },
            beforeSend: function () {
                a.fadeTo('fast', 0.3);
                a.parent().addClass('uiActivityIndicator');
            },
            success: function () {
                var action = a.data('action');
                var actions = {subscribe:'unsubscribe',unsubscribe:'subscribe'};
                var text = StringLibAnahita['action'][actions[action]];

                a.data('action', actions[action]).text(text);
                a.removeClass('action-' + action).addClass('action-'+actions[action]);
            },
            complete: function () {
                a.fadeTo('fast', 1);
                a.parent().removeClass('uiActivityIndicator');
            }
        });
    };

    $('body').on('click', 'a[data-action="subscribe"], a[data-action="unsubscribe"]', function (event) {
        event.preventDefault();
        $(this).AnActionSubscribe();
    });

}(jQuery, window, document));