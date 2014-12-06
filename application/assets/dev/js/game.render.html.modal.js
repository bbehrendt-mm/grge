goog.provide('game.render.html.modal');

goog.require('game'); 
goog.require('game.render');
goog.require('game.render.html');

game.render.html.modal = {

    blend: function(callback, clickable) {
        var targetZ = $.topZIndex('*') + 1;
        var blend = $('<div class="doc-blend" id="doc-blend-' + targetZ + '"></div>').css('z-index', targetZ).appendTo('body')
            .css('opacity', 0).animate({
                opacity: 1
            }, 500).on('callback', function()
            {
                if (typeof(callback) == 'function')
                    callback();
            });

        if (clickable)
            blend.on('click', function() {
                game.render.html.modal.unblend(targetZ, true);
            });

        return targetZ;
    },

    unblend: function(targetZ, invokeCallback) {
        $('.doc-blend').each(function()
        {
            if ($(this).css('z-index') >= targetZ)
            {
                if (invokeCallback)
                    $(this).trigger('callback');

                $(this).stop().animate({
                    opacity: 0
                }, 200, 'swing', function()
                {
                    $(this).remove();
                });
            }
        })
    },

    work: function() {
        if ($('#loader').length)
            return;

        var loader = $('<div id="loader"><i class="fa fa-spin fa-cog"></i></div>').appendTo('body');
        var z = 1 + game.render.html.modal.blend(function(){
            loader.stop().animate({
                opacity: 0
            }, 200, 'swing', function() {
                $(this).remove();
            })
        });

        loader.css({
            opacity: 0,
            top: 64,
            left: $(window).width()/2 - 25,
            'z-index': z
        }).animate({
            opacity: 1
        }, 500, 'swing')
    }
};