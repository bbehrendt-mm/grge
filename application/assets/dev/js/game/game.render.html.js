goog.provide('game.render.html');
goog.require('game');
goog.require('game.render');

game.render.html = {
    put: function(id, content) {

        var target;
        switch (id) {
            case ':body':
                target = $('body');
                break;
            case ':footer':
                target = $('footer');
                if (!target.size())
                    $('html').append(target = $('<footer />'));
                break;
            default: target = $('#'+id); break;
        }

        if (!target.length) return;

        target.empty().html(content);
    },

    hint: function(show) {
        var target = $('#hint').empty();
        if (show) {
            var z = $.topZIndex('*:not(#notifications)');
            target.css('z-index',z+1);
        } else target.css('z-index',0);
        return target;
    },

    notify: function(type, content, title, custom_timeout) {
        if (!custom_timeout)
            custom_timeout = 10000;

        var z = $.topZIndex('*:not(#notifications)');
        var target = $('#notifications');
        target.css('z-index',z+1);

        var notification  = $('<div class="' + type + '"><div></div><div>' + content + '</div></div>');
        if (title)
            notification.find('> div:last-child').prepend('<b class="headline">' + title + '</b>');

        notification.appendTo(target).click(function() {

            if (!game.mobile)
                notification.animate({
                    width: 96,
                    'margin-left': 252
                }, 200, 'swing', function() {
                    notification.animate({
                        opacity: 0,
                        transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)'
                    }, 100, 'swing', function() {
                        notification.css({
                            transform: 'scale(1)',
                            'min-height': 0
                        }).animate({
                            height: 0
                        }, 100, 'swing', function() {
                            notification.remove();
                        })
                    })
                });
            else
                notification.animate({
                    opacity: 0,
                    transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)'
                }, 200, 'swing', function() {
                    notification.remove();
                });
        }).mouseleave(function() {
            var timeout_id = window.setTimeout(function() {
                notification.trigger('click');
            }, custom_timeout);
            notification.off('mouseenter').on('mouseenter',function() {
                window.clearTimeout(timeout_id);
            })
        }).trigger('mouseleave');

        if (!game.mobile)
            notification.css({
                width: 96,
                'margin-left': 252,
                opacity: 0,
                transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)'
            }).animate({
                opacity: 1,
                transform: 'scale(1)'
            }, 200, 'swing', function() {
                notification.animate({
                    width: 600,
                    'margin-left': 0
                }, 300, 'swing');
            });
        else
            notification.css({
                opacity: 0,
                transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)'
            }).animate({
                opacity: 1,
                transform: 'scale(1)'
            });
    }
};