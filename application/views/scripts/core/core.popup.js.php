core.popup = {
    spawn: function(dx,dy) {

        var exp = function(w) {
            if (typeof w == 'undefined')
                w = 'auto';
            if (typeof w !== 'object')
                w = {desktop: w, lg: w, md: w, sm: w};

            w.desktop = w.desktop || w.lg || w.md || w.sm;
            w.lg = w.lg || w.desktop || w.md || w.sm;
            w.md = w.md || w.lg || w.sm || w.desktop;
            w.sm = w.sm || w.md || w.lg || w.desktop;
            return w;
        };

        var gt = function(w, ref) {
            var decoded;
            if (typeof w !== 'object') decoded = w;
            else if (!game.mobile) decoded = w.desktop;
            else decoded = w[game.mobile];

            if (typeof decoded == "string") {

                if (decoded == 'auto') {}
                else if (decoded[decoded.length-1] == '%')
                    decoded = ref * (parseFloat(decoded.substr(0,decoded.length-1)/100));
                else decoded = parseFloat(decoded);

            }

            return decoded;
        };

        dx = exp(dx);
        dy = exp(dy);

        var wrapper = $('<div />').addClass('popup-wrapper').appendTo($('body').css('overflow','hidden'));

        var popup = $('<div />').addClass('popup')
            .on('reposition', function() {

                var ldx = gt(dx, $(window).width());
                var ldy = gt(dy, window.innerHeight);

                $(this).css({
                    top: ldy == 'auto' ? 30 : (((ldy + 60) > window.innerHeight) ? 0 : 60),
                    left: ldx == 'auto' ? 0 : Math.max(0,$(window).width()/2 - ldx/2),
                    height: ldy,
                    width: ldx
                });
            }).trigger('reposition').appendTo(wrapper);

        var z = game.render.html.modal.blend(function() {
            popup.trigger('close').addClass('disabled').css({
                '-webkit-filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)',
                'filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: game.s.quality() > 1 ? 'scale(1.5)' : 'scale(1)'
            }, 400, 'swing', function() {
                $(this).parent().remove();
                if (!$('.popup').length)
                    $('body').css('overflow','auto');
            });
        }, true, true);

        wrapper.css('z-index',z+1);

        popup.on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).css({
            opacity: 0,
            transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)',
            'transition': 'filter 0.4s ease, -webkit-filter 0.4s ease',
            '-webkit-filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)',
            'filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)'
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
        var popup = core.popup.spawn({desktop: 500, md: '100%'},{desktop: 300, md: '100%'});

        var frame = NF.row().appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 0,
                bottom: 36,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = NF.row().css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 36,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn').text(<?=__j('Abbrechen');?>).appendTo($('<div />').addClass('cell rw-4 rw-sm-6').appendTo(bottom)).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn').text(<?=__j('Anwenden');?>).appendTo($('<div />').addClass('cell ro-4 rw-4 rw-sm-6 ro-sm-0').appendTo(bottom)).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters, class_cell;
        frame.append(
            NF.row().append(
                $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text(<?=__j('Status')?>)).append(typefilters = NF.row()))
            ).append(
                class_cell = $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text(<?=__j('Kategorie')?>)).append(classfilters = NF.row()))
            )
        );

        $.each(typeFilterData, function(id, obj) {
            if (id == 'categories') return;
            var chk;
            typefilters.append(
                $('<div />').addClass('cell rw-6 rw-sm-12').append(
                    $('<label />').text(obj.name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('tid', id).prop('checked', obj.active).click(function() {
                            typeFilterData[id].active = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
        });

        var has_cats = false;
        $.each(typeFilterData.categories, function(name, active) {
            var chk;
            classfilters.append(
                $('<div />').addClass('cell rw-4 rw-sm-6').append(
                    $('<label />').text(name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('cid', name).prop('checked', active).click(function() {
                            typeFilterData.categories[name] = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
            has_cats = true;
        });

        if (!has_cats)
            class_cell.hide();
        else class_cell.show();
    },

    genericBlueprintLoader: function(type, popup, data, frame, filters, close) {
        if (!popup) popup = core.popup.spawn({desktop: 700, lg: '100%'},{desktop: 450, lg: '100%'});

        if (!frame || !filters || !close) {
            popup.empty();

            frame = NF.row().appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: 24,
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            ).data('type-filters', {
                    'impossible': {active: false, name: <?=__j('Unmögliche Projekte')?>},
                    'locked': {active: true, name: <?=__j('Gesperrte Projekte')?>},
                    'possible': {active: true, name: <?=__j('Vorbereitete Projekte')?>},
                    'ready': {active: true, name: <?=__j('Mögliche Projekte')?>},
                    'done': {active: true, name: <?=__j('Abgeschlossene Projekte')?>},
                    'categories': {}
            }).on('filter', function() {
                    var typefilters = $(this).data('type-filters');

                    $(this).find('.blueprint').parent().hide();

                    var alias = $(this);
                    var all_enabled = true;
                    $.each(typefilters.categories, function(name, active) {
                        if (active)
                            alias.find('.blueprint[data-cats*="|' + name + '|"]').parent().show();
                        else all_enabled = false;
                    });
                    if (all_enabled) alias.find('.blueprint[data-cats="||"]').parent().show();

                    if (!typefilters.impossible.active)  $(this).find('.blueprint.red').parent().hide();
                    if (!typefilters.locked.active)      $(this).find('.blueprint.plain').parent().hide();
                    if (!typefilters.possible.active)    $(this).find('.blueprint.green').parent().hide();
                    if (!typefilters.ready.active)       $(this).find('.blueprint.green.active').parent().hide();
                    if (!typefilters.done.active)        $(this).find('.blueprint.blue').parent().hide();

                    $(this).find('.blueprint-group').each(function() {
                        $(this).show();
                        if (!$(this).find('.blueprint:visible').length) $(this).hide();
                    });
            });

            NF.row().append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text(<?=__j('Angezeigte Projekte filtern...')?>).click(function() {
                core.popup.genericFilterLoader(function(a) {
                    frame.data('type-filters', a).trigger('filter');
                }, JSON.parse(JSON.stringify(frame.data('type-filters'))));
            }));
            close.append($('<div />').addClass('btn small').append($('<i/>').addClass('fa fa-times')).click(function() {
                popup.trigger('unpop');
            }));
        }

        var build_func = function(bdata) {
            frame.empty();
            var categories = {}; var cat_count = 0;
            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;

                if (!$.objToArray(v.categories).length) v.categories = [<?=__j('Sonstiges')?>];
                $.each(v.categories, function(i,cat) {
                    if (!categories[cat]) cat_count++;
                    categories[cat] = true;
                });
            });

            if (cat_count > 1)
                $.each(categories, function(n,t) {
                    frame.append($('<div />').attr('data-group-cat',n).addClass('blueprint-group').append($('<b />').addClass('header').text(n)).append($('<div />').addClass('blueprint-group-target row')));
                });

            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;
                if (!$.objToArray(v.categories).length) v.categories = [<?=__j('Sonstiges')?>];
                var targets = $();
                if (cat_count > 1) $.each(v.categories, function(i,cat) {targets = targets.add(frame.find('div[data-group-cat="' + cat + '"]').find('.blueprint-group-target'));});
                else targets = frame;

                targets.append($('<div />').addClass('cell padded rw-4 rw-md-6').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
                    if (v.confirm && !window.confirm(game.i18n(v.confirm, {':name': v.name})))
                        return;

                    var prev_scroll = $('.popup').find('>*:first-child').scrollTop();
                    popup.addClass('disabled');
                    core.command('location/' + type, {build: k}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        frame.trigger('filter');
                        $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        popup.off('close').on('close', function() {
                            setTimeout(function() {
                                core.command();
                            }, 100);
                        })
                    }, true)
                })));
            });

            var tf = frame.data('type-filters');
            $.each(categories, function(name, t) {
                tf.categories[name] = typeof tf.categories[name] !== "undefined" ? tf.categories[name] : true;
            });
            frame.data('type-filters', tf).trigger('filter');
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
    },

    map: function() {
        var popup = core.popup.spawn(804, 604).css('overflow','hidden');

        popup.attr('tabindex', 1).append(core.snippets.wait()).focus();

        var init = function(data) {

            var overlay, help, list, list_inner, dbl1, dbl2;

            popup.empty()
                .append($('<canvas />').attr({id: 'gamemap', height: 600, width: 800}))
                .append(
                    overlay = $('<div />').addClass('map panel bottom hide no-interaction')
                ).append(
                    help = $('<div />').addClass('map panel left hide no-interaction')
                        .append(NF.row()
                            .append(NF.cell(true, 12, 0, 'center b').text(<?=__j('Maus')?>))
                            .append(NF.cell(true, 12, 0).append(NF.n('ul')
                                .append(NF.n('li', '', <?=__j('::b::Linke Maustaste::/b:: halten und ::b::Maus bewegen::/b::, um den Kartenausschnitt zu verschieben.')?>, true))
                                .append(NF.n('li', '', <?=__j('::b::Mausrad::/b:: drehen, um zu zoomen.')?>, true))
                                .append(NF.n('li', '', <?=__j('::b::Mittlere Maustaste::/b:: drücken, um die Karte zurückzusetzen.')?>, true))
                            ))
                            .append(NF.cell(true, 12, 0, 'center b').text(<?=__j('Tastatur')?>))
                            .append(NF.cell(true, 12, 0).append(NF.n('ul')
                                .append(NF.n('li', '', <?=__j('::b::Pfeiltasten::/b:: benutzen, um den Kartenausschnitt zu verschieben.')?>, true))
                                .append(NF.n('li', '', <?=__j('::b::+::/b:: und ::b::-::/b::-Tasten verwenden, um zu zoomen.')?>, true))
                                .append(NF.n('li', '', <?=__j('::b::0::/b:: oder ::b::R::/b::-Tasten verwenden, um die Karte zurückzusetzen.')?>, true))
                            ))
                        )
                ).append($('<div />').addClass('map panel left-top center')
                    .append(NF.n('span', 'b', <?=__j('Steuerung')?>))
                    .append(dbl1 = NF.fa('angle-double-right').addClass('pointer').css('float', 'right'))
                    .click(function() {
                        if (help.hasClass('hide')) {
                            help.removeClass('hide');
                            dbl1.removeClass('fa-angle-double-right').addClass('fa-angle-double-left')
                        } else {
                            help.addClass('hide');
                            dbl1.addClass('fa-angle-double-right').removeClass('fa-angle-double-left')
                        }
                    })
                ).append(
                    list = $('<div />').addClass('map panel right manual-color hide').css('overflow-y','auto').append(NF.row().append(list_inner = NF.cell(true, 12)))
                ).append($('<div />').addClass('map panel right-top center')
                    .append(NF.n('span', 'b', <?=__j('Orte')?>))
                    .append(dbl2 = NF.fa('angle-double-left').addClass('pointer').css('float', 'left'))
                    .click(function() {
                        if (list.hasClass('hide')) {
                            list.removeClass('hide');
                            dbl2.removeClass('fa-angle-double-left').addClass('fa-angle-double-right')
                        } else {
                            list.addClass('hide');
                            dbl2.addClass('fa-angle-double-left').removeClass('fa-angle-double-right')
                        }
                    })
                );

            var map = new Gamemap('gamemap', data);

            $.each(data.locations, function(id, location) {
                if (id == data.current) return;
                list_inner.append(NF.row().append(NF.cell(false, 12, 0, 'hotbox').on('mouseover', function() {map.hover(id);}).on('mouseout', function() {map.unhover(id);}).on('click', function() {map.handler(id, 'click')})
                    .append(NF.row('center').append(NF.cell(true, 12).append(NF.n('b', '', location.name)).append(NF.img('media/icons/places/' + location.icon).css('float','left'))))
                    .append(NF.row('center').append(
                        NF.cell(false, 3)
                            .append(NF.img('media/icons/distance.gif'))
                            .append(NF.n('span','',location.distance))
                        ).append(
                        NF.cell(false, 3)
                            .append(NF.img('media/icons/status_energy.gif'))
                            .append(NF.n('span','',location.energy))
                        ).append(
                        NF.cell(false, 3)
                            .append(NF.img('media/icons/zombie.gif'))
                            .append(NF.n('span','',location.zombies))
                        ).append(
                        NF.cell(false, 3)
                            .append(NF.img('media/icons/status_weight.gif'))
                            .append(NF.n('span','',location.weight === null ? 0 : location.weight))
                        )
                    )
                ))
            });

            popup.on('close', function() {
                map.end();
            }).on('mousewheel', function(event) {
                map.zoom(event.deltaY);
            }).on('mousedown', function(event) {
                if (event.which == 2) map.zoom(0);
            }).on('keydown', function(event) {
                switch (event.key) {
                    case "+": map.zoom(1); return;
                    case "-": map.zoom(-1); return;
                    case "0":case "r": map.zoom(0); return;
                }
                switch (event.which) {
                    case 37: map.scroll(-24,0); event.preventDefault(); return; //LEFT
                    case 38: map.scroll(0,-24); event.preventDefault(); return; //UP
                    case 39: map.scroll(24,0); event.preventDefault(); return; //RIGHT
                    case 40: map.scroll(0, 24); event.preventDefault(); return; //DOWN
                }
            });

            map.load();
            map.setHandler(function(id, event) {
                switch (event) {
                    case 'click':
                        if ((data.read_only && !data.locations[id].skip_ro) || data.locations[id].energy > data.radius || id == data.current) return;

                        if (data.locations[id].zombies && !confirm(game.i18n(<?=__j('Dieser Ort wird von :zombies Zombies belagert. Wenn du diesen Ort betrittst, wirst du kämpfen müssen. Weiter?')?>, {':zombies': data.locations[id].zombies}))) return;

                        var route_zombies = [];
                        $.each(data.locations[id].route, function(rkey, rval) {
                            if (rval == data.locations[id].id || rval == data.current) return;
                            if (data.locations[rval].zombies > 0)
                                route_zombies.push(data.locations[rval].name);
                        });
                        if (route_zombies.length && !confirm(game.i18n(<?=__j('Auf dem Weg zu diesem Ort befinden sich Zombies (:locations). Du wirst gegen sie kämpfen müssen, wenn du dorthin möchtest. Weiter?')?>,{':locations': route_zombies.join(', ')}))) return;

                        var escortables = false;
                        if (core.last.players && core.last.players.others)
                            $.each(core.last.players.others, function(id, player) {
                                if (player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_MOVE?>])
                                    escortables = true;
                            });

                        if (game.storage.get('settings','travel_confirm') != 'auto' || escortables) {
                            var esc_popup = core.popup.spawn({desktop: 400, sm: '100%'});

                            var title;
                            esc_popup.append($('<h2 />').addClass('center').text(data.locations[id].name));

                            esc_popup.append(
                                NF.row().append(title = NF.cell(true, 12).text(<?=__j('Wenn du dich alleine fürchtest, kannst du andere Spieler bitten, dich zu begleiten. Oder noch besser, schick sie am besten direkt vor, nicht dass noch jemand (z.B. du) verletzt wird!')?>))
                            );

                            var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                            if (escortables && core.last.players && core.last.players.others)
                                $.each(core.last.players.others, function(id, player) {
                                    if (player.allow === true ||player.allow[<?=Interface_Plentity::IC_ALLOW_MOVE?>])
                                        check_row.append($('<div />').addClass('cell rw-6 padded').append(
                                            $('<label />').text(player.name).prepend($('<input />').attr('type','checkbox').attr('data-id', player.id))
                                        ))
                                });


                            if (check_row.children().length) {
                                var bhav;

                                check_row
                                    .prepend($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Wer soll alles mitkommen?')?>)))
                                    .append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Und wie siehts mit dir aus?')?>)))
                                    .append($('<div />').addClass('cell rw-12 padded').append(
                                        bhav = $('<select />')
                                            .append($('<option />').val('2').text(<?=__j('Mitgehen und helfen')?>))
                                            .append($('<option />').val('1').text(<?=__j('Nur mitgehen')?>))
                                            .append($('<option />').val('0').text(<?=__j('Die Stellung halten')?>))
                                            .val('1')
                                    ));

                                bhav.selectric();
                                check_row.find(':checkbox').customRadioCheck();

                            } else title.text(<?=__j('Bist du sicher, dass du diesen Ort betreten möchtest? Er ist weit weg, und riecht auch bestimmt nicht sehr gut...')?>);

                            esc_popup.append(NF.row()
                                .append($('<div />').addClass('cell rw-8 rw-sm-12 padded').append(
                                    $('<div />').addClass('btn').text(<?=__j('Los gehts!')?>).click(function() {

                                        var cfg = {to: id, follow: 1};
                                        if (check_row.children().length) {
                                            cfg.follow = parseInt(bhav.val()) > 0 ? 1 : 0;
                                            cfg.support = parseInt(bhav.val()) == 2 ? 1 : 0;
                                            cfg.co = [];
                                            $.each(check_row.find(':checkbox:checked'), function() {
                                                cfg.co.push($(this).attr('data-id'))
                                            })
                                        }

                                        esc_popup.trigger('unpop');
                                        popup.addClass('disabled');
                                        core.command('map/go', cfg, true, function(data) {
                                            popup.removeClass('disabled');
                                            if (data.success) {
                                                popup.trigger('unpop');
                                                core.command();
                                            }
                                        });
                                    })))
                                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded').append(
                                    $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(function() {
                                        esc_popup.trigger('unpop');
                                    })))
                            );
                        } else {
                            popup.addClass('disabled');
                            core.command('map/go', {to: id, follow: 1}, true, function(data) {
                                popup.removeClass('disabled');
                                if (data.success) {
                                    popup.trigger('unpop');
                                    core.command();
                                }
                            });
                        }

                        break;
                    case 'mouseover':
                        overlay.empty().append(
                            $('<h3 />').text(data.locations[id].name)
                        ).append(
                            NF.row('center').append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/distance.gif'))
                                    .append(NF.n('span','',data.locations[id].distance))
                            ).append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/status_energy.gif'))
                                    .append(NF.n('span','',data.locations[id].energy))
                            ).append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/zombie.gif'))
                                    .append(NF.n('span','',data.locations[id].zombies))
                            ).append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/status_weight.gif'))
                                    .append(NF.n('span','',data.locations[id].weight === null ? 0 : data.locations[id].weight))
                            )
                        ).removeClass('hide');
                        break;
                    case 'mouseout':
                        overlay.addClass('hide');
                        break;
                }
            });
            map.begin();

        };


        core.command('map/data', {}, true, function(data) {

            if (typeof Gamemap === "undefined")

                $.ajax({
                    url: 'web/map/?l=' + game.lang(),
                    dataType: "script",
                    headers: { 'X-Skip-ETag': game.storage.get('update','force_next_update',false) ? '1' : '0' },
                    cache: true,
                    success: function() {init(data)}
                }).fail(function( jqxhr, settings, exception ) {
                    popup.empty().append($('<div />').addClass('center').text(exception.message));
                });
            else init(data);

        });
    }
};