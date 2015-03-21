(function() {
    var render_others = function(data, target) {
        target.append($('<h3 />').text(<?=__j('Andere Spieler')?>));

        var row = $('<div />').addClass('row').appendTo(target);
        $.each(data, function(id, player) {
            var box = $('<div />').addClass('playerbox' + (player.escort ? ' escort' : '') + (player.local ? '' : ' unknown')).appendTo($('<div />').addClass('cell rw-4 padded').appendTo(row));

            box.append($('<b />').text(player.name));
            var bars = $('<div />').addClass('row').appendTo(box);

            box.attr('title', '-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty().css('width', 360);

                    var table;
                    content.append(
                        $('<b />').addClass('header').text(player.name)
                    ).append(table = $('<div />').addClass('row'));

                    var date = new Date(player.last_seen * 1000);

                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Beruf')?>))
                        .append($('<div />').addClass('cell rw-6 padded left').text(player.job))
                        .appendTo(content);

                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Spielgeschwindigkeit')?>))
                        .append($('<div />').addClass('cell rw-6 padded left').text(core.snippets.timestr(player.speed)))
                        .appendTo(content);
                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Letzte Aktivität')?>))
                        .append($('<div />').addClass('cell rw-6 padded left').text(date.toLocaleString()))
                        .appendTo(content);
                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text(player.joke[0]))
                        .append($('<div />').addClass('cell rw-6 padded left').text(player.joke[1]))
                        .appendTo(content);
                }
            }));

            if (player.stats)
                core.parts.status_bars(bars, player.stats, true);
        })
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

        var player_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-4 padded').appendTo(target));
        render_self(data.self, player_info);

        var others_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-8 padded').appendTo(target));
        render_others(data.others, others_info);

    };
})();