/**
 * Author: Rastin Mehr
 * Email: rastin@anahitapolis.com
 * Copyright 2015 rmdStudio Inc. www.rmdStudio.com
 * License: GPL3
 */

;(function ($, window) {

    'use strict';

    var title = $('title');
    var metaTitle = title.html();
    var badge = $('#new-notifications-counter');

    $.fn.viewer = function () {

        window.setInterval(updateNotificationCount, 30000);

        function updateNotificationCount() {
            $.ajax({
                type: 'GET',
                url: 'index.php?option=com_notifications&view=notifications&get=count',
            })
            .done(function (data) {
                badge.html(data.new_notifications);

                if (data.new_notifications > 0) {
                    title.html('(' + data.new_notifications + ') ' + metaTitle);
                    badge.addClass('badge-important');
                } else {
                    title.html(metaTitle);
                    badge.removeClass('badge-important');
                }
            });
        }
    };
    
    $.viewer();

}(jQuery, window));