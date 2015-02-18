;(function ($, window, document) {

    'use strict';

    $.fn.AnActionCommentStatus = function () {

        var a = $(this);

        $.ajax({
            type: 'POST',
            url: a.attr('href'),
            data: {
                action: 'commentstatus',
                status: a.data('status')
            },
            beforeSend: function () {
                a.fadeTo('fast', 0.3);
                a.parent().addClass('uiActivityIndicator');
            },
            success: function (response) {
                var status = 1 - parseInt(a.data('status'));
                var text = (status ? StringLibAnahita.action.openComments : StringLibAnahita.action.closeComments);

                a.data('status', status).text(text);

                $.get(a.attr('href'), function(response) {
                    if (status) {
                        $('.an-comment-form').remove();
                        $('.an-comments-wrapper').append($(response).find('div.alert'));
                    } else {
                        $('.an-comments-wrapper').find('div.alert').remove();
                        $('.an-comments-wrapper').append($(response).find('.an-comment-form'));
                    }
                });
            },
            complete: function() {
                a.fadeTo('fast',1);
                a.parent().removeClass('uiActivityIndicator');
            }
        });

        return this;

    };

    $('body').on('click', 'a[data-action="commentstatus"]', function (event) {
        event.preventDefault();
        $(this).AnActionCommentStatus();
    });

}(jQuery, window, document));