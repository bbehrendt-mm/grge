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
            '-webkit-filter': '',
            'filter': ''
        });

        return popup;
    },

    genericBlueprintLoader: function(type, popup, data, frame, filters, close) {
        if (!popup) popup = core.popup.spawn(700,450);

        if (!frame || !filters || !close) {
            popup.empty();

            frame = $('<div />').addClass('row').appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: 24,
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            );

            $('<div />').addClass('row').append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text('Angezeigte Projekte filtern...'));
            close.append($('<div />').addClass('btn small').append($('<i/>').addClass('fa fa-times')).click(function() {
                popup.trigger('unpop');
            }));
        }

        var build_func = function(bdata) {
            console.log(bdata);
            frame.empty();
            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;

                frame.append($('<div />').addClass('cell padded rw-4').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
                    var prev_scroll = $('.popup').find('>*:first-child').scrollTop();
                    popup.addClass('disabled');
                    core.command('location/' + type, {build: k}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        popup.off('unpop').on('unpop', function() {
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
            core.command('location/' + type, {}, true, build_func);
        } else build_func(data);
    },

    builder: function() {
        core.popup.genericBlueprintLoader('builder');
    },
    maker: function() {
        core.popup.genericBlueprintLoader('maker');
    },
    fighter: function() {
        core.popup.genericBlueprintLoader('fighter');
    }
};