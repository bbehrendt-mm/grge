(function() {
    var render_others = function(data, target, messages) {
        target.append($('<h3 />').text(core.last.players.multiplayer ? <?=__j('Andere Spieler und NPCs')?> : <?=__j('NPCs in deiner Umgebung')?>));

        var row = NF.row().appendTo(target);
        var found = false;
        $.each(data, function(id, player) {
            found = true;
            var box = $('<div />').addClass('playerbox' + (player.escort ? ' escort' : '') + (player.local ? '' : ' unknown') + (player.npc ? ' npc' : '')).appendTo($('<div />').addClass('cell rw-4 rw-lg-6 rw-md-4 rw-sm-12 padded').appendTo(row));

            box.append($('<b />').text(player.name));
            var bars = NF.row().appendTo(box);

            box.children('b').css('cursor', 'default').attr('title', '-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty().css('width', 360);

                    var table;
                    content.append(
                        $('<b />').addClass('header').text(player.name)
                    ).append(table = NF.row());

                    var date = new Date(player.last_seen * 1000);

                    if (player.npc)
                        NF.row()
                            .append(NF.cell(true, 12).text(<?=__j('Dies ist ein computergesteuerter Charakter (NPC)!')?>))
                            .appendTo(content);
                    else {
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text(<?=__j('Beruf')?>))
                            .append(NF.cell(true, 6, 0, 'left').text(player.job))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text(<?=__j('Spielgeschwindigkeit')?>))
                            .append(NF.cell(true, 6, 0, 'left').text(core.snippets.timestr(player.speed)))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text(<?=__j('Letzte Aktivität')?>))
                            .append(NF.cell(true, 6, 0, 'left').text(date.toLocaleString()))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text(player.joke[0]))
                            .append(NF.cell(true, 6, 0, 'left').text(player.joke[1]))
                            .appendTo(content);
                    }

                }
            }));

            if (player.stats)
                core.parts.status_bars(bars, player.stats, true);
        });

        if (!found) row.append(NF.cell(true, 12, 0, 'center').text(<?=__j('Hier scheint niemand zu sein ...')?>));

        if (core.last.players.multiplayer)
            row.append(NF.cell(true, 12).append(
                NF.row()
                    .append($('<div />').addClass('cell rw-4 rw-md-5 rw-sm-12 padded').append($('<div />').addClass('btn btn-zv').text(<?=__j('Post')?>).prepend(messages ? $('<img />').attr('src','media/icons/new.png') : false).click(function() {
                        game.network.load('game/pm');
                    })))
            ));
    };

    var render_self = function(data, target) {
        var mpc_escort, mpc_ping;

        target
            .empty()
            .append($('<h3 />').text(<?=__j('Du')?>))

            .append($('<div />').append($('<label />').attr('title',<?=__j('Ist diese Option aktiviert, erhalten andere Spieler begrenzte Kontrolle über dich. Sie können dich bewegen, Gegenstände auf dich anwenden oder Gegenstände in deinen Rucksack legen.')?>).qtip(game.render.html.qtip.ingame('right')).text(<?=__j('Befehle entgegennehmen')?>).prepend(mpc_escort = $('<input />').attr('type', 'checkbox').prop('checked', data.escort))))
            .append($('<div />').append($('<label />').attr('title',<?=__j('Aktiviere diese Option um anderen Spielern mitzuteilen, dass sie in den Chat kommen sollen. Der Chat-Aufruf wird nach 15 Minuten automatisch deaktiviert.')?>).qtip(game.render.html.qtip.ingame('right')).text(<?=__j('Chat-Aufruf')?>).prepend(mpc_ping = $('<input />').attr('type', 'checkbox').prop('checked', data.ping))))

            .find(':checkbox').customRadioCheck();

        $(mpc_escort).add(mpc_ping).click(function() {
            core.command('player/mp', {escort: mpc_escort.prop('checked') ? 1 : 0, ping: mpc_ping.prop('checked') ? 1 : 0}, true, function(ret) {
                if (!ret.success) {
                    game.render.html.notify('error', <?=__j('Beim Speichern der Einstellungen ist ein Fehler aufgetreten.')?>);
                    render_self(data,target);
                }
            });
        })
    };

    core.parts.mp_players = function(data, target) {

        if (core.last.players.multiplayer) {
            var player_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-4 rw-lg-6 rw-md-12 padded').appendTo(target));
            render_self(data.self, player_info);
        }

        var others_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass(core.last.players.multiplayer ? 'cell rw-8 rw-lg-6 rw-md-12 padded' : 'cell rw-8 ro-2 rw-lg-10 ro-lg-1 rw-md-12 ro-md-0 padded').appendTo(target));
        render_others(data.others, others_info, data.messages);

    };
})();