(function() {
    var colosseum = function(data, target) {
        var round, rank, next_arena;
        $(target).empty()
            .append(
            $('<div />').addClass('cell rw-6 padded').append(
                round = $('<div />').addClass('widget')
            )
        ).append(
            $('<div />').addClass('cell rw-6 padded').append(
                rank = $('<div />').addClass('widget')
            )
        ).append(
            $('<div />').addClass('cell rw-12 padded').append(
                next_arena = $('<div />').addClass('widget')
            )
        );

        var ranks = [<?=__j('Neulingskämpfe')?>,<?=__j('Tournament für Nachwuchsmetzler')?>,<?=__j('Tournament für routinierte Schlächter')?>,<?=__j('Tournament für Profikiller')?>,<?=__j('Master-Tournament')?>];
        var arenas = [<?=__j('Cagematch')?>,<?=__j('Boxring')?>,<?=__j('Freiluft-Arena')?>,<?=__j('Hauptplatz des Kolosseums')?>];

        round
            .text(data.level == 0 ? <?=__j('Qualifikationsrunde')?> : game.i18n(<?=__j('Runde :round')?>,{':round': data.level}))
            .attr('title', <?=__j('Für jeden gewonnenen Kampf steigst du im Kolosseum eine Ebene auf. Außerdem erhälst du Seelenpunkte sowie nützliche Gegenstände. Natürlich werden die Kämpfe mit jeder Runde gefährlicher...')?>)
            .qtip(game.render.html.qtip.ingame('top'));

        rank.text(ranks[data.rank]);

        next_arena
            .append($('<b />').text(<?=__j('Nächster Kampf:')?>)).append('<br />')
            .append($('<span />').text(arenas[data.arena]))
            .attr('title', <?=__j('Jede Arena des Colosseums stellt dich vor andere Herausforderungen. Achte darauf wo der nächste Kampf stattfindet, um dich optimal zu bewaffnen.')?>)
            .qtip(game.render.html.qtip.ingame('top'));
    };

    var scoutmode = function(data, target) {
        var level, tx;

        $(target).empty()
            .append(
            $('<div />').addClass('cell rw-12 padded').append(
                level = $('<div />').addClass('widget')
            )
        );

        level.text(<?=__j('Detailgrad deiner Karte: ')?>).append(tx = $('<b />').text(data.level + '%'));
        level.attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
            render: function() {
                var content = $(this).find('.qtip-content').empty().append(
                    $('<b />').addClass('header').text(<?=__j('Diesen Ort erkunden')?>)
                ).append(
                    $('<span />').text(<?=__j('Um in diesem Spielmodus punkte zu sammeln, musst du so viele Ruinen wie möglich kartographieren. Je gründlicher du arbeitest, desto schneller steigt der Detailgrad deiner Karte - aber du gehst auch ein größeres Risiko ein.')?>)
                ).append('<span class="separator" />')
                .append(
                    $('<div />').addClass('btn btn-zv').text(<?=__j('Überblicken')?>).click(function() {
                        core.command('location/scout', {speed: 1});
                    })
                ).append(
                    $('<div />').addClass('btn btn-zv').text(<?=__j('Skizzieren')?>).click(function() {
                        core.command('location/scout', {speed: 2});
                    })
                ).append(
                    $('<div />').addClass('btn btn-zv').text(<?=__j('Vermessen')?>).click(function() {
                        core.command('location/scout', {speed: 3});
                    })
                ).append(
                    $('<div />').addClass('btn btn-zv ' + (data.laser ? '' : 'disabled')).text(<?=__j('Lasermessgerät einsetzen')?>).click(function() {
                        core.command('location/scout', {speed: 'item'});
                    })
                )
            }
        }))
    };

    var roadtrip = function(data, target) {
        $(target).empty();

        var main_gauge, parts_gauge, data_space;

        $(target)
            .append($('<div />').addClass('cell rw-4 padded').append(main_gauge = $('<div />').addClass('widget')))
            .append($('<div />').addClass('cell rw-4 padded').append(parts_gauge = $('<div />').addClass('widget')))
            .append(data_space = $('<div />').addClass('cell rw-4 padded'))
            .append($('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('car-weightbar').append($('<div />').css('width', (100*data.weight[0]/data.weight[1]) + '%')).attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty()
                        .append($('<b />').addClass('header').text(<?=__j('Beladung')?>))
                        .append($('<div />').text(<?=__j('Du fährst ein Wohnmobil, keinen LKW - wenn du mehr einlädst als der Motor ziehen kann, wirst du nicht vom Fleck kommen.')?>))
                        .append('<span class="separator" />')
                        .append($('<div />').addClass('center').text(data.weight[0] + ' / ' + data.weight[1]));
                    }
            }))));

        $.each(data.parts, function(k,v) {
            var canvas;
            parts_gauge.append($('<div />').addClass('margin').css({position: 'relative', height: 32, width: 32, display: 'inline-block'})
                .append($('<img />').attr('src','media/icons/' + v.icon + '.gif').css({position: 'absolute', top: 8, left: 8}))
                .append(canvas = $('<canvas />').attr({height: 32, width: 32}))
                    .attr('title','-').qtip(game.render.html.qtip.ingame('top', {
                        render: function(event,api) {
                            var content = $(this).find('.qtip-content').empty()
                                .append($('<b />').addClass('header').text(v.name))
                                .append($('<div />').text(<?=__j('Dein Wohnmobil ist schon etwas betagt... und war auch nie für eine wilde Flucht vor Zombies auf schlecht befestigten Straßen vorgesehen. Früher oder später wirst du anhalten und Reparaturen vornehmen müssen.')?>))
                                .append('<span class="separator" />')
                                .append($('<div />').addClass('center').text(<?=__j('Zustand')?> + ': ' + v.count + ' / ' + v.max));

                            if (v.max == v.count) content.append($('<div />').text(<?=__j('Hier muss im Moment nichts repariert werden.')?>));
                            else if (data.speed == 0) {
                                var row;
                                content.append(row = $('<div />').addClass('row'));
                                $.each([1,2,5,10], function(k,i) {
                                    row.append($('<div />').addClass('cell rw-3 smallpad').append(
                                        $('<div />').addClass('btn').append($('<i />').addClass('fa fa-wrench')).append($('<span />').text(' x ' + i)).click(function() {
                                            core.command('location/caravan', {'do': 'repair', addr: v.addr, count: i});
                                        })
                                    ))
                                })
                            } else content.append($('<div />').text(<?=__j('Während der Fahrt kannst du keine Reparaturen vornehmen!')?>))
                        }
                    }))
            );

            var status = v.count/v.max;
            var color;

            if (status >= 1) color = '#27A6F5';
            else if (status > 0.9) color = '#56E314';
            else if (status > 0.75) color = '#F0CC00';
            else if (status > 0.5)  color = '#FF9100';
            else if (status > 0) color = '#D60000';
            else color = '#750000';

            var gauge = new Donut(canvas[0]).setOptions({
                lines: 12, angle: 0.1, lineWidth: 0.1, limitMax: 'false', colorStart: color, strokeColor: '#000000', generateGradient: true
            });
            gauge.maxValue = v.max;
            gauge.animationSpeed = 1;
            gauge.set(Math.max(0.00000001,v.count));
        });

        var canvas;
        main_gauge.append($('<div />').addClass('margin').css({position: 'relative', height: 70, width: 110, display: 'inline-block'})
                .append(canvas = $('<canvas />').attr({height: 70, width: 110}).css({position: 'absolute', left: 0, top: 0, 'z-index': 2}))
                .append($('<div />').addClass('center b small').css({position: 'absolute', left: 0, top: 30, width: '100%', 'z-index': 1}).text(Math.round(data.speed) + ' km/h'))
                .attr('title','-').qtip(game.render.html.qtip.ingame('top', {
                    render: function(event,api) {
                        var content = $(this).find('.qtip-content').empty()
                            .append($('<b />').addClass('header').text(<?=__j('Amaturenbrett')?>))
                            .append($('<div />').text(<?=__j('Hier siehst du, wie weit du schon gekommen bist. Um Punkte zu sammeln musst du so weit wie möglich fahren.')?>))
                            .append('<span class="separator" />')
                            .append($('<div />').text(game.i18n(<?=__j('Du bist bereits :distance km gefahren und hast :breaks Städte aufgesucht.')?>, {':distance': Math.round(data.distance), ':breaks': data.stops})));
                    }
                }))
        );
        var gauge = new Gauge(canvas[0]).setOptions({
            lines: 12, angle: 0.1, lineWidth: 0.2, limitMax: 'false', percentColors: [[0.0, "#D60000" ], [0.61, "#56E314"], [1.0, "#27A6F5"]], strokeColor: '#000000', generateGradient: true,
            pointer: {
                length: 0.5,
                strokeWidth: 0.035,
                color: '#FFF6BF'
            }
        });
        gauge.maxValue = 180;
        gauge.animationSpeed = 1;
        gauge.set(Math.max(0.00000001,data.speed));

        if (data.speed == 0) {
            data_space.append($('<div />').addClass('btn btn-zv btn-zv-skinned-hero').text(<?=__j('Losfahren')?>).click(function() {
                if ((data.stops > 0 || confirm(<?=__j('Sobald du losgefahren bist, können keine weiteren Spieler deiner Partie beitreten. Fortfahren?')?>)) && confirm(<?=__j('Denk daran: Du kannst nicht wieder hierher zurückkehren. Wenn du jetzt losfährst verlierst du alle Gegenstände, die sich außerhalb des Wohnwagens befinden. Wenn du andere Spieler zurücklässt, werden sie einsam in der Wildniss sterben. Wirklich losfahren?')?>))
                    core.command('location/caravan', {'do': 'go'});
            }));
        } else {
            data_space.append($('<div />').addClass('btn btn-zv btn-zv-skinned-hero').text(<?=__j('Nächste Stadt suchen')?>).click(function() {
                if (confirm(<?=__j('Möchtest du wirklich anhalten?')?>))
                    core.command('location/caravan', {'do': 'stop'});
            }));
            data_space.append($('<div />').addClass('btn btn-zv btn-zv-skinned-hero').text(<?=__j('Zwischenstop einlegen')?>).click(function() {
                if (confirm(<?=__j('Möchtest du wirklich anhalten?')?>))
                    core.command('location/caravan', {'do': 'break'});
            }));
        }
    };

    var hideoutstats = function(data, target) {
        var repair, defense, deco;
        $(target).empty()
        .append(
            $('<div />').addClass('cell rw-4 padded').append(
                deco = $('<div />').addClass('widget')
            )
        ).append(
            $('<div />').addClass('cell rw-4 padded').append(
                repair = $('<div />').addClass('widget')
            )
        ).append(
            $('<div />').addClass('cell rw-4 padded').append(
                defense = $('<div />').addClass('widget')
            )
        );

        deco
            .append($('<img />').attr('src', 'media/icons/deco.gif'))
            .append($('<span />').text(data.deco[0] + data.deco[1] + data.deco[2] + data.deco[3]))
            .attr('title', '-')
            .qtip(game.render.html.qtip.ingame('top', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty();

                    content
                        .append($('<span />').text(<?=__j('Ein hübsch eingerichtetes Versteck reduziert die Chance, dass plötzlich ein RTL-Messie-Kamerateam (oder Tine Wittler) vor deiner Tür steht. So fühlst du dich direkt viel wohler.')?>))
                        .append($('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-9 padded right').text(<?=__j('Grundwert')?>))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[0] < 0 ? 'negative' : (data.deco[0] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[0]))
                            .append($('<div />').addClass('cell rw-9 padded right').text(<?=__j('Zustand des Verstecks')?>))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[1] < 0 ? 'negative' : (data.deco[1] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[1]))
                            .append($('<div />').addClass('cell rw-9 padded right').text(<?=__j('Verbesserungen')?>))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[2] < 0 ? 'negative' : (data.deco[2] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[2]))
                            .append($('<div />').addClass('cell rw-9 padded right').text(<?=__j('Gegenstände')?>))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[3] < 0 ? 'negative' : (data.deco[3] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[3]))
                        );
                }
            }));

        repair
            .append($('<img />').attr('src', 'media/icons/decay.gif'))
            .append($('<span />').text(data.state + '%'))
            .attr('title', <?=__j('Dein Versteck ist eine ziemliche Bruchbude - vermutlich hast du beim Bau nicht mal gängige Normen eingehalten. Tja, deswegen musst du dich nun mit Verfall herumschlagen. Mit der Zeit wird sich der Zustand deines Verstecks verschlechtern, wodurch die Hausverteidigung sinkt.')?>)
            .qtip(game.render.html.qtip.ingame('top'));

        defense
            .append($('<img />').attr('src', 'media/icons/defense.gif'))
            .append($('<span />').text(data.defense == data.max_defense ? data.defense : (data.defense + '/' + data.max_defense)))
            .attr('title', <?=__j('Die Hausverteidigung gibt an, wie vielen Zombies dein Versteck bei einer Belagerung standhalten kann. Wird dein Versteck von mehr Zombies belagert, so können diese deine Verteidigung durchbrechen und dich angreifen!')?>)
            .qtip(game.render.html.qtip.ingame('top'));
    };

    var zombieradar = function(data, target) {

        // Create danger text
        var danger_text, zombie_text;
        switch (data.danger) {
            case 0:             danger_text = <?=__j('Sicher')?>; break;
            case 1:             danger_text = <?=__j('Geringe Gefahr')?>; break;
            case 2:             danger_text = <?=__j('Moderate Gefahr')?>; break;
            case 3:             danger_text = <?=__j('Beträchtliche Gefahr')?>; break;
            case 4:             danger_text = <?=__j('Hohe Gefahr')?>; break;
            case 5: default:    danger_text = <?=__j('Sehr hohe Gefahr!')?>; break;
        }

        // Create zombie count
        if (data.zombies == 0)
            zombie_text = <?=__j('Keine Zombies')?>;
        else if (data.zombies == 1)
            zombie_text = <?=__j('1 Zombie')?>;
        else zombie_text = <?=__j(':num Zombies')?>;

        var radar, siege;
        $(target).empty().append(
            $('<div />').addClass('cell rw-6 padded').append(
                radar = $('<div />').addClass('widget radar alert-' + data.danger).text(danger_text)
            )
        ).append(
            $('<div />').addClass('cell rw-6 padded').append(
                siege = $('<div />').addClass('widget siege alert-' + (data.zombies > 0 ? '4' : '0')).text(game.i18n(zombie_text, {':num': data.zombies}))
            )
        );

        var tooltip = [];
        if (data.prop == 0 && !data.hideout) tooltip.push(<?=__j('Hier musst du vorerst keine Angst unerwarteten Angriffen haben.')?>);
        else if (data.prop == 0 && data.hideout) tooltip.push(<?=__j('Du bist hier so lange sicher, wie dein Versteck den Zombies widerstehen kann.')?>);
        else {
            tooltip.push(<?=__j('Dein Gespühr sagt dir, dass du auf Zombie-Gruppen mit einer Größe von bis zu :max Zombies gefasst sein solltest.')?>);
            tooltip.push(<?=__j('Rechne damit, etwa alle :pc_min Minuten auf Zombies zu treffen.')?>);
        }
        if (!data.hideout)
            if (data.inc == 0) tooltip.push(<?=__j('Die Zombies haben hier keine Gelegenheit, dir den Weg zu versperren.')?>);
            else tooltip.push(<?=__j('Die Zombies könnten sich hier versammeln und dir den Fluchtweg abschneiden... So wies aussieht würden sie dafür vermutlich um die :sg_min Minuten benötigen.')?>);
        else
            if (data.inc == 0) tooltip.push(<?=__j('Dieses Versteck werden die Zombies niemals finden!')?>);
            else tooltip.push(<?=__j('Es ist nur eine Frage der Zeit, bis dieses Versteck von Zombies umstellt wird. So wies aussieht würden sie dafür vermutlich um die :sg_min Minuten benötigen.')?>);

        radar.attr('title',
            game.i18n(tooltip.join('<br /><br />'), {':min': '<b>' + data.min + '</b>',':max': '<b>' + data.max + '</b>',':pc_min': '<b>' + data.prop + '</b>',':sg_min': '<b>' + data.inc + '</b>'})
        ).qtip(game.render.html.qtip.ingame('top'));

        siege.attr('title','-').qtip(game.render.html.qtip.ingame('top',{
            render: function(event,api) {
                var content = $(this).find('.qtip-content').empty();
                if (data.zombies == 0)
                    content.append(<?=__j('Es sieht so aus, als könntest du diesen Ort momentan ohne Probleme verlassen. Du solltest trotzdem regelmäßig nachschauen, ob Zombies eventuell den Weg blockieren.')?>);
                else {
                    var fight, flee;
                    if (data.hideout)
                        content.append(<?=__j('Die Zombies haben dein Versteck aufgespürt. Von hier kannst du nicht mehr fliehen - du musst die Zombies bekämpfen!')?>);
                    else content.append(<?=__j('Es geht weder vor noch zurück - Zombies blockieren den Ausgang! Du kannst entweder eine waghalsige Flucht versuchen oder den Weg freizuräumen. Eins steht fest: Von alleine werden diese Zombies hier nicht verschwinden...')?>);

                    content
                        .append('<br /><br />')
                        .append(core.snippets.button(<?=__j('Weg freikämpfen')?>, function() {
                            api.hide();
                            core.command('location/fight');
                        }))
                        .append(core.snippets.button(<?=__j('Fluchtversuch')?>, function() {
                            api.hide();
                            core.command('location/flee');
                        }).addClass(data.hideout ? 'disabled' : ''));
                }
                return true;
            }
        }));
    };

    core.parts.location = function(data, target) {
        var zradar, hideout, actions, spc_colosseum, spc_scout, spc_roadtrip, desc;

        $(target).empty().addClass('row location_box ' + (data.meta.outside ? 'outside' : 'inside')).append(
            $('<h2 />').text(data.meta.name)
        ).append(
            $('<div />').addClass('cell rw-6 padded').append(
                zradar = $('<div />').addClass('row')
            ).append(
                hideout = data.hideout ? $('<div />').addClass('row') : null
            ).append(
                spc_colosseum = data.colosseum ? $('<div />').addClass('row') : null
            ).append(
                spc_scout = data.scouting ? $('<div />').addClass('row') : null
            ).append(
                spc_roadtrip = data.caravan ? $('<div />').addClass('row') : null
            ).append(
                actions = $('<div />').addClass('row')
            )
        ).append(
            desc = $('<div />').addClass('cell rw-6 padded justify').text(data.meta.desc)
        );

        if (core.last.players) {
            var area = [];
            $.each(core.last.players.others, function(id, player) {
                if (player.local) area.push(player.name);
            });

            if (area.length) {
                var p = $('<p />').appendTo(desc).attr('title',<?=__j('Hier siehst du Spieler, die sich momentan in deiner Nähe befinden. Um mehr Details zu erfahren, klicke "Spielerübersicht".')?>).qtip(game.render.html.qtip.ingame('bottom'));
                $.each(area, function(k,name) {
                    p.append($('<span />').addClass('inline-player').text(name));
                });

            }
        }

        $.each(data.actions, function(k,v) {
            actions.append(
                $('<div />').addClass('cell rw-6 padded justify').append(core.snippets.button(v, null, 'tooltip'))
            )
        });

        actions.append(
            $('<div />').addClass('cell padded justify rw-' + (data.doorways ? '10' : '12')).append(core.snippets.button(<?=__j('Karte');?>, function() {
                core.popup.map();
            }))
        );

        if (data.doorways) {
            actions.append(
                $('<div />').addClass('cell padded justify rw-2').append(
                    $('<div />').addClass('btn').append($('<i>').addClass('fa fa-sign-in')).append('&nbsp;').click(function() {
                        var esc_popup = core.popup.spawn(400);

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text(<?=__j('Ort wechseln')?>));

                        esc_popup.append(
                            $('<div />').addClass('row').append(title = $('<div />').addClass('cell rw-12 padded').text(<?=__j('Du kannst von diesem Ort aus einen anderen Teil der Spielwelt betreten.')?>))
                        );

                        var destination = $('<select />');
                        $.each(data.doorways, function(id, meta) {
                            $('<option />').attr('value', id).text(meta.name == meta.location ? meta.name : (meta.name + ' (' + meta.location + ')')).appendTo(destination);
                        });

                        esc_popup.append(
                            $('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j("Wo soll's denn hingehen?")?>)))
                        ).append(
                            $('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append(destination))
                        );
                        destination.selectric();

                        if (core.last.players) {

                            var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                            if (core.last.players.others)
                                $.each(core.last.players.others, function(id, player) {
                                    if (player.escort)
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
                                            .append($('<option />').val('1').text(<?=__j('Mitgehen')?>))
                                            .append($('<option />').val('0').text(<?=__j('Die Stellung halten')?>))
                                            .val('1')
                                    ));

                                bhav.selectric();
                                check_row.find(':checkbox').customRadioCheck();

                            }
                        }

                        esc_popup.append($('<div />').addClass('row')
                                .append($('<div />').addClass('cell rw-8 padded').append(
                                    $('<div />').addClass('btn').text(<?=__j('Los gehts!')?>).addClass(data.radar.zombies > 0 ? 'disabled' : '').click(function() {

                                        var cfg = {to: destination.val(), follow: 1};
                                        if (core.last.players && check_row.children().length) {
                                            cfg.follow = parseInt(bhav.val()) > 0 ? 1 : 0;
                                            cfg.support = parseInt(bhav.val()) == 2 ? 1 : 0;
                                            cfg.co = [];
                                            $.each(check_row.find(':checkbox:checked'), function() {
                                                cfg.co.push($(this).attr('data-id'))
                                            })
                                        }

                                        esc_popup.addClass('disabled');
                                        core.command('map/go', cfg, true, function(data) {
                                            esc_popup.removeClass('disabled');
                                            if (data.success) {
                                                esc_popup.trigger('unpop');
                                                core.command();
                                            }
                                        });
                                    })))
                                .append($('<div />').addClass('cell rw-4 padded').append(
                                    $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(function() {
                                        esc_popup.trigger('unpop');
                                    })))
                        );
                    })
                )
            );
        }

        zombieradar(data.radar, zradar);
        if (hideout)
            hideoutstats(data.hideout, hideout);
        if (data.colosseum)
            colosseum(data.colosseum, spc_colosseum);
        if (data.scouting)
            scoutmode(data.scouting, spc_scout);
        if (data.caravan)
            roadtrip(data.caravan, spc_roadtrip)
    };
})();