core.popup.map = function() {

    var win_d;
    if (screen.width < 804 || screen.height < 604)
        win_d = [screen.width, screen.height];
    else win_d = [804,604];

    var popup = core.popup.spawn(win_d[0], win_d[1]).css('overflow','hidden');


    popup.attr('tabindex', 1).append(core.snippets.wait()).focus();

    var init = function(data) {

        if (data.current != core.last.location.id)
            return core.command();

        var overlay, help, list, list_inner, dbl1, dbl2, control, tagtog;

        var markers = {
            0 : <?=__j('Keine Notiz');?>,
            1 : <?=__j('Achtung');?>, 2: <?=__j('Rohstoffe');?>, 3: <?=__j('Gegenstände');?>, 4: <?=__j('Wichtige Gegenstände');?>,
            5 : <?=__j('Leergesucht');?>, 6: <?=__j('Gesichert');?>, 7: <?=__j('Unerkundet');?>, 8: <?=__j('Gefährlich');?>,
            9 : <?=__j('Sehr gefährlich');?>, 10: <?=__j('Übernachtung');?>, 11: <?=__j('Erkundet');?>, 12: <?=__j('Schwere Gegenstände');?>,
            13: <?=__j('Nicht betreten!');?>, 14: <?=__j('Gesichertes Versteck');?>, 15: <?=__j('Brenzlige Situation');?>, 16: <?=__j('HÜHNCHEN!');?>
        };

        popup.empty()
            .append($('<canvas />').contextmenu(function() {return false;}).attr({id: 'gamemap', height: win_d[1] - 4, width: win_d[0] - 4}))
            .append(
                overlay = $('<div />').addClass('map panel bottom hide no-interaction').addClass(win_d[0] < 804 ? 'wide' : '')
            ).append(
                tagtog = $('<div />').addClass('map panel top pointer').addClass(win_d[0] < 804 ? 'hide' : '').text(<?=__j('Symbole umschalten')?>)
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
                            .append(NF.n('li', '', <?=__j('::b::ESC::/b:: oder ::b::X::/b::-Tasten verwenden, um die Karte zu schließen.')?>, true))
                            .append(NF.n('li', '', <?=__j('::b::T::/b::-Taste verwenden, um Symbole umzuschalten.')?>, true))
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

        tagtog.click(function() {map.toggleTagMode()});

        $.each(data.locations, function(id, location) {
            if (id == data.current) return;
            list_inner.append(NF.row().append(NF.cell(false, 12, 0, 'hotbox').addClass(location.energy > data.radius ? 'disabled' : '').on('mouseover', function() {map.hover(id);}).on('mouseout', function() {map.unhover(id);}).on('click', function() {map.handler(id, 'click')})
                .append(NF.row('center').append(NF.cell(true, 12).append(NF.n('b', '', location.name)).append(NF.img('media/icons/places/' + location.icon).css('float','left'))))
                .append(
                    location.note.tag == 0 ? null : NF.row('center').append(
                        NF.cell(false, 12)
                            .append(NF.img('media/icons/places/tags/tag_' + location.note.tag + '.gif'))
                            .append(NF.n('span','', location.note.text ? location.note.text : markers[location.note.tag]))
                        )
                )
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
            ).attr('data-sort',true).attr('data-sort-tag', -location.note.tag).attr('data-sort-type', location.icon).attr('data-sort-cron', id).attr('data-sort-dist', location.distance).attr('data-sort-name', location.name))
        });

        list_inner.prepend(NF.row('center').append(NF.cell(true, 12).append(
            NF.select({'dist': <?=__j('Entfernung')?>, 'tag': <?=__j('Markierung')?>, 'name': <?=__j('Name')?>, 'type': <?=__j('Typ')?>, 'cron': <?=__j('Chronologisch')?>}, 'dist')
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
                case 't': map.toggleTagMode(); return;
                case "x": popup.trigger('unpop'); return;
                case "+": map.zoom(1); return;
                case "-": map.zoom(-1); return;
                case "0":case "r": map.zoom(0); return;
            }
            switch (event.which) {
                case 27: popup.trigger('unpop'); return;                    //ESC
                case 37: map.scroll(-24,0); event.preventDefault(); return; //LEFT
                case 38: map.scroll(0,-24); event.preventDefault(); return; //UP
                case 39: map.scroll(24,0); event.preventDefault(); return; //RIGHT
                case 40: map.scroll(0, 24); event.preventDefault(); return; //DOWN
            }
        });

        map.load();
        map.setHandler(function(id, event) {
            switch (event) {
                case 'click': case 'altclick':

                    var escortables = false;
                    if (data.players && data.players.others)
                        $.each(data.players.others, function(id, player) {
                            if (player.local && (player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_MOVE?>]))
                                escortables = true;
                        });

                    var no_go = false;
                    var same_location = id == data.current;

                    if ((data.read_only && !data.locations[id].skip_ro) || (data.locations[id].energy > data.radius && !escortables)) {
                        if (event == 'altclick')
                            no_go = true;
                        else return;
                    }

                    var only_remote = data.locations[id].energy > data.radius;

                    if (!no_go)
                        if (data.locations[id].zombies && !confirm(game.i18n(<?=__j('Dieser Ort wird von :zombies Zombies belagert. Wenn du diesen Ort betrittst, wirst du kämpfen müssen. Weiter?')?>, {':zombies': data.locations[id].zombies}))) return;

                    var route_zombies = [];
                    $.each(data.locations[id].route, function(rkey, rval) {
                        if (rval == data.locations[id].id || rval == data.current) return;
                        if (data.locations[rval].zombies > 0)
                            route_zombies.push(data.locations[rval].name);
                    });
                    if (route_zombies.length && !confirm(game.i18n(<?=__j('Auf dem Weg zu diesem Ort befinden sich Zombies (:locations). Du wirst gegen sie kämpfen müssen, wenn du dorthin möchtest. Weiter?')?>,{':locations': route_zombies.join(', ')}))) return;

                    if (game.storage.get('settings','travel_confirm') != 'auto' || escortables || game.touch() || event == 'altclick') {
                        var esc_popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text(data.locations[id].name));

                        if (game.touch() && !same_location)
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

                        if (!same_location) {
                            esc_popup.append(
                                NF.row().append(title = NF.cell(true, 12).text(<?=__j('Wenn du dich alleine fürchtest, kannst du andere Spieler bitten, dich zu begleiten. Oder noch besser, schick sie am besten direkt vor, nicht dass noch jemand (z.B. du) verletzt wird!')?>))
                            );

                            var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                            if (escortables && data.players && data.players.others)
                                $.each(data.players.others, function(id, player) {
                                    if (player.local && (player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_MOVE?>]))
                                        check_row.append($('<div />').addClass('cell rw-6 padded').append(
                                            $('<label />').text(player.name).prepend($('<input />').attr('type','checkbox').attr('data-id', player.id))
                                        ))
                                });

                            if (only_remote)
                                esc_popup.append(
                                    NF.row().append(NF.cell(true, 12, 0, 'center b text-red').text(<?=__j('Dieser Ort ist zu weit für dich entfernt!')?>))
                                );
                            else if (no_go)
                                esc_popup.append(
                                    NF.row().append(NF.cell(true, 12, 0, 'center b text-red').text(<?=__j('Du kannst dich momentan nicht bewegen.')?>))
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
                                            .append($('<option />').val('0').prop('disabled', no_go).text(<?=__j('Die Stellung halten')?>))
                                            .val(only_remote ? '0' : '1')
                                    ));

                                bhav.selectric();
                                check_row.find(':checkbox').customRadioCheck();

                            } else title.text(<?=__j('Bist du sicher, dass du diesen Ort betreten möchtest? Er ist weit weg, und riecht auch bestimmt nicht sehr gut...')?>);
                        }

                        var marker_tx, no_marker_tx, marker;
                        var open_editor = function() {
                            marker_tx.hide(); no_marker_tx.hide();
                            marker.show();
                        };

                        no_marker_tx = NF.row()
                            .append(NF.cell(true, 12, 0, 'pointer').click(open_editor).text(' ' + <?=__j('Kartenmarkierung hinzufügen')?>).prepend(NF.fa('plus')))
                            .appendTo(esc_popup);
                        marker_tx = NF.row()
                            .append(NF.cell(true, 8).text(data.locations[id].note.text ? data.locations[id].note.text : markers[data.locations[id].note.tag]).prepend(NF.img('media/icons/places/tags/tag_' + data.locations[id].note.tag + '.gif')))
                            .append(NF.cell(true, 4, 0, 'pointer').click(open_editor).text(' ' + <?=__j('Bearbeiten')?>).prepend(NF.fa('pencil')))
                            .appendTo(esc_popup);

                        if (data.locations[id].note.tag == 0)
                            marker_tx.hide();
                        else no_marker_tx.hide();

                        marker = NF.n('div', 'flatbox').appendTo(NF.cell(true, 12).appendTo(NF.row().appendTo(esc_popup)));
                        var selector, noteblock, tagpic, savebtn;
                        marker.append(NF.row()
                            .append(NF.cell(true, 2, 0, 'right').append(NF.n('div').css('padding-top', 10).append(tagpic = NF.img('media/icons/places/tags/tag_0.gif'))))
                            .append(NF.cell(true, 10).append(selector = NF.select(markers, data.locations[id].note.tag)))
                            .append(NF.cell(true, 10).append(noteblock = NF.input('text', data.locations[id].note.text).attr('maxlength', 32).attr('placeholder',<?=__j('Zusätzliche Informationen (optional)')?>)))
                            .append(NF.cell(true, 2).append(savebtn = NF.n('button','btn disabled').append(NF.fa('save'))))
                        ).hide();
                        noteblock.keyup(function() {
                            if ($(this).val() != data.locations[id].note.text)
                                savebtn.removeClass('disabled');
                            else savebtn.addClass('disabled');
                        });
                        selector.change(function() {
                            if ($(this).val() == 0)
                                noteblock.text('').addClass('disabled');
                            else noteblock.removeClass('disabled');

                            if ($(this).val() != data.locations[id].note.tag)
                                savebtn.removeClass('disabled');
                            else savebtn.addClass('disabled');
                            tagpic.attr('src', 'media/icons/places/tags/tag_' + $(this).val() + '.gif')
                        }).change().selectric({
                            optionsItemBuilder: function(a) {
                                return a.value == 0 ? '[' + a.text + ']' : '<table><tr><td><img src="media/icons/places/tags/tag_' + a.value + '.gif" alt="" /></td><td style="line-height: 10px;">' +  a.text + '</td></tr></table>';
                            }
                        });
                        savebtn.click(function() {
                            $(savebtn).addClass('disabled');
                            var new_tx = noteblock.val();
                            var new_tg = selector.val();
                            core.command('map/tag', {id: id, tag: new_tg, text: new_tx}, true, function(retval) {
                                if (retval.success) {
                                    game.render.html.notify('success', <?=__j('Die Kartenmarkierung wurde aktualisiert.')?>);
                                    data.locations[id].note.text = new_tx;
                                    data.locations[id].note.tag = new_tg;
                                    map.addIconTag(id, new_tg, game.touch());
                                }

                            });
                        });

                        var confirm_btn;
                        esc_popup.append(NF.row()
                            .append($('<div />').addClass('cell rw-8 rw-sm-12 padded').append(
                                confirm_btn = same_location ? null : $('<div />').addClass('btn').toggleClass('disabled', no_go).text(<?=__j('Los gehts!')?>).click(function() {

                                    if (marker.is(':visible') && !savebtn.hasClass('disabled') && !confirm(<?=__j('Der Kartenmarker wurde noch nicht gespeichert. Bist du sicher, dass du den ausgewählten Ort besuchen möchtest?')?>))
                                        return;

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

                                    if (!cfg.follow && !cfg.co.length) {
                                        popup.trigger('unpop');
                                        return;
                                    }

                                    core.command('map/go', cfg, true, function(data) {
                                        popup.removeClass('disabled');
                                        if (data.success) {
                                            popup.trigger('unpop');
                                            core.command();
                                        }
                                    });
                                })))
                            .append($('<div />').addClass('cell rw-4 rw-sm-12 padded').append(
                                $('<div />').addClass('btn').text(same_location ? <?=__j('Schließen')?> : <?=__j('Abbrechen')?>).click(function() {
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
                    } else if ( !same_location ) {
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
                            data.locations[id].note.tag == 0 ? null : NF.row('center').append(
                                NF.cell(false, 12)
                                    .append(NF.img('media/icons/places/tags/tag_' + data.locations[id].note.tag + '.gif'))
                                    .append(NF.n('span','', data.locations[id].note.text ? data.locations[id].note.text : markers[data.locations[id].note.tag]))
                            )
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