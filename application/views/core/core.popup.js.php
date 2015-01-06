core.popup = {
    spawn: function(dx,dy) {
        var popup = $('<div />').addClass('popup').css({
            height: dy,
            width: dx,
            top: 60,
            left: $(window).width()/2 - dx/2
        });

        var z = game.render.html.modal.blend(function() {
            popup.addClass('disabled').css({
                '-webkit-filter': 'blur(5px)',
                'filter': 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: 'scale(1.5)'
            }, 400, 'swing', function() {
                $(this).remove();
            });
        }, true);

        popup.css('z-index',z+1).on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).appendTo('body').css({
            opacity: 0,
            transform: 'scale(0.5)',
            'transition': 'filter 0.4s ease, -webkit-filter 0.4s ease',
            '-webkit-filter': 'blur(5px)',
            'filter': 'blur(5px)'
        }).animate({
            opacity: 1,
            transform: 'scale(1)'
        }, 500).css({
            '-webkit-filter': 'blur(0px)',
            'filter': 'blur(0px)'
        });

        return popup;
    },

    builder: function() {
        var popup = core.popup.spawn(700,450);

        core.command('location/builder', {}, true, function(data) {
            console.log(data);
        })
    }
};