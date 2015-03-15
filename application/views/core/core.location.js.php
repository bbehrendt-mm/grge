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
        console.log(data);
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
            .append($('<span />').text(data.deco == data.max_deco ? data.deco : (data.deco + '/' + data.max_deco)))
            .attr('title', <?=__j('Ein hübsch eingerichtetes Versteck reduziert die Chance, dass plötzlich ein RTL-Messie-Kamerateam (oder Tine Wittler) vor deiner Tür steht. So fühlst du dich direkt viel wohler.')?>)
            .qtip(game.render.html.qtip.ingame('top'));

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
        var zradar, hideout, actions, spc_colosseum, spc_scout;

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
                actions = $('<div />').addClass('row')
            )
        ).append(
            $('<div />').addClass('cell rw-6 padded justify').text(data.meta.desc)
        );

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
                    $('<div />').addClass('btn').append($('<i>').addClass('fa fa-sign-in')).append('&nbsp;')
                )
            );
        }

        zombieradar(data.radar, zradar);
        if (hideout)
            hideoutstats(data.hideout, hideout);
        if (data.colosseum)
            colosseum(data.colosseum, spc_colosseum)
        if (data.scouting)
            scoutmode(data.scouting, spc_scout)
    };
})();