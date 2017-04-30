(function() {

    var fill_timesettings_var = function(data, target, lock) {
        var set_row;
        target.append($('<h3 />').text(<?=__j('Spielgeschwindigkeit')?>))
            .append($('<p />').addClass('justify').text(game.i18n(<?=__j('Du kannst die Spielgeschwindigkeit jederzeit deinen Bedürfnissen anpassen. Bedenke jedoch, dass eine Änderung nur alle :minutes möglich ist.')?>, {':minutes': core.snippets.timestr(data.interval)})))
            .append(set_row = $('<div />').addClass('row center'));

        $.each(data.selection, function(k,v) {
            set_row.addClass(lock ? 'disabled' : '').prepend(
                $('<label />').attr('title', core.snippets.timestr(v)).append(
                    $('<input />').attr({
                        name: 'set_time',
                        type: 'radio',
                        value: k
                    }).prop("checked", k == data.player).click(function() {
                        if (!confirm(<?=__j('Bist du sicher, dass du die Spielgeschwindigkeit ändern möchtest?')?>))
                            return false;
                        core.command('player/reflux', {set: $(this).val()})
                    })
                ).qtip(game.render.html.qtip.ingame('bottom'))
            )
        });
        set_row.find('input').customRadioCheck();
        set_row.find('>label').css('margin', 0);

        target.append($('<div />').addClass('center row').append($('<b />').text(<?=__j('Aktuelle Geschwindigkeit')?>)).append($('<span />').text(core.snippets.timestr(data.game))));

        if (lock) {
            var ct = $('<p />').appendTo(target);
            core.snippets.countdown(lock, function(s, v) {
                if (v == 0) {
                    set_row.removeClass('disabled');
                    ct.remove();
                    game.render.html.notify('success', <?=__j('Die Sperre ist abgelaufen - ab sofort kannst du die Spielgeschwindigkeit wieder ändern!')?>);
                    return false;
                }

                ct.html(game.i18n(<?=__j('Änderung in ::i:: :time ::/i:: wieder möglich.')?>, {':time': s}));
                return true;
            }, 'time_lock')
        }
    };

    var fill_timesettings_stat = function(data, target, lock) {
        var set_row, button;
        target.append($('<h3 />').text(<?=__j('Spiel pausieren')?>))
            .append($('<p />').addClass('justify').text(game.i18n(<?=__j('Du kannst das Spiel jederzeit anhalten. Allerdings muss eine Pause mindestens :minutes1 dauern und du musst :minutes2 warten, bis du erneut pausieren kannst.')?>, {':minutes1': core.snippets.timestr(data.duration),':minutes2': core.snippets.timestr(data.interval)})))
            .append(set_row = $('<div />').addClass('row center'));

        $('<div />').addClass('cell rw-12 padded').appendTo(set_row).append(
            button = $('<div />').addClass('btn').addClass(lock ? 'disabled' : '').text(<?=__j('Pausieren')?>).click(function() {
                if (confirm(<?=__j('Bist du sicher, dass du das Spiel jetzt pausieren willst?')?>))
                    core.command('player/pause', {set: 1});
            })
        );

        if (lock) {
            var ct = $('<p />').appendTo(target);
            core.snippets.countdown(lock, function(s, v) {
                if (v == 0) {
                    button.removeClass('disabled');
                    ct.remove();
                    game.render.html.notify('success', <?=__j('Die Sperre ist abgelaufen - du kannst das Spiel ab sofort wieder pausieren!')?>);
                    return false;
                }

                ct.html(game.i18n(<?=__j('Nächte Pause in ::i:: :time ::/i:: möglich.')?>, {':time': s}));
                return true;
            }, 'time_lock')
        }
    };

    var fill_battleai = function(target, data) {
        var button;

        var sel_clone = NF.n('select')
            .append(NF.n('option', '', <?=__j('Hohe Priorität')?>).attr('value','+'))
            .append(NF.n('option', '', <?=__j('Normale Priorität')?>).attr('value','0'))
            .append(NF.n('option', '', <?=__j('Geringe Priorität')?>).attr('value','-'));

        target.empty()
            .append($('<h3 />').text(<?=__j('Kampfverhalten')?>))

            .append(NF.row().attr('title', <?=__j('Steuert die Priorität, Zombies zu attackieren, die für dich selbst eine Bedrohung darstellen.')?>).qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text(<?=__j('Selbstverteidigung')?>))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_self').find('option[value="' + data[0] + '"]').prop('selected',true).end()))
            )

            .append(NF.row().attr('title', <?=__j('Steuert die Priorität, Zombies zu attackieren, die für deine Kameraden eine Bedrohung darstellen.')?>).qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text(<?=__j('Teamverteidigung')?>))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_team').find('option[value="' + data[1] + '"]').prop('selected',true).end()))
            )

            .append(NF.row().attr('title', <?=__j('Steuert die Priorität, im Kampf zu einer besseren Waffe zu wechseln.')?>).qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text(<?=__j('Waffenauswahl')?>))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_wpn').find('option[value="' + data[2] + '"]').prop('selected',true).end()))
            )

            .append(NF.row().attr('title', <?=__j('Steuert die Priorität, die optimale Angriffsdistanz zu den Zombies für die aktuelle Waffe herzustellen.')?>).qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text(<?=__j('Kampfdistanz')?>))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_move').find('option[value="' + data[3] + '"]').prop('selected',true).end()))
            )

            .append(NF.row().append(NF.cell(false, 6, 6).append(
                button = $('<div />').addClass('btn btn-icon disabled')
                    .append($('<span />').addClass('btn-icon-inner').append($('<i />').addClass('fa fa-check')))
                    .append($('<span />').text(<?=__j('Speichern')?>))
                    .click(function() {
                        core.command('player/ai', {
                            ai: $('#bhav_self').val() + $('#bhav_team').val() + $('#bhav_wpn').val() + $('#bhav_move').val()
                        }, true, function(ret) {
                            if (!ret.success) {
                                game.render.html.notify('error', <?=__j('Beim Speichern der Einstellungen ist ein Fehler aufgetreten.')?>);
                                fill_battleai(target, data);
                            } else button.addClass('disabled')
                        })
                    })
            ))).find('select').on('change', function() {button.removeClass('disabled');}).selectric();
    };

    core.parts.settings = function(data, target) {
        var time_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-5 rw-lg-6 rw-md-12 padded').appendTo(target));
        if (data.clock.time_mode == 0) fill_timesettings_stat(data.clock.time_settings, time_settings, data.clock.locked);
        if (data.clock.time_mode == 1) fill_timesettings_var(data.clock.time_settings, time_settings, data.clock.locked);

        var bai_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-7 rw-lg-6 rw-md-12 padded').appendTo(target));
        fill_battleai(bai_settings, data.ai);
    };
})();