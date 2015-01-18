(function() {
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
        var zradar, actions;

        $(target).empty().addClass('row location_box ' + (data.meta.outside ? 'outside' : 'inside')).append(
            $('<h2 />').text(data.meta.name)
        ).append(
            $('<div />').addClass('cell rw-6 padded').append(
                zradar = $('<div />').addClass('row')
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
            $('<div />').addClass('cell rw-12 padded justify').append(core.snippets.button(<?=__j('Karte');?>, function() {
                core.popup.map();
            }))
        );

        zombieradar(data.radar, zradar);

    };
})();