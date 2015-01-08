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
        }, true, true);

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

    builder: function(popup,data) {
        if (!popup) popup = core.popup.spawn(700,450);
        else popup.empty();
        var frame = $('<div />').addClass('row').appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 24,
                bottom: 0,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var filters, close;
        $('<div />').addClass('row').append(
            filters = $('<div />').addClass('cell rw-11')
        ).append(
            close = $('<div />').addClass('cell rw-1 right')
        ).appendTo(popup);

        filters.append($('<div />').addClass('btn small btn-exp').text('Angezeigte Projekte filtern...'));
        close.append($('<div />').addClass('btn small').append($('<i/>').addClass('fa fa-times')).click(function() {
            popup.trigger('unpop');
        }));

        var build_func = function(bdata) {
            frame.empty();
            $.each(bdata.blueprints, function(k,v) {
                frame.append($('<div />').addClass('cell padded rw-4').append(core.snippets.blueprint(v, bdata.energy, bdata.blueprints, function() {
                    core.command('location/builder', {build: k}, false, function(new_data) {
                        core.popup.builder(popup,new_data);
                        popup.on('unpop', function() {
                            setTimeout(function() {
                                core.command();
                            }, 100);
                        })
                    }, true)
                })));
            })
        };

        if (!data) {
            frame.append(core.snippets.wait());
            core.command('location/builder', {}, true, build_func);
        } else build_func(data);
    }
};