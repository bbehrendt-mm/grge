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
                                content.append(row = NF.row());
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
                        .append(NF.row()
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
        ).qtip(game.render.html.qtip.ingame('bottom'));

        siege.attr('title','-').qtip(game.render.html.qtip.ingame('bottom',{
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

    var locationradar = function(data, target) {
        $(target).empty().append(
            $('<div />').addClass('cell rw-12 padded').append(

                $('<div />').addClass('widget radar').append(
                    $('<span />').text(<?=__j('Erkundungsrate')?>)
                ).append(
                    $('<div />').addClass('discoverybar').append($('<div />').css('width', data + '%'))
                )
                    .attr('title', '-')
                    .qtip(game.render.html.qtip.ingame('top', {
                        render: function(ev,api) {
                            var content = $(this).find('.qtip-content').empty()
                                .append($('<span />').text(data >= 100 ? <?=__j('Du hast diesen Ort vollständig ausgekundschaftet - von hier aus wirst du keine neuen Ruinen entdecken können.')?> : <?=__j('Du bist momentan auf der Suche nach neuen Orten. Jedes mal, wenn der Ereigniscountdown abläuft, hast du die Chance einen neuen Ort zu entdecken.')?>))
                            if (data <= 100)
                                content
                                    .append($('<span />').addClass('separator'))
                                    .append($('<div />').addClass('center').text(game.i18n(<?=__j('Aktueller Wert: :num')?>, {':num': Math.round(data) + '%'})))
                        }
                    }))
            )
        );
    };

    var garden = function(data, target) {
        var content;
        target.append($('<div />').addClass('cell rw-12 widget epic padded').append(content = NF.row().append($('<h3 />').text(<?=__j('Kleines Gewächshaus')?>))));

        if (!data.planted)
            content.append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j('Das Beet ist momentan leer.')?>)));
        else {

            var bar_growth, bar_quality, bar_water, bar_fert;

            content
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text(<?=__j('Fortschritt')?>)).append($('<br />')).append(bar_growth = $('<div />').addClass('gardenbar growth').append($('<div />').css('width', (data.harvest * 100) + '%'))))
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text(<?=__j('Qualität')?>)).append($('<br />')).append(bar_quality = $('<div />').addClass('gardenbar quality').append($('<div />').css('width', (data.quality * 100) + '%'))))
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text(<?=__j('Wasser')?>)).append($('<br />')).append(bar_water = $('<div />').addClass('gardenbar water').addClass(data.time_water ? '' : 'dry').append($('<div />').css('width', (data.water * 100) + '%'))))
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text(<?=__j('Dünger')?>)).append($('<br />')).append(bar_fert = $('<div />').addClass('gardenbar fertilizer').append($('<div />').css('width', (data.fertilizer * 100) + '%'))))
            ;

            var q;
            if          (data.quality >= 1.00)  q = <?=__j('Excellent')?>;
            else if     (data.quality >= 0.90)  q = <?=__j('Ausgezeichnet')?>;
            else if     (data.quality >= 0.80)  q = <?=__j('Sehr gut')?>;
            else if     (data.quality >= 0.65)  q = <?=__j('Gut')?>;
            else if     (data.quality >= 0.50)  q = <?=__j('Durchschnittlich')?>;
            else if     (data.quality >= 0.35)  q = <?=__j('Verbesserungswürdig')?>;
            else if     (data.quality >= 0.20)  q = <?=__j('Schlecht')?>;
            else if     (data.quality >= 0.10)  q = <?=__j('Sehr schlecht')?>;
            else                                q = <?=__j('Unbrauchbar')?>;

            bar_growth.attr('title', game.i18n(data.time === false ? <?=__j('Deine Pflanzen sind erntebereit!')?> : <?=__j('Deine Pflanzen sind in :time erntebereit!')?>, {':time': '<b>' + data.time + '</b>'})).qtip(game.render.html.qtip.ingame('top'));
            bar_quality.attr('title', game.i18n(<?=__j('Die Qualität bestimmt die Anzahl der Früche, die du bei der Ernte erhalten wirst. Derzeit zeichnet sich folgende Qualität ab: :quality')?>, {':quality': '<b>' + q + '</b>'})).qtip(game.render.html.qtip.ingame('top'));
            bar_water.attr('title', <?=__j('Wenn der Wasservorrat deiner Pflanzen aufgebraucht ist, musst du neues nachfüllen. Warte damit nicht zu lange, andernfalls sinkt die Erntequalität.')?> + ' ' + game.i18n(data.time_water ? <?=__j('Du kannst in :time neues Wasser hinzufügen.')?> : (data.time_water2 ? <?=__j('Wenn du bis in :time kein neues Wasser hinzugefügt hast, wird die Erntequalität abnehmen!')?> : ('<b>' + <?=__j('Deine Pflanzen verdorren! Füge schnell neues Wasser hinzu!')?> + '</b>')), {':time': '<b>' + (data.time_water || data.time_water2) + '</b>'})).qtip(game.render.html.qtip.ingame('top'));
            bar_fert.attr('title', <?=__j('Die Stärke deiner Düngung bestimmt die Höhe der Effekte der geernteten Pflanzen. Wenn du nach Erreichen der maximalen Düngestärke noch weiter düngst, hat dies nur noch Einfluss auf die Art der Effekte, nicht jedoch deren Höhe.')?>).qtip(game.render.html.qtip.ingame('top'));
        }

        $.each(data.actions, function(k,v) {
            if (v.flags.as !== 'fertilize')
                content.append(
                    $('<div />').addClass(game.touch() ? 'cell rw-12 padded justify' : 'cell rw-6 rw-sm-12 padded justify').append(core.snippets.button(v, null, 'tooltip'))
                )
        });

        var ft_row;
        content.append(ft_row = $('<div />').addClass('cell rw-12 padded center'));

        $.each(data.actions, function(k,v) {
            if (v.flags.as === 'fertilize') {
                var f_btn;
                ft_row.append(
                    f_btn = $('<div />').addClass('btn btn-zv btn-zv-skinned-epic small')
                );

                $.each(v.requires, function(rid, rq) {
                    f_btn.append($('<span />').addClass('group').append($('<img />').attr('src','media/icons/' + rq.icon + '.gif')).append(rq.value != 1 ? $('<span />').text(rq.value) : null))
                });

                f_btn.click(function() {
                    if (confirm(<?=__j('Bist du sicher, dass du diese Gegenstände einsetzen möchtest, um die Pflanzen zu düngen?')?>))
                        core.command('act/item', {action: v.action, item: v.target});
                }).attr('title', <?=__j('Welchen Effekt deine geernteten Pflanzen haben hängt davon ab, womit du sie düngst. Nahrung macht sie saftiger, Drogen geben ihnen einen heilenden Effekt und Chemikalien lassen sie aufputschend wirken.')?> + '<br /><br />' + v.tooltip).qtip(game.render.html.qtip.ingame('bottom'));
            }
        });
    };

    var raven = function(data, target) {
        var content;
        target.append($('<div />').addClass('cell rw-12 widget epic padded').append(content = NF.row().append($('<h3 />').text(<?=__j('Raben-Bootcamp')?>))));

        if (data.time)
            content.append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(game.i18n(<?=__j('Der Rabe muss sich noch :time ausruhen.')?>,{':time': data.time}))));

        var fetch_btn;
        $.each(data.actions, function(k,v) {
            if (v.flags.as !== 'fetch')
                content.append(
                    $('<div />').addClass(game.touch() ? 'cell rw-12 padded justify' : 'cell rw-6 rw-sm-12 padded justify').append(core.snippets.button(v, null, 'tooltip'))
                );
            else fetch_btn = core.snippets.button(v, null, 'none');
        });

        if (fetch_btn) content.append($('<div />').addClass(game.touch() ? 'cell rw-12 padded justify' : 'cell rw-6 rw-sm-12 padded justify').append(fetch_btn.clone(false).click(function() {

            var popup = core.popup.spawn({desktop: 600, md: '100%'});
            var select, food;

            popup.append(
                $('<h2 />').addClass('center').text(<?=__j('Zielgebiet auswählen')?>)
            ).append(
                NF.row().append($('<div />').addClass('cell rw-12 padded').append(
                    $('<div />').addClass('note').text(game.i18n(<?=__j('Hier kannst du auswählen, wie weit der Rabe fliegen soll, um eine Ruine auszuwählen. Für eine größere Distanz musst du selbstverständlich mehr Futter springen lassen. Der Rabe wird zufällig eine Ruine (die kein Aussichtspunkt und auch kein Versteck ist) in dem gewählten Bereich auswählen und dort dreimal nach Gegenständen suchen. Gefundene Gegenstände wird er zu dir bringen, zumindest so lange er sie tragen kann. Falls er nichts findet oder die gefundenen Gegenstände ihn nicht auslasten, wird er Gegenstände vom Boden aufheben. Der Rabe kann nicht mehr als :capacity Gegenstände mit einem Gesamtgewicht von :size tragen!')?>,{':size': data.size, ':capacity': data.capacity}))
                )).append($('<div />').addClass('cell rw-12 padded').append(
                    select = $('<select />').addClass('form_input').change(function() {
                        food.empty().attr('title', <?=__j('Gewöhnliche Nahrung')?>).append($('<img />').attr('src','media/icons/items/basefood/generic.gif')).append($('<span />').text(' x ' + Math.max(1,$(this).val() * 2))).qtip(game.render.html.qtip.ingame('bottom'));
                    })
                        .append($('<option />').attr('value',0).text(game.i18n(<?=__j('Nähere Umgebung (Distanz bis :m2)')?>,{':m1': 0, ':m2': 15})))
                        .append($('<option />').attr('value',1).text(game.i18n(<?=__j('Entfernte Regionen (Distanz zwischen :m1 und :m2)')?>,{':m1': 16, ':m2': 50})))
                        .append($('<option />').attr('value',2).text(game.i18n(<?=__j('Arsch der Welt (Distanz über :m1)')?>,{':m1': 51, ':m2': 900})))
                ))
            ).append(
                NF.row()
                    .append($('<div />').addClass('cell rw-10 rw-sm-12 padded').text(<?=__j('Für die gewählte Distanz benötigt der Rabe folgendes Futter:')?>))
                    .append(food = $('<div />').addClass('cell rw-2 rw-sm-12 padded'))
            ).append(
                NF.row()
                    .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(function() {
                        popup.trigger('unpop');
                    })))
                    .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<div />').addClass('btn').text(<?=__j('Raben aussenden')?>).click(function() {
                        popup.trigger('unpop');
                        fetch_btn.trigger('click', [select.val()]);
                    })))
            );

            select.trigger('change').selectric();

        })));
    };

    var fence = function(data, target) {
        var content;
        target.append($('<div />').addClass('cell rw-12 widget epic padded').append(content = NF.row().append($('<h3 />').text(<?=__j('Laserzaun')?>))));

        content.append(NF.row()
                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded center').append($('<img />').attr('src', 'media/icons/defense.gif')).append($('<span />').addClass('margin-left').text(data.status ? '∞' : '0')).attr('title', <?=__j('Die durch den Laserzaun zusätzlich generierte Verteidigung wird auf die Hausverteidigung addiert.')?>).qtip(game.render.html.qtip.ingame('top')))
                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded center').append($('<img />').attr('src', 'media/icons/items/energy.gif')).append($('<span />').addClass('margin-left').text(data.energy)).attr('title', <?=__j('Zeigt die Menge an Energie an, die deinem Versteck momentan zur Verfügung steht. Geht die Energie zur Neige, solltest du mit dem Generator neue erzeugen.')?>).qtip(game.render.html.qtip.ingame('top')))
                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded center').append($('<img />').attr('src', 'media/icons/clock.gif')).append($('<span />').addClass('margin-left').text(data.time ? data.time : '---')).attr('title', <?=__j('Dies ist die Zeit, die der Laserzaun mit deinem aktuellen Energievorrat noch laufen kann, bevor er wegen Energiemangel automatisch heruntergefahren wird.')?>).qtip(game.render.html.qtip.ingame('top')))
        );

        $.each(data.actions, function(k,v) {
            content.append(
                $('<div />').addClass(game.touch() ? 'cell rw-12 padded justify' : 'cell rw-6 rw-sm-12 padded justify').append(core.snippets.button(v, null, 'tooltip'))
            );
        });
    };

    core.parts.location = function(data, target) {
        var zradar, hideout, actions, spc_colosseum, spc_scout, spc_roadtrip, desc, epic, lradar;

        $(target).empty().addClass('row location_box ' + (data.meta.outside ? 'outside' : 'inside')).append(
            $('<h2 />').text(data.meta.name)
        );

        if (data.lomap) {

            var lomap,mapbg,esc;

            $(target).append(
                lomap = $('<div />').addClass('cell padded rw-4 rw-lg-6 rw-md-12')
            );

            var d = lomap.width();
            var sc = 6;

            lomap
                .append(mapbg = $('<div />').addClass('row lomap').css({height: d, width: d}))
                .append(esc = NF.row().attr('title', <?=__j('Wähle die NPCs aus, die dich begleiten sollen.')?>).qtip(game.render.html.qtip.ingame('top')));
            mapbg.append($('<canvas />').attr({height: d, width: d}));

            var renderer = core.cache_get('minimap_stage');

            if (renderer) renderer.reset(mapbg.find('canvas').get(0));
            else renderer = new core.plugins.Minimap(mapbg.find('canvas').get(0));
            renderer.addEnvironment(0,0, data.lomap.top ? data.lomap.top.id : 0, data.lomap.bottom ? data.lomap.bottom.id : 0, data.lomap.left ? data.lomap.left.id : 0, data.lomap.right ? data.lomap.right.id : 0, data.lomap.current.zombies,  data.lomap.current.players);

            core.cache_put('minimap_stage', renderer);
            var go = function(lid, slidex, slidey) {
                return function() {
                    lomap.find('.navbtn').fadeOut(200);
                    $('#content').addClass('disabled');

                    var nids = [];
                    esc.find(':checkbox').each(function() {
                        if ($(this).prop('checked')) nids.push($(this).attr('data-nid'));
                    });

                    core.command('map/go', {to: lid, follow: 1, co: nids, support: 1}, true, function(data) {
                        if (data.success) {
                            if (data.preview && (slidex != 0 || slidey != 0)) {

                                renderer
                                    .addEnvironment(slidex, slidey, data.preview.top ? data.preview.top.id : 0, data.preview.bottom ? data.preview.bottom.id : 0, data.preview.left ? data.preview.left.id : 0, data.preview.right ? data.preview.right.id : 0, data.preview.current.zombies,  data.preview.current.players)
                                    .shift(slidex, slidey, 1000, function () {
                                        core.command(null, null, true, null, false, function() {$('#content').removeClass('disabled');});
                                    });
                            }
                            else {
                                $('#content').removeClass('disabled');
                                core.command();
                            }
                        } else {
                            $('#content').removeClass('disabled');
                            lomap.find('.navbtn').fadeIn(200);
                        }
                    });
                }
            };

            $.each(data.lomap.current.npcs, function(k, nid) {
                esc.append(NF.cell(true, 12)
                    .append(
                        $('<label />').attr('for', 'lomap_esc_' + nid).text(core.last.players.others[nid].name)
                            .prepend($('<input />').attr({type: 'checkbox', id: 'lomap_esc_' + nid, checked: 'checked', 'data-nid': nid}))
                    )
                );
            });
            esc.find(':checkbox').customRadioCheck();

            if (data.lomap.left)    mapbg.append($('<div />').click(go(data.lomap.left.id, -1, 0)).addClass('navbtn nav-left').css({top: d/sc, bottom: d/sc, left: 0, width: d/sc}));
            if (data.lomap.top)     mapbg.append($('<div />').click(go(data.lomap.top.id, 0, -1)).addClass('navbtn nav-top').css({top: 0, right: d/sc, left: d/sc, height: d/sc}));
            if (data.lomap.bottom)  mapbg.append($('<div />').click(go(data.lomap.bottom.id, 0, 1)).addClass('navbtn nav-bottom').css({right: d/sc, bottom: 0, left: d/sc, height: d/sc}));
            if (data.lomap.right)   mapbg.append($('<div />').click(go(data.lomap.right.id, 1, 0)).addClass('navbtn nav-right').css({top: d/sc, right: 0, bottom: d/sc, width: d/sc}));

            if (data.lomap.others) {
                var center;
                mapbg.append(center = $('<div />').addClass('nav-center').css({top: d/sc, right: d/sc, bottom: d/sc, left: d/sc}));
                $.each(data.lomap.others, function(k,v) {
                    center.append($('<div />').click(go(v.id, 0, 0)).addClass('navbtn').text(v.name));
                });
            }


            lomap.find('.navbtn').hide().fadeIn(200);
        }

        $(target).append(
            $('<div />').addClass('rw-12 padded hide-desktop hide-sm').text(data.meta.desc)
        ).append(
            $('<div />').addClass('cell padded').addClass(data.lomap ? 'rw-4 rw-lg-6 rw-md-12' : 'rw-6 rw-lg-12').append(
                zradar = NF.row()
            ).append(
                lradar = data.discovery !== false ? NF.row() : null
            ).append(
                hideout = data.hideout ? NF.row() : null
            ).append(
                spc_colosseum = data.colosseum ? NF.row() : null
            ).append(
                spc_scout = data.scouting ? NF.row() : null
            ).append(
                spc_roadtrip = data.caravan ? NF.row() : null
            ).append(
                actions = NF.row()
            ).append(
                epic = (data.epc_garden || data.epc_raven || data.epc_fence) ? NF.row() : null
            )
        ).append(
            desc = $('<div />').addClass('cell padded justify').addClass(data.lomap ? 'rw-4' : 'rw-6').append($('<span />').addClass('hide-mobile').text(data.meta.desc))
        );

        if (core.last.players) {
            var area = [];
            var area_npc = [];
            $.each(core.last.players.others, function(id, player) {
                if (player.local && !player.loner) {
                    if (player.npc) area_npc.push([player.name,player.icon])
                    else area.push([player.name,player.icon]);
                }
            });

            if (area.length + area_npc.length) {
                var p = $('<p />').appendTo(desc).attr('title',<?=__j('Hier siehst du Spieler, die sich momentan in deiner Nähe befinden. Um mehr Details zu erfahren, klicke "Spielerübersicht".')?>).qtip(game.render.html.qtip.ingame('bottom'));
                $.each(area, function(k,obj) {
                    var name = obj[0];
                    var icon = obj[1];
                    p.append($('<span />').addClass('inline-player').text(name).append(icon ? NF.n('div','player_icon').append(NF.img('media/icons/player/' + icon)) : null));
                });
                $.each(area_npc, function(k,obj) {
                    var name = obj[0];
                    var icon = obj[1];
                    p.append($('<span />').addClass('inline-npc green').text(name).append(icon ? NF.n('div','player_icon').append(NF.img('media/icons/player/' + icon)) : null));
                });

            }
        }

        $.each(data.actions, function(k,v) {
            actions.append(
                $('<div />').addClass(game.touch() ? 'cell rw-12 padded justify' : 'cell rw-6 rw-sm-12 padded justify').append(core.snippets.button(v, null, 'tooltip'))
            )
        });

        if (data.xmasfair) {
            actions.append(
                $('<div />').addClass('cell rw-12 padded justify').append(core.snippets.button(<?=__j('Weihnachtsbaum schmücken')?>, function() {
                    var inner;
                    var popup = core.popup.spawn(515, 772).append($('<div />').css({height: 768, width: 511, background: 'url("media/img/tree.jpg") center/cover no-repeat'}).append(inner = $('<div />').addClass('row')));

                    inner.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append(
                        $('<div />').addClass('note')
                            .append($('<span />').text(<?=__j('Aktueller Dekorationswert:')?> + ' '))
                            .append($('<b />').text(data.xmasfair.deco))
                            .append($('<img />').attr('src', 'media/icons/deco_event.gif'))
                            .append($('<br />'))
                            .append($('<span />').text(<?=__j('Wenn du dir bei der Dekoration des Weihnachtsbaums Mühe gibst, wirst du beim Verlassen des Weihnachtsmarktes BrainCoins sowie ein paar nützliche Geschenke erhalten.')?>))
                    )));

                    $.each(data.xmasfair.tree, function(k, v) {
                        var req;

                        inner.append(
                            $('<div />').addClass('cell rw-6 rw-md-12').append(
                                $('<div />').addClass(v.current[0] >= v.current[1] ? 'hotbox disabled' : 'hotbox')
                                    .append($('<b />').text(v.name).css({'min-height': 54, display: 'block'}))

                                    .append(
                                        $('<div />').addClass('row')
                                            .append($('<div />').addClass('cell rw-6 padded').text(<?=__j('Info')?>))
                                            .append($('<div />').addClass('cell rw-6 padded').text(<?=__j('Erfordert')?>))
                                            .append($('<div />').addClass('cell rw-6 details')
                                                .append(
                                                    $('<div />').addClass('group').append(
                                                        $('<img />').attr('src', v.points > 0 ? 'media/icons/deco_event.gif' : 'media/icons/deco_neutral.gif')
                                                    ).append(
                                                        $('<span />').append($('<b />').text(v.points))
                                                    )
                                                ).append(
                                                    $('<div />').addClass('group').append(
                                                        $('<img />').attr('src', v.current[0] >= v.current[1] ? 'media/icons/lock.gif' : 'media/icons/plus.gif')
                                                    ).append(
                                                        $('<span />').append($('<b />').addClass(v.current[0] < v.current[1] ? 'green' : 'red').text(v.current[0])).append($('<span />').text('/' + v.current[1]))
                                                    )
                                                )
                                            )
                                            .append(req = $('<div />').addClass('cell rw-6 details'))
                                    ).css({opacity: 0, position: 'relative', top: 25}).delay(Math.random() * 1500 + 1250).animate({opacity: 1, top: 0}, 500)
                            ).click(function() {
                                core.command('location/legacy', {'do': 'xmas', 'arg': k});
                                popup.trigger('unpop');
                            })
                        );

                        $.each(v.requires, function(ki,vi) {
                            req.append(
                                $('<div />').addClass('group').append(
                                    vi.icon ? $('<img />').attr('src', 'media/icons/' + vi.icon + '.gif') : $('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')
                                ).append(
                                    $('<span />').append($('<b />').addClass(vi.have >= vi.count ? 'green' : 'red').text(vi.have)).append($('<span />').text('/' + vi.count))
                                )
                            );
                        });
                    });

                }).addClass('purple'))
            )
        }

        actions.append(
            $('<div />').addClass('cell padded justify rw-' + (data.doorways ? (data.lomap ? '8' : '10') : '12')).append(
                data.radar.zombies > 0 ? $('<div />').addClass('note margin-bottom').text(data.hideout ? <?=__j('Zombies blockieren den Weg. Besiege sie, um diesen Ort verlassen zu können.')?> : <?=__j('Zombies blockieren den Weg. Besiege sie oder versuche zu fliehen, um diesen Ort verlassen zu können.')?>) : false
            ).append(core.snippets.button(<?=__j('Karte');?>, function() {
                core.popup.map();
            })).addClass(data.lomap ? 'disabled' : '')
        );

        if (data.doorways) {
            actions.append(
                $('<div />').addClass('cell padded justify').addClass(data.lomap ? 'rw-4' : 'rw-2').append(
                    $('<div />').addClass('btn').append($('<i>').addClass('fa fa-sign-in')).append('&nbsp;').click(function() {
                        var esc_popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text(<?=__j('Ort wechseln')?>));

                        esc_popup.append(
                            NF.row().append(title = $('<div />').addClass('cell rw-12 padded').text(<?=__j('Du kannst von diesem Ort aus einen anderen Teil der Spielwelt betreten.')?>))
                        );

                        if (data.xmasfair)
                            esc_popup.append(
                                $('<div />').addClass('row').append(title = $('<div />').addClass('cell rw-12 padded b text-red').text(<?=__j('Wenn du den Weihnachtsmarkt verlässt, kannst du nicht mehr zurückkehren. Falls du noch über weitere Tickets verfügst, werden diese dich zu anderen Weihnachtsmärkten bringen. Event-Gegenstände werden bei der Reise aus deinem Inventar entfernt. ')?>))
                            );

                        var destination = $('<select />');
                        $.each(data.doorways, function(id, meta) {
                            $('<option />').attr('value', id).text(meta.name == meta.location ? meta.name : (meta.name + ' (' + meta.location + ')')).appendTo(destination);
                        });

                        esc_popup.append(
                            NF.row().append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(<?=__j("Wo soll's denn hingehen?")?>)))
                        ).append(
                            NF.row().append($('<div />').addClass('cell rw-12 padded').append(destination))
                        );
                        destination.selectric();

                        if (core.last.players) {

                            var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                            if (core.last.players.others)
                                $.each(core.last.players.others, function(id, player) {
                                    if (player.local && (player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_MOVE?>]))
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

                        esc_popup.append(NF.row()
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
        if (data.discovery !== false)
            locationradar(data.discovery, lradar);
        if (hideout)
            hideoutstats(data.hideout, hideout);
        if (data.colosseum)
            colosseum(data.colosseum, spc_colosseum);
        if (data.scouting)
            scoutmode(data.scouting, spc_scout);
        if (data.caravan)
            roadtrip(data.caravan, spc_roadtrip);

        if (data.epc_garden)
            garden(data.epc_garden, epic);
        else if (data.epc_raven)
            raven(data.epc_raven, epic);
        else if (data.epc_fence)
            fence(data.epc_fence, epic);
    };
})();