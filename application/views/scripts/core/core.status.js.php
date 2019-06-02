(function() {
    var num_decode_str = function(n) {
        switch (n) {
            case <?=Model_Status::MS_STAT_HUNGER   ?>: return 'hunger';
            case <?=Model_Status::MS_STAT_THIRST   ?>: return 'thirst';
            case <?=Model_Status::MS_STAT_HEALTH   ?>: return 'health';
            case <?=Model_Status::MS_STAT_SLEEPY   ?>: return 'sleepy';
            case <?=Model_Status::MS_STAT_ENERGY   ?>: return 'energy';
            case <?=Model_Status::MS_STAT_DRUNK    ?>: return 'drunk';
            case <?=Model_Status::MS_STAT_RADIATION?>: return 'rad';
            case <?=Model_Status::MS_STAT_ZOMBIFY  ?>: return 'zmb';
            case <?=Model_Status::MS_STAT_FREEZE   ?>: return 'freeze';

            case <?=Model_Status::MS_CHAR_DISTANCING        ?>: return 'char_mv';
            case <?=Model_Status::MS_CHAR_EVASIVENESS       ?>: return 'char_eva';
            case <?=Model_Status::MS_CHAR_ACCURACY          ?>: return 'char_acc';
            case <?=Model_Status::MS_CHAR_DAMAGE_RESISTANCE ?>: return 'char_def';
            case <?=Model_Status::MS_CHAR_BULKYNESS         ?>: return 'char_bulk';
            case <?=Model_Status::MS_CHAR_DAMAGE_MULTIPLIER ?>: return 'char_atk';
            case <?=Model_Status::MS_CHAR_LOCATION_SPAWNRATE?>: return 'char_find_loc';
            case <?=Model_Status::MS_CHAR_ITEM_SPAWNRATE    ?>: return 'char_find_item';
            
            default: return 'unknown';
        }
    };

    var num_decode_states = function(n) {
        switch (n) {
            case <?=Model_Status::MS_STAT_HUNGER?>:
            case <?=Model_Status::MS_STAT_THIRST?>:
            case <?=Model_Status::MS_STAT_HEALTH?>:
            case <?=Model_Status::MS_STAT_ENERGY?>:
                return 5;
            case <?=Model_Status::MS_STAT_SLEEPY?>:
                return 4;
            default: return 1;
        }
    };

    var num_decode_char = function(n) {
        return n >= <?=Model_Status::MS_THRESHOLD?>;
    };

    var num_decode_icon_name = function(n,v) {
        var icon_name_base = 'media/icons/bars/' + num_decode_str(n);
        var icons = num_decode_states(n);

        if (icons <= 1 || num_decode_char(n)) icon_name_base += '.gif';
        else {
            var n = Math.min(Math.floor( v / (100 / icons)) + 1, icons);
            icon_name_base += ('-' + n + '.gif');
        }

        return icon_name_base;
    }

    var num_decode_inverse = function(n) {
        switch (n) {
            case <?=Model_Status::MS_STAT_DRUNK    ?>: return true;
            case <?=Model_Status::MS_STAT_RADIATION?>: return true;
            case <?=Model_Status::MS_STAT_ZOMBIFY  ?>: return true;
            case <?=Model_Status::MS_STAT_FREEZE   ?>: return true;

            case <?=Model_Status::MS_CHAR_DISTANCING   ?>: return true;

            default: return false;
        }
    };



    var num_decode_color = function(n) {
        switch (n) {
            case <?=Model_Status::MS_STAT_HUNGER   ?>: return '#FF4A19';
            case <?=Model_Status::MS_STAT_THIRST   ?>: return '#1C44D2';
            case <?=Model_Status::MS_STAT_HEALTH   ?>: return '#A819FF';
            case <?=Model_Status::MS_STAT_SLEEPY   ?>: return '#589BF2';
            case <?=Model_Status::MS_STAT_ENERGY   ?>: return '#00D47A';
            case <?=Model_Status::MS_STAT_DRUNK    ?>: return '#ECCB19';
            case <?=Model_Status::MS_STAT_RADIATION?>: return '#A1E900';
            case <?=Model_Status::MS_STAT_ZOMBIFY  ?>: return '#906C04';
            case <?=Model_Status::MS_STAT_FREEZE   ?>: return '#96FFFF';
            default: return'#999999';
        }
    };

    var num_decode_title = function(n) {
        switch (n) {
            case <?=Model_Status::MS_STAT_HUNGER   ?>: return <?=__j('Hunger')?>;
            case <?=Model_Status::MS_STAT_THIRST   ?>: return <?=__j('Durst')?>;
            case <?=Model_Status::MS_STAT_HEALTH   ?>: return <?=__j('Gesundheit')?>;
            case <?=Model_Status::MS_STAT_SLEEPY   ?>: return <?=__j('Müdigkeit')?>;
            case <?=Model_Status::MS_STAT_ENERGY   ?>: return <?=__j('Energie')?>;
            case <?=Model_Status::MS_STAT_DRUNK    ?>: return <?=__j('Alkohol')?>;
            case <?=Model_Status::MS_STAT_RADIATION?>: return <?=__j('Verstrahlung')?>;
            case <?=Model_Status::MS_STAT_ZOMBIFY  ?>: return <?=__j('Zombie-Infektion')?>;
            case <?=Model_Status::MS_STAT_FREEZE   ?>: return <?=__j('Eisige Kälte')?>;

            case <?=Model_Status::MS_CHAR_DISTANCING        ?>: return <?=__j('Reisekosten')?>;
            case <?=Model_Status::MS_CHAR_EVASIVENESS       ?>: return <?=__j('Ausweichen')?>;
            case <?=Model_Status::MS_CHAR_ACCURACY          ?>: return <?=__j('Treffergenauigkeit')?>;
            case <?=Model_Status::MS_CHAR_DAMAGE_RESISTANCE ?>: return <?=__j('Schadensresistenz')?>;
            case <?=Model_Status::MS_CHAR_BULKYNESS         ?>: return <?=__j('Rammen')?>;
            case <?=Model_Status::MS_CHAR_DAMAGE_MULTIPLIER ?>: return <?=__j('Schadens-Multiplikator')?>;
            case <?=Model_Status::MS_CHAR_LOCATION_SPAWNRATE?>: return <?=__j('Fundrate (Orte)')?>;
            case <?=Model_Status::MS_CHAR_ITEM_SPAWNRATE    ?>: return <?=__j('Fundrate (Items)')?>;

            default: return'???';
        }
    };

    var num_decode_description = function(n) {
        switch (n) {
            case <?=Model_Status::MS_STAT_HUNGER   ?>: return <?=__j('Mit leerem Magen fällt der Kampf ums Überleben schwer. Iss regelmäßig, ansonsten verlierst du Energie und Gesundheit.')?>;
            case <?=Model_Status::MS_STAT_THIRST   ?>: return <?=__j('Es ist nicht leicht, in der Ödnis Wasser zu finden - nichtsdestotrotz ist es essentiell für dein Überleben.')?>;
            case <?=Model_Status::MS_STAT_HEALTH   ?>: return <?=__j('Du stirbst, wenn deine Gesundheit den Wert 0 erreicht. Gesundheit regeneriert sich von alleine, wenn du genug gegessen und getrunken hast. Du kannst deine Gesundheit aber auch durch die Verwendung verschiedener Items verbessern.')?>;
            case <?=Model_Status::MS_STAT_SLEEPY   ?>: return <?=__j('Zombies müssen nicht schlafen - du hingegen schon! Du solltest Übermüdung um jeden Preis vermeiden, also schlafe regelmäßig.')?>;
            case <?=Model_Status::MS_STAT_ENERGY   ?>: return <?=__j('Du brauchst Energie, um Aktionen durchführen zu können. Energie regeneriert sich von alleine, wenn du bei guter Gesundheit und nicht hungrig/durstig bist. Es gibt allerdings auch einige Items, die Energie regenerieren.')?>;
            case <?=Model_Status::MS_STAT_DRUNK    ?>: return <?=__j('Mit ordentlich Promille im Blut wird das Leben nach der Apokalypse gleich viel erträglicher. Leider wird es auch kürzer, denn wenn du völlig abgefüllt in der Ecke liegst, kannst du dich nicht wirklich gut gegen Zombies verteidigen. Wenigstens um die Langzeitschäden an deiner Leber brauchst du dich nicht mehr zu sorgen ...')?>;
            case <?=Model_Status::MS_STAT_RADIATION?>: return <?=__j('Du warst Strahlung ausgesetzt! Das ist relativ ungesund, und dein Körper kann die strahlenden Partikel nur langsam abbauen. Während eine geringe Strahlendosis noch vertretbar ist, können höhere Strahlenmengen schnell dein Leben bedrohen!')?>;
            case <?=Model_Status::MS_STAT_ZOMBIFY  ?>: return <?=__j('Ohje, das ist gar nicht gut... Offensichtlich bist du mit dem Zombievirus infiziert. Du solltest unbedingt ein Heilmittel finden, ansonsten wirst du sehr bald ins Unleben übertreten...')?>;
            case <?=Model_Status::MS_STAT_FREEZE   ?>: return <?=__j('Der kalte Wind bläst dir um die Ohren... allzu lange kann du hier nicht bleiben, wenn du nicht erfrieren willst.')?>;

            case <?=Model_Status::MS_CHAR_DISTANCING        ?>: return <?=__j('Nicht jedem fallen lange Märsche durch das Ödland gleich leicht ...')?>;
            case <?=Model_Status::MS_CHAR_EVASIVENESS       ?>: return <?=__j('Überlebenstipp #34: Gegen schwere Verletzungen ist es hilfreich, sich einfach nicht treffen zu lassen.')?>;
            case <?=Model_Status::MS_CHAR_ACCURACY          ?>: return <?=__j('Überlebenstipp #35: Auch die beste Waffe hilft nicht, wenn du damit nichts triffst.')?>;
            case <?=Model_Status::MS_CHAR_DAMAGE_RESISTANCE ?>: return <?=__j('Eine Konfrontation mit Zombies muss nicht gleich dein Ende bedeuten - zumindest, wenn du in der Lage bist, etwas Schaden einzustecken, ohne direkt zu sterben ...')?>;
            case <?=Model_Status::MS_CHAR_BULKYNESS         ?>: return <?=__j('Wenn du planst, durch eine Horde Zombies einfach hindurchzurennen, solltest du dir vorher eventuell etwas Bauchfett anfressen.')?>;
            case <?=Model_Status::MS_CHAR_DAMAGE_MULTIPLIER ?>: return <?=__j('Es kommt nicht auf die Länge deiner Machete an, sondern darauf, wie kunstvoll du sie schwingst.')?>;
            case <?=Model_Status::MS_CHAR_LOCATION_SPAWNRATE?>: return <?=__j('Wenn du dich ein bisschen mehr auf Objekte in der Entfernung konzentrierst findest du überraschenderweise leichter Dinge, die nicht direkt vor die liegen.')?>;
            case <?=Model_Status::MS_CHAR_ITEM_SPAWNRATE    ?>: return <?=__j('Wenn du ab und zu einmal einen Blick auf den Boden wirfst findest du überraschenderweise manchmal sogar nützliche Items!')?>;
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
        var sum_absolute_p = absolute_p.reduce(fsum,0), sum_absolute_n = absolute_n.reduce(fsum,0), sum_relative_p = Math.max(0,1+relative_p.reduce(fsum,0)), sum_relative_n = Math.max(0, 1+relative_n.reduce(fsum,0));
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
                $('<span />').text(Math.round(relative_p[i].value * 100) + "%")
            );
            else tmp.append($('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')).append($('<span />').text(" "));

            row.append(tmp = $('<div />').addClass('cell rw-6 left'));
            if (relative_n[i]) tmp.append(
                $('<span />').text(Math.round(relative_n[i].value * 100) + "%")
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

    var make_small_bar = function(type, value) {
        if (type === null) return $('<div />').addClass('cell rw-4').html('&nbsp;');
        return $('<div />').addClass('cell rw-4 smallpad').append(
            $('<div />').addClass('varbar').append(
                $('<div />').css({width: value + '%', background: num_decode_color(type)})
            )
        ).attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function (event, api) {
                    $(this).find('.qtip-content').empty().append(
                        $('<b />').addClass('header hold').text(num_decode_title(type))
                    ).append(
                        $('<div />').addClass('info center').append(make_bar(type, value, null, true))
                    );
                }
            })
        );
    };

    var make_bar = function(type, value, effects, inline) {
        var char = num_decode_char(type);
        var cb;
        if (char) {
            value *= 100;
            var w = Math.min(50,Math.abs(100 - value)/2);
            var l = value < 100 ? (50 - w) : 50;

            cb = $('<div />').addClass((num_decode_inverse(type) ? (value > 100) : (value < 100)) ? 'negative': 'positive').css({position: 'relative', left: l + '%', width: w + '%'});
            //cb = $('<div />').css({width: value + '%', background: num_decode_color(type)});
        } else cb = $('<div />').css({width: value + '%', background: num_decode_color(type)});

        var bar = $('<div />').addClass('cell rw-' + (inline ? 12 : 4)).append(
            $('<div />').addClass('bar').append(
                $('<img />').attr('src', num_decode_icon_name(type,value))
            ).append(
                $('<div />').addClass('background')
                    .append(cb)
                    .append(
                        $('<div />').addClass('label').text(Math.round(value * 10)/10 + (char ? ' %' : ''))
                    )
            )
        );

        if (!inline) bar.attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function (event, api) {
                    var content = $(this).find('.qtip-content').empty().append(
                        $('<b />').addClass('header').text(num_decode_title(type))
                    ).append(
                        $('<div />').addClass('info center').text(game.i18n(<?=__j('Aktueller Wert: :num')?>, {':num': value}))
                    ).append(
                        $('<span />').addClass('separator')
                    ).append(
                        $('<span />').text(num_decode_description(type))
                    ).append(
                        $('<span />').addClass('separator')
                    ).append(make_buff_table(effects, num_decode_inverse(type)))
                }
            })
        );

        return bar;
    };

    var make_buffbar = function(buffs, bars, small) {
        var target;
        var bar = $('<div />').addClass(small ? 'cell rw-12 padded' : 'cell rw-4').append(
            target = $('<div />').addClass(small ? 'center' : 'bar')
        );

        var hidden = [];
        $.each(bars, function(k,v) {
            if (k > 5) hidden.push(k);
        });

        if (!(buffs.length + hidden.length))
            target.append($('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif'));
        else {

            $.each(hidden,function(k,v) {
                v = parseInt(v);
                $('<img />').addClass('status').attr('src',num_decode_icon_name(v,bars[v].value)).attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                        render: function (event, api) {
                            var content = $(this).find('.qtip-content').empty().append(
                                $('<b />').addClass('header hold').text(num_decode_title(v))
                            ).append(
                                NF.row().append(make_bar(v,bars[v].value,bars[v].buffs, true))
                            );

                            if (!small)
                                content.append(
                                    $('<span />').addClass('separator')
                                ).append(
                                    $('<span />').text(num_decode_description(v))
                                ).append(
                                    $('<span />').addClass('separator')
                                ).append(make_buff_table(bars[v].buffs, num_decode_inverse(v)));
                        }
                    })
                ).appendTo(target);
            });

            $.each(buffs, function(k,v) {
                $('<img />').addClass('buff').attr('src','media/icons/' + v.icon + '.gif').attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                        render: function (event, api) {
                            var content = $(this).find('.qtip-content').empty().append(
                                $('<b />').addClass('header').text(v.name)
                            ).append(
                                $('<span />').text(v.desc)
                            );

                            if (v.time)
                                content.append(
                                    $('<span />').addClass('separator')
                                ).append($('<div />').text(game.i18n(<?=__j('Verbleibende Dauer: :time')?>, {':time': v.time})));
                        }
                    })
                ).appendTo(target);
            });
        }

        return bar;
    };

    core.parts.status_bars = function(target, data, small) {
        var order = [
            <?=Model_Status::MS_STAT_ENERGY?>,<?=Model_Status::MS_STAT_HEALTH?>,<?=Model_Status::MS_STAT_HUNGER?>,
            <?=Model_Status::MS_STAT_SLEEPY?>,               null              ,<?=Model_Status::MS_STAT_THIRST?>
        ];

        if (small) {
            $.each(order, function(k,v) {
                target.append(make_small_bar(v,v ? data.bars[v].value : 0));
            });
            target.append(make_buffbar($.objToArray(data.buffs,true), data.bars, true));
        } else $.each(order, function(k,v) {
            target.append(v ? make_bar(v,data.bars[v].value,data.bars[v].buffs) : make_buffbar($.objToArray(data.buffs,true), data.bars, false));
        });
    };

    core.parts.status = function(data, data_clock, target) {
        var main, inventory, clock, bars;

        target.empty().append(
            main = NF.row().append(
                $('<div />').addClass('cell rw-9').append(
                    bars = NF.row()
                )
            ).append(
                clock = $('<div />').addClass('cell rw-3 padded')
            )
        );

        core.parts.status_bars(bars, data, false);

        core.parts.clock(data_clock,clock);
    };

    core.parts.clock = function(data, target) {

        var clockbox = $('<div />').addClass('clockbox').appendTo(target);
        var countdown = $('<div />').addClass('countdown').appendTo(clockbox);

        clockbox.attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
            render: function(event,api) {
                var content = $(this).find('.qtip-content').empty();

                content.append(NF.info( <?=__j('Mit jedem Ablauf des Countdowns vergehen 5 Minuten in Spielzeit. Zudem können bestimmte Events ausgelöst werden, wie beispielsweise der Fund eines neuen Gegenstands oder ein Zombie-Angriff.')?> ));
                content.append($('<span />').addClass('separator'));
                content.append( NF.row()
                    .append( NF.cell(true,12,0, 'b center').text( <?=__j('Tageszeiten');?> ) )
                ).append( NF.row()
                    .append( NF.cell(true,5, 0, "right") .text( <?=__j('Morgens');?> ) )
                    .append( NF.cell(true,2, 0, "center").append(NF.img( 'media/icons/buffs/dtmorning.gif' )) )
                    .append( NF.cell(true,5, 0, "left" ) .text( "6:00 - 9:59" ) )
                ).append( NF.row()
                    .append( NF.cell(true,5, 0, "right").text( <?=__j('Tag');?> ) )
                    .append( NF.cell(true,2, 0, "center").append(NF.img( 'media/icons/buffs/dtday.gif' )) )
                    .append( NF.cell(true,5, 0, "left" ).text( "10:00 - 17:59" ) )
                ).append( NF.row()
                    .append( NF.cell(true,5, 0, "right").text( <?=__j('Abends');?> ) )
                    .append( NF.cell(true,2, 0, "center").append(NF.img( 'media/icons/buffs/dtevening.gif' )) )
                    .append( NF.cell(true,5, 0, "left" ).text( "18:00 - 21:59" ) )
                ).append( NF.row()
                    .append( NF.cell(true,5, 0, "right").text( <?=__j('Nacht');?> ) )
                    .append( NF.cell(true,2, 0, "center").append(NF.img( 'media/icons/buffs/dtnight.gif' )) )
                    .append( NF.cell(true,5, 0, "left" ).text( "22:00 - 5:59" ) )
                )

                content.append(NF.info( <?=__j('Die Tageszeit hat Einfluss auf bestimmte Spielparameter. So sind beispielsweise manche Aktionen zu bestimmten Tageszeiten effektiver. In der Statusleiste findest du weitere Details über die Auswirkungen der aktuellen Tageszeit.')?> ));

            }
        }));

        var timestr, datestr;
        $('<div />').addClass('datebox').append(
            $('<div />').addClass('row hide-sm').append(
                $('<div />').addClass('cell rw-4').append(
                    $('<i />').addClass('fa fa-clock-o hide-mobile')
                ).append(
                    timestr = $('<span />')
                )
            ).append(
                $('<div />').addClass('cell rw-8').append(
                    $('<i />').addClass('fa fa-calendar hide-mobile')
                ).append(
                    datestr = $('<span />')
                )
            )
        ).appendTo(clockbox);

        var next_tick = data.next_tick * 1000;
        var last_tick = data.last_tick * 1000;
        var ingame = data.ingame * 1000;
        var current_offset = (data.current * 1000) - (new Date()).getTime();

        var updater = function() {
            var left = next_tick - ((new Date()).getTime() + current_offset);

            if (left < 0) {
                countdown.html('<span class="hide-sm">' + <?=__j('Weiter');?> + '</span><span class="hide-md hide-lg hide-desktop"><i class="fa fa-angle-double-right"><i></span>');
                clockbox.addClass('pointer').click(function() {
                    core.command();
                });
                return;
            }

            var datetime = new Date(ingame);
            var date = [<?=__j('So')?>,<?=__j('Mo')?>,<?=__j('Di')?>,<?=__j('Mi')?>,<?=__j('Do')?>,<?=__j('Fr')?>,<?=__j('Sa')?>][datetime.getDay()] + ', ' + datetime.toLocaleDateString();
            var time = datetime.getHours() + ':' + (datetime.getMinutes() < 10 ? '0' + datetime.getMinutes() : datetime.getMinutes());

            datestr.softText(date);
            timestr.softText(time);

            var scm = [];
            $.each([86400000,3600000,60000,1000,10], function(k,v) {
                var tmp = Math.floor(left/v);
                left -= tmp * v;
                if (tmp > 0 || v <= 60000)
                    scm.push(tmp);
            });

            var i = 0;
            countdown.children().each(function() {
                if (i >= scm.length)
                    $(this).remove();
                else {
                    var tx = scm[i] < 10 ? '0' + scm[i] : scm[i];
                    $(this).softText(tx);
                }
                i++;
            });
            if (i < scm.length)
                for (i; i < scm.length; i++)
                    $('<div />').addClass(i ==  (scm.length -1) ? (game.s.quality() < 3 ? 'hidden' : 'hide-md hide-sm') : '').text(scm[i] < 10 ? '0' + scm[i] : scm[i]).appendTo(countdown);

            window.requestAnimationFrame(updater);
        };

        updater();
    }
})();