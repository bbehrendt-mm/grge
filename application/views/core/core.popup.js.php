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

    genericFilterLoader: function(callback, typeFilterData) {
        var popup = core.popup.spawn(500,300);

        var frame = $('<div />').addClass('row').appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 0,
                bottom: 26,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = $('<div />').addClass('right').css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 26,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn small').text(<?=__j('Abbrechen');?>).appendTo(bottom).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn small').text(<?=__j('Anwenden');?>).appendTo(bottom).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters;
        frame.append(
            $('<div />').addClass('row').append(
                typefilters = $('<div />').addClass('cell rw-6')
            ).append(
                classfilters = $('<div />').addClass('cell rw-6')
            )
        );

        $.each(typeFilterData, function(id, obj) {
            var chk;
            typefilters.append(
                $('<div />').append(
                    $('<label />').text(obj.name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('tid', id).prop('checked', obj.active).click(function() {
                            typeFilterData[id].active = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
        })
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
            ).data('type-filters', {
                    'impossible': {active: true, name: <?=__j('Unmögliche Projekte')?>},
                    'locked': {active: true, name: <?=__j('Gesperrte Projekte')?>},
                    'possible': {active: true, name: <?=__j('Vorbereitete Projekte')?>},
                    'ready': {active: true, name: <?=__j('Mögliche Projekte')?>},
                    'done': {active: true, name: <?=__j('Abgeschlossene Projekte')?>}
            }).on('filter', function() {
                    var typefilters = $(this).data('type-filters');
                    if (typefilters.impossible.active)  $(this).find('.blueprint.red').parent().show();
                    else                                $(this).find('.blueprint.red').parent().hide();

                    if (typefilters.locked.active)      $(this).find('.blueprint.plain').parent().show();
                    else                                $(this).find('.blueprint.plain').parent().hide();

                    if (typefilters.possible.active)    $(this).find('.blueprint.green').parent().show();
                    else                                $(this).find('.blueprint.green').parent().hide();

                    if (typefilters.ready.active)       $(this).find('.blueprint.green.active').parent().show();
                    else                                $(this).find('.blueprint.green.active').parent().hide();

                    if (typefilters.done.active)        $(this).find('.blueprint.blue').parent().show();
                    else                                $(this).find('.blueprint.blue').parent().hide();
            });

            $('<div />').addClass('row').append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text('Angezeigte Projekte filtern...').click(function() {
                core.popup.genericFilterLoader(function(a) {
                    frame.data('type-filters', a).trigger('filter');
                }, JSON.parse(JSON.stringify(frame.data('type-filters'))));
            }));
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
                    core.command('location/' + type, {/*build: k*/}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        frame.trigger('filter');
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