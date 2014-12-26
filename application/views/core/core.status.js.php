(function() {
    var num_decode_str = function(n) {
        switch (n) {
            case 1: return 'hunger';
            case 2: return 'thirst';
            case 3: return 'health';
            case 4: return 'sleepy';
            case 5: return 'energy';
            case 6: return 'drunk';
            case 7: return 'rad';
            case 8: return 'zmb';
            case 9: return 'freeze';
            default: return 'unknown';
        }
    };

    var num_decode_inverse = function(n) {
        switch (n) {
            case 1: return false;
            case 2: return false;
            case 3: return false;
            case 4: return false;
            case 5: return false;
            case 6: return true;
            case 7: return true;
            case 8: return true;
            case 9: return true;
            default: return false;
        }
    };

    var num_decode_color = function(n) {
        switch (n) {
            case 1: return '#FF4A19';
            case 2: return '#1C44D2';
            case 3: return '#A819FF';
            case 4: return '#589BF2';
            case 5: return '#00D47A';
            case 6: return '#ECCB19';
            case 7: return '#A1E900';
            case 8: return '#906C04';
            case 9: return '#96FFFF';
            default: return'#999999';
        }
    };

    var num_decode_title = function(n) {
        switch (n) {
            case 1: return <?=__j('Hunger')?>;
            case 2: return <?=__j('Durst')?>;
            case 3: return <?=__j('Gesundheit')?>;
            case 4: return <?=__j('Müdigkeit')?>;
            case 5: return <?=__j('Energie')?>;
            case 6: return <?=__j('Alkohol')?>;
            case 7: return <?=__j('Verstrahlung')?>;
            case 8: return <?=__j('Zombie-Infektion')?>;
            case 9: return <?=__j('Eisige Kälte')?>;
            default: return'???';
        }
    };

    var num_decode_description = function(n) {
        switch (n) {
            case 1: return <?=__j('Mit leerem Magen fällt der Kampf ums Überleben schwer. Iss regelmäßig, ansonsten verlierst du Energie und Gesundheit.')?>;
            case 2: return <?=__j('Es ist nicht leicht, in der Ödnis Wasser zu finden - nichtsdestotrotz ist es essentiell für dein Überleben.')?>;
            case 3: return <?=__j('Du stirbst, wenn deine Gesundheit den Wert 0 erreicht. Gesundheit regeneriert sich von alleine, wenn du genug gegessen und getrunken hast. Du kannst deine Gesundheit aber auch durch die Verwendung verschiedener Items verbessern.')?>;
            case 4: return <?=__j('Zombies müssen nicht schlafen - du hingegen schon! Du solltest Übermüdung um jeden Preis vermeiden, also schlafe regelmäßig.')?>;
            case 5: return <?=__j('Du brauchst Energie, um Aktionen durchführen zu können. Energie regeneriert sich von alleine, wenn du bei guter Gesundheit und nicht hungrig/durstig bist. Es gibt allerdings auch einige Items, die Energie regenerieren.')?>;
            case 6: return <?=__j('Mit ordentlich Promille im Blut wird das Leben nach der Apokalypse gleich viel erträglicher. Leider wird es auch kürzer, denn wenn du völlig abgefüllt in der Ecke liegst, kannst du dich nicht wirklich gut gegen Zombies verteidigen. Wenigstens um die Langzeitschäden an deiner Leber brauchst du dich nicht mehr zu sorgen ...')?>;
            case 7: return <?=__j('Du warst Strahlung ausgesetzt! Das ist relativ ungesund, und dein Körper kann die strahlenden Partikel nur langsam abbauen. Während eine geringe Strahlendosis noch vertretbar ist, können höhere Strahlenmengen schnell dein Leben bedrohen!')?>;
            case 8: return <?=__j('Ohje, das ist gar nicht gut... Offensichtlich bist du mit dem Zombievirus infiziert. Du solltest unbedingt ein Heilmittel finden, ansonsten wirst du sehr bald ins Unleben übertreten...')?>;
            case 9: return <?=__j('Der kalte Wind bläst dir um die Ohren... allzu lange kann du hier nicht bleiben, wenn du nicht erfrieren willst.')?>;
            default: return'???';
        }
    };

    var make_buff_table = function(effects, inverse) {
        var i, tmp, row;
        var absolute_p = [],absolute_n = [],relative_p = [],relative_n = [];

        $.each(effects, function(k,v) {
            if (v.effects[1] != 0) absolute_p.push({value: v.effects[1], icon: v.icon});
            if (v.effects[2] != 0) relative_p.push({value: v.effects[2], icon: v.icon});
            if (v.effects[3] != 0) absolute_n.push({value: v.effects[3], icon: v.icon});
            if (v.effects[4] != 0) relative_n.push({value: v.effects[4], icon: v.icon});
        });

        var fsum = function(last, current) {return last + current.value};
        var sum_absolute_p = absolute_p.reduce(fsum,0), sum_absolute_n = absolute_n.reduce(fsum,0), sum_relative_p = 1+relative_p.reduce(fsum,0), sum_relative_n = 1+relative_n.reduce(fsum,0);
        var sum_p = sum_absolute_p * sum_relative_p, sum_n = sum_absolute_n * sum_relative_n;
        var sum = sum_p - sum_n;

        var table = $('<div />').addClass('row statcalc');
        if (inverse) table.addClass('inverse');

        table.append($('<h4 />').addClass('center').text(<?=__j('Absolute Einflüsse')?>));
        for (i = 0; i < Math.max(absolute_p.length, absolute_n.length); i++) {
            row = $('<div />').addClass('row line').appendTo(table);
            row.append(tmp = $('<div />').addClass('cell rw-6 right'));
            if (absolute_p[i]) tmp.append(
                $('<img />').attr('src', 'media/icons/' + absolute_p[i].icon + '.gif')
            ).append(
                $('<span />').text(Math.round(100*absolute_p[i].value)/100)
            );
            else tmp.append($('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')).append($('<span />').text(" "));

            row.append(tmp = $('<div />').addClass('cell rw-6 left'));
            if (absolute_n[i]) tmp.append(
                $('<span />').text(-Math.round(100*absolute_n[i].value)/100)
            ).append(
                $('<img />').attr('src', 'media/icons/' + absolute_n[i].icon + '.gif')
            );
            else tmp.append($('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')).append($('<span />').text(" "));
        }
        $('<div />').addClass('row line sum').appendTo(table).append($('<div />').addClass('cell rw-6 right sum-green').text(Math.round(100*sum_absolute_p)/100)).append($('<div />').addClass('cell rw-6 left sum sum-red').text(-Math.round(100*sum_absolute_n)/100));

        table.append($('<h4 />').addClass('center').text(<?=__j('Relative Einflüsse')?>));
        for (i = 0; i < Math.max(relative_p.length, relative_n.length); i++) {
            row = $('<div />').addClass('row line').appendTo(table);
            row.append(tmp = $('<div />').addClass('cell rw-6 right'));
            if (relative_p[i]) tmp.append(
                $('<img />').attr('src', 'media/icons/' + relative_p[i].icon + '.gif')
            ).append(
                $('<span />').text(relative_p[i].value * 100 + "%")
            );
            else tmp.append($('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')).append($('<span />').text(" "));

            row.append(tmp = $('<div />').addClass('cell rw-6 left'));
            if (relative_n[i]) tmp.append(
                $('<span />').text(relative_n[i].value * 100 + "%")
            ).append(
                $('<img />').attr('src', 'media/icons/' + relative_n[i].icon + '.gif')
            );
            else tmp.append($('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')).append($('<span />').text(" "));
        }
        $('<div />').addClass('row line sum').appendTo(table).append($('<div />').addClass('cell rw-6 right sum-green').text(Math.round(sum_relative_p * 100) + "%")).append($('<div />').addClass('cell rw-6 left sum sum-red').text(Math.round(sum_relative_n * 100) + "%"));

        table.append($('<h4 />').addClass('center').text(<?=__j('Berechnung')?>));

        $('<div />').addClass('row line').appendTo(table).append($('<div />').addClass('cell rw-6 right').text(Math.round(100*sum_absolute_p)/100)).append($('<div />').addClass('cell rw-6 left').text(-Math.round(100*sum_absolute_n)/100));
        $('<div />').addClass('row line').appendTo(table).append($('<div />').addClass('cell rw-6 right').text(Math.round(sum_relative_p * 100) + "%")).append($('<div />').addClass('cell rw-6 left').text(Math.round(sum_relative_n * 100) + "%"));
        $('<div />').addClass('row line sum').appendTo(table).append($('<div />').addClass('cell rw-6 right sum-green').text(Math.round(100 * sum_p)/100)).append($('<div />').addClass('cell rw-6 left sum sum-red').text(-Math.round(100 * sum_n)/100));

        table.append($('<h4 />').addClass('center').text(<?=__j('Gesamt')?>));
        $('<div />').addClass('row line sum').appendTo(table).append($('<div />').addClass('cell rw-12 center').addClass(sum >= 0 ? 'positive' : 'negative').text(Math.round(100*sum)/100));
        return table;
    };

    var make_bar = function(type, value, effects) {
        return $('<div />').addClass('cell rw-4').append(
            $('<div />').addClass('bar').append(
                $('<img />').attr('src', 'media/icons/status_' + num_decode_str(type) + '.gif')
            ).append(
                $('<div />').addClass('background').append(
                    $('<div />').css({width: value + '%', background: num_decode_color(type)})
                )
            )
        ).attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function (event, api) {
                    var content = $(this).find('.qtip-content').empty().append(
                        $('<b />').addClass('header').text(num_decode_title(type))
                    ).append(
                        $('<div />').addClass('note center').text(game.i18n(<?=__j('Aktueller Wert: :num')?>, {':num': value}))
                    ).append(
                        $('<span />').addClass('separator')
                    ).append(
                        $('<span />').text(num_decode_description(type))
                    ).append(
                        $('<span />').addClass('separator')
                    ).append(
                        make_buff_table(effects, num_decode_inverse(type))
                    )
                }
            })
        );
    };

    var make_buffbar = function() {
        var bar = $('<div />').addClass('cell rw-4').text('buffs');
        return bar;
    };

    core.parts.status = function(data, target) {
        var main, inventory, clock;

        target.empty().append(
            main = $('<div />').addClass('row').append(
                $('<div />').addClass('cell rw-9').append(
                    inventory = $('<div />').addClass('row')
                )
            ).append(
                $('<div />').addClass('cell rw-3').append(
                    clock = $('<div />').addClass('row')
                )
            )
        );

        $.each([5,3,1,4,null,2], function(k,v) {
            inventory.append(v ? make_bar(v,data.bars[v].value,data.bars[v].buffs) : make_buffbar());
        })


    };
})();