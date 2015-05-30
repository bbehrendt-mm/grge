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
            })
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
            })
        }
    };

    var fill_battleai = function(target, data) {
        var button;

        target.empty().append($('<h3 />').text(<?=__j('Kampfverhalten')?>));

        var bhav_select, bhav = $('<div />').addClass('row').appendTo(target);
        bhav.append($('<b />').text(<?=__j('Kampfstrategie')?>));
        bhav.append($('<div />').addClass('cell rw-6 rw-lg-8 rw-md-6 rw-sm-12 padded').append($('<label />').attr('title',<?=__j('Der ausgewählte Kampfstil beeinflusst deine Waffen- und Gegnerauswahl. Offensive Spieler werden versuchen, so viel Schaden anzurichten wie möglich. Defensive Spieler werden versuchen, Zombies so gut es geht auf Abstand zu halten.')?>).qtip(game.render.html.qtip.ingame('top')).prepend(bhav_select = $('<select />'))));

        bhav_select
            .append($('<option />').text(<?=__j('Defensiv')?>).attr('value','1'))
            .append($('<option />').text(<?=__j('Ausgeglichen')?>).attr('value','2'))
            .append($('<option />').text(<?=__j('Offensiv')?>).attr('value','3'))
            .val(data.type).on('change', function() {
                button.removeClass('disabled')
            }).selectric();

        var sw_energy, sw_breakable, sw_ammocache;
        var sw = $('<div />').addClass('row').appendTo(target);
        sw.append($('<b />').text(<?=__j('Verwendung einzelner Waffenarten sperren')?>));
        sw
            .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<label />').attr('title',<?=__j('Ist diese Option aktiviert, wirst du im Kampf keine Waffen einsetzen, die Energie verbrauchen.')?>).qtip(game.render.html.qtip.ingame('top')).text(<?=__j('Energiewaffen')?>).prepend(sw_energy = $('<input />').attr('type', 'checkbox').prop('checked', data.weapons.energy).prop('disabled', (data.weapons.energy === 'locked')))))
            .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<label />').attr('title',<?=__j('Ist diese Option aktiviert, wirst du im Kampf keine Waffen verwenden, die beim Einsatz zerstört werden (z.B. Wasserbombe).')?>).qtip(game.render.html.qtip.ingame('top')).text(<?=__j('Wurfgeschosse')?>).prepend(sw_breakable = $('<input />').attr('type', 'checkbox').prop('checked', data.weapons.throw))))
            .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<label />').attr('title',<?=__j('Ist diese Option aktiviert, wirst du im Kampf keine Waffen verwenden, die einen internen Munitionsspeicher haben (z.B. Wasserpistole).')?>).qtip(game.render.html.qtip.ingame('top')).text(<?=__j('Verbrauchswaffen')?>).prepend(sw_ammocache = $('<input />').attr('type', 'checkbox').prop('checked', data.weapons.tank))));

        var mun = $('<div />').addClass('row').appendTo(target);
        mun.append($('<b />').text(<?=__j('Verwendung einzelner Munitionstypen sperren')?>));

        var mun_elems = {};

        $.each(data.ammo, function(k,v) {
            mun.append($('<div />').addClass('cell rw-2 padded rw-lg-4 rw-md-2 rw-sm-4').append($('<label />').attr('title', game.i18n(<?=__j('Ist diese Option aktiviert, werden im Kampf keine Waffen verwendet, die diese Munition (:item) verwenden.')?>, {':item': v.name})).qtip(game.render.html.qtip.ingame('top')).append($('<img />').attr('src', 'media/icons/'+ v.icon + '.gif')).prepend(mun_elems[k] = $('<input />').attr('type', 'checkbox').prop('checked', v.locked))))
        });

        target.find(':checkbox').click(function() {
            button.removeClass('disabled')
        }).customRadioCheck();

        target.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-6 ro-6').append(
            button = $('<div />').addClass('btn btn-icon disabled')
                .append($('<span />').addClass('btn-icon-inner').append($('<i />').addClass('fa fa-check')))
                .append($('<span />').text(<?=__j('Speichern')?>))
                .click(function() {
                    var ammo = {};
                    $.each(mun_elems, function(k,v) {
                        ammo[k] = v.prop('checked') ? 1 : 0;
                    });
                    core.command('player/ai', {
                        'ai': bhav_select.val(),
                        'wp_energy': sw_energy.prop('checked') ? 1 : 0,
                        'wp_throw': sw_breakable.prop('checked') ? 1 : 0,
                        'wp_tank': sw_ammocache.prop('checked') ? 1 : 0,
                        'wp_ammo': ammo
                    }, true, function(ret) {
                        if (!ret.success) {
                            game.render.html.notify('error', <?=__j('Beim Speichern der Einstellungen ist ein Fehler aufgetreten.')?>);
                            fill_battleai(target, data);
                        } else button.addClass('disabled')
                    })
                })
        )));

    };

    core.parts.settings = function(data, target) {
        var time_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-5 rw-lg-6 rw-md-12 padded').appendTo(target));
        if (data.clock.time_mode == 0) fill_timesettings_stat(data.clock.time_settings, time_settings, data.clock.locked);
        if (data.clock.time_mode == 1) fill_timesettings_var(data.clock.time_settings, time_settings, data.clock.locked);

        var bai_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-7 rw-lg-6 rw-md-12 padded').appendTo(target));
        fill_battleai(bai_settings, data.ai);
    };
})();