core.popup.map = function() {

    var win_d;
    if (screen.width < 804 || screen.height < 604)
        win_d = [screen.width, screen.height];
    else win_d = [804,604];

    var popup = core.popup.spawn(win_d[0], win_d[1]).css('overflow','hidden');


    popup.attr('tabindex', 1).append(core.snippets.wait()).focus();

    var init = function(data) {

        var overlay, help, list, list_inner, dbl1, dbl2, control;

        popup.empty()
            .append($('<canvas />').attr({id: 'gamemap', height: win_d[1] - 4, width: win_d[0] - 4}))
            .append(
                overlay = $('<div />').addClass('map panel bottom hide no-interaction').addClass(win_d[0] < 804 ? 'wide' : '')
            ).append(
                control = $('<div />').addClass('map panel bottom').addClass(!game.touch() ? 'hide' : '' ).addClass(win_d[0] < 804 ? 'wide' : '')
            ).append(
                help = $('<div />').addClass('map panel left hide no-interaction').addClass(win_d[0] < 804 ? 'wide' : '')
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
            ).append(game.touch() || win_d[0] < 640 ? null : $('<div />').addClass('map panel left-top center').addClass(win_d[0] < 804 ? 'wide' : '')
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
                list = $('<div />').addClass('map panel right manual-color hide').addClass(win_d[0] < 804 ? 'wide' : '').css('overflow-y','auto').append(NF.row().append(list_inner = NF.cell(true, 12)))
            ).append($('<div />').addClass('map panel right-top center').addClass(win_d[0] < 804 ? 'wide' : '')
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

        var map = new Gamemap('gamemap', data, win_d[0] - 4, win_d[1] - 4);
        control.append(NF.row('center')
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {popup.trigger('unpop');}).append(NF.fa('close')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.zoom( 0);}).append(NF.fa('crosshairs')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.zoom( 1);}).append(NF.fa('search-plus')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.zoom(-1);}).append(NF.fa('search-minus')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll(0, 48);}).append(NF.fa('arrow-up')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll(0,-48);}).append(NF.fa('arrow-down')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll( 48,0);}).append(NF.fa('arrow-left')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll(-48,0);}).append(NF.fa('arrow-right')))
        );

        $.each(data.locations, function(id, location) {
            if (id == data.current) return;
            list_inner.append(NF.row().append(NF.cell(false, 12, 0, 'hotbox').addClass(location.energy > data.radius ? 'disabled' : '').on('mouseover', function() {map.hover(id);}).on('mouseout', function() {map.unhover(id);}).on('click', function() {map.handler(id, 'click')})
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
            ).attr('data-sort',true).attr('data-sort-type', location.icon).attr('data-sort-cron', id).attr('data-sort-dist', location.distance).attr('data-sort-name', location.name))
        });

        list_inner.prepend(NF.row('center').append(NF.cell(true, 12).append(
            NF.select({'dist': <?=__j('Entfernung')?>, 'name': <?=__j('Name')?>, 'type': <?=__j('Typ')?>, 'cron': <?=__j('Chronologisch')?>}, 'dist')
        ))).find('select').on('change', function() {
            var by = $(this).val();
            var items = list_inner.children('[data-sort]').sort(function(a, b) {
                var a_val = $(a).attr('data-sort-' + by);
                var b_val = $(b).attr('data-sort-' + by);

                if (!isNaN(a_val)) a_val = parseFloat(a_val);
                if (!isNaN(b_val)) b_val = parseFloat(b_val);

                return (a_val < b_val) ? -1 : (a_val > b_val) ? 1 : 0;
            });
            list_inner.append(items);

        }).trigger('change').selectric();

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
                    var escortables = false;
                    if (core.last.players && core.last.players.others)
                        $.each(core.last.players.others, function(id, player) {
                            if (player.local && (player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_MOVE?>]))
                                escortables = true;
                        });

                    if ((data.read_only && !data.locations[id].skip_ro) || (data.locations[id].energy > data.radius && !escortables) || id == data.current) return;

                    var only_remote = data.locations[id].energy > data.radius;

                    if (data.locations[id].zombies && !confirm(game.i18n(<?=__j('Dieser Ort wird von :zombies Zombies belagert. Wenn du diesen Ort betrittst, wirst du kämpfen müssen. Weiter?')?>, {':zombies': data.locations[id].zombies}))) return;

                    var route_zombies = [];
                    $.each(data.locations[id].route, function(rkey, rval) {
                        if (rval == data.locations[id].id || rval == data.current) return;
                        if (data.locations[rval].zombies > 0)
                            route_zombies.push(data.locations[rval].name);
                    });
                    if (route_zombies.length && !confirm(game.i18n(<?=__j('Auf dem Weg zu diesem Ort befinden sich Zombies (:locations). Du wirst gegen sie kämpfen müssen, wenn du dorthin möchtest. Weiter?')?>,{':locations': route_zombies.join(', ')}))) return;



                    if (game.storage.get('settings','travel_confirm') != 'auto' || escortables || game.touch()) {
                        var esc_popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text(data.locations[id].name));

                        if (game.touch())
                            esc_popup.append(NF.row('center').append(
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
                                );

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


                        if (only_remote)
                            esc_popup.append(
                                NF.row().append(NF.cell(true, 12, 0, 'center b text-red').text(<?=__j('Dieser Ort ist zu weit für dich entfernt!')?>))
                            );

                        if (check_row.children().length) {
                            var bhav;

                            check_row
                                .prepend($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Wer soll alles mitkommen?')?>)))
                                .append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Und wie siehts mit dir aus?')?>)))
                                .append($('<div />').addClass('cell rw-12 padded').append(
                                    bhav = $('<select />')
                                        .append($('<option />').val('2').prop('disabled', only_remote).text(<?=__j('Mitgehen und helfen')?>))
                                        .append($('<option />').val('1').prop('disabled', only_remote).text(<?=__j('Nur mitgehen')?>))
                                        .append($('<option />').val('0').text(<?=__j('Die Stellung halten')?>))
                                        .val(only_remote ? '0' : '1')
                                ));

                            bhav.selectric();
                            check_row.find(':checkbox').customRadioCheck();

                        } else title.text(<?=__j('Bist du sicher, dass du diesen Ort betreten möchtest? Er ist weit weg, und riecht auch bestimmt nicht sehr gut...')?>);

                        var confirm_btn;
                        esc_popup.append(NF.row()
                            .append($('<div />').addClass('cell rw-8 rw-sm-12 padded').append(
                                confirm_btn = $('<div />').addClass('btn').toggleClass('disabled', only_remote).text(<?=__j('Los gehts!')?>).click(function() {

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

                        $.each(check_row.find(':checkbox'), function() {
                                $(this).click(function() {
                                    var ok = false;
                                    $.each(check_row.find(':checkbox:checked'), function() {
                                        ok = true;
                                    });
                                    confirm_btn.toggleClass('disabled', only_remote && !ok);

                                })
                            });

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
                    if (!game.touch())
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
};