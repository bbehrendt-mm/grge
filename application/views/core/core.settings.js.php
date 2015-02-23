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

        target.append($('<p />').addClass('center').append($('<b />').text(<?=__j('Aktuelle Geschwindigkeit')?>)).append($('<span />').text(core.snippets.timestr(data.game))));

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

    var fill_timesettings_stat = function(target) {
        target.append($('<h3 />').text(<?=__j('Spiel pausieren')?>))
    };

    core.parts.settings = function(data, target) {
        time_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-5 padded').appendTo(target));
        if (data.time_mode == 0) fill_timesettings_stat(time_settings);
        if (data.time_mode == 1) fill_timesettings_var(data.time_settings, time_settings, data.locked);
    };
})();