(function() {

    var render_box = function(data, target, rucksack) {
        $(target).empty();

        var cancel = function() {
            $('.inventory_location, .inventory_player').find('li[data-id]').removeClass('disabled marked-target').data('click-override', false);
            game.render.html.hint(false);
        };

        $.each(data, function(k,v) {
            var container = core.snippets.item(false, v.name, v.icon, v.static <= 1 ? v.count : v.static, v.static > 1, true);
            var flags = $.map(v.flags, function(m) {return m;});
            $(target).append(container);

            if (!v.is_water)
                container.click(function() {
                    var o;
                    if (o = $(this).data('click-override'))
                        o(this);
                    else core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: [v.uin]});
                });
            else
                container.click(function() {
                    var o;
                    if (o = $(this).data('click-override'))
                        return o(this);

                    cancel();
                    var items = $('.inventory_location, .inventory_player').find('li[data-id]');
                    var hint = game.render.html.hint(true);
                    hint.append(
                        $('<span />').text(<?=__j('Wähle einen Wasserbehälter aus, in den du die gewählte Ration Wasser hineinschütten willst.')?>)
                    ).append(
                        $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(cancel)
                    );

                    items.each(function() {
                        if ($(this).is('[data-is-fillable="true"]')) {
                            $(this).addClass('marked-target');
                            $(this).data('click-override', function(o) {
                                core.command('act/inventory',{action: 'fill', items: [v.uin, $(o).data('id')]});
                                cancel();
                            });

                        } else $(this).addClass('disabled');
                    })
                });

            container.attr('data-id', v.uin);
            if (v.fill)
                container.attr('data-is-fillable', v.count < v.fill.capacity);
            if (v.is_water || (v.fill && !v.fill.fixed && v.count > 0))
                container.attr('data-is-spillable', true);

            var notes = [];
            $.each(flags, function(k,v) {
                switch (v) {
                    case 'equipped':
                        container.addClass('equipped');
                        break;
                    case 'armor':
                        notes.push(<?=__j('Dies ist eine Rüstung. Sie wendet während eines Kampfes Schaden von dir ab.')?>);
                        break;
                    case 'weapon':
                        notes.push(<?=__j('Dies ist eine Waffe. Sie wird automatisch eingesetzt wenn du gegen Zombies kämpfst.')?>);
                        break;
                    case 'escape':
                        notes.push(<?=__j('Dieser Gegenstand hilft dir dabei, vor Zombies zu fliehen die dich Belagern. Er wird automatisch bei Bedarf eingesetzt.')?>);
                        break;
                    case 'temp':
                        container.addClass('temp');
                        notes.push(<?=__j('Dieser Gegenstand verschwindet, wenn du ihn zurücklässt.')?>);
                        break;
                    case 'event':
                        container.addClass('event');
                        notes.push(<?=__j('Dies ist ein Event-Gegenstand. Er verschwindet, wenn du das Event-Gebiet verlässt oder das Event endet.')?>);
                        break;
                    case 'carrier':
                        notes.push(<?=__j('Du kannst diesen Gegenstand mitführen, ohne dass dein Rucksack belastet wird.')?>);
                        break;
                }
            });

            container.attr('title','-').qtip(game.render.html.qtip.ingame('bottom',{
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty();

                    content.append(
                        $('<b />').addClass('header').text(v.name)
                    );

                    var subhead = $();
                    if (v.label)  subhead = subhead.add($('<i />').addClass('note center').text(v.label));
                    if (v.count && !v.fill)  subhead = subhead.add($('<i />').addClass('note center').text(v.count + (v.capacity ? (' / ' + v.capacity + ' ') : ' ' ) + v.stack));
                    if (v.weight) subhead = subhead.add($('<i />').addClass('note center').text(<?=__j('Gewicht')?> + ': ' + v.weight));

                    if (subhead.size())
                        content.append(subhead).append('<span class="separator" />');

                    if (v.is_chem) {
                        content.append(
                            $('<i />').addClass('note justify').text(<?=__j('Du kannst diese Chemikalie mit beliebigen anderen Gegenständen kombinieren. Welchen Effekt das hat... das wirst du selbst herausfinden müssen.')?>)
                        ).append(
                            $('<div />').addClass('btn').text(<?=__j('Experimentieren ...')?>).click(function() {
                                container.qtip().hide();

                                cancel();
                                var items = $('.inventory_location, .inventory_player').find('li[data-id]');
                                var hint = game.render.html.hint(true);
                                hint.append(
                                    $('<span />').text(<?=__j('Wähle einen Gegenstand, mit dem du die Chemikalie verbinden möchtest.')?>)
                                ).append(
                                    $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(cancel)
                                );

                                items.each(function() {
                                    if ($(this).data('id') != v.uin || v.static > 1) {
                                        $(this).addClass('marked-target');
                                        $(this).data('click-override', function(o) {
                                            core.command('act/inventory',{action: 'mix', items: [v.uin, $(o).data('id')]});
                                            cancel();
                                        });

                                    } else $(this).addClass('disabled');
                                })
                            })
                        ).append('<span class="separator" />').append('<span />');
                    }

                    if (v.fill) {
                        var fillbox, i;
                        content.append(
                            fillbox = $('<div />').addClass('center')
                        ).append($('<i />').addClass('note justify').text(<?=__j('Klicke einen leeren Slot an, um Wasser aus einer anderen Quelle hinzuzugeben. Klicke einen gefüllten Slot an, um Wasser auszuschütten. Schwarz gefärbte Slots können nicht ausgeleert werden.')?>));
                        for (i = 0; i < v.count; i++)
                            fillbox.append($('<div />').addClass('fillbox fillbox-filled ' + (v.fill.fixed ? 'fillbox-fixed' : '')).click(function() {
                                if (!v.fill.fixed)
                                    core.command('act/inventory',{action: 'spill', items: [v.uin]});
                            }));
                        for (i = v.count; i < v.fill.capacity; i++)
                            fillbox.append($('<div />').addClass('fillbox pointer').click(function() {
                                container.qtip().hide();

                                cancel();
                                var items = $('.inventory_location, .inventory_player').find('li[data-id]');
                                var hint = game.render.html.hint(true);
                                hint.append(
                                    $('<span />').text(<?=__j('Wähle eine Flüssigkeit oder einen anderen Behälter aus, um diesen Behälter zu füllen.')?>)
                                ).append(
                                    $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(cancel)
                                );

                                items.each(function() {
                                    if ($(this).is('[data-is-spillable="true"]') && $(this).data('id') != v.uin) {
                                        $(this).addClass('marked-target');
                                        $(this).data('click-override', function(o) {
                                            core.command('act/inventory',{action: $(o).is('[data-is-fillable]') ? 'defill' : 'fill', items: [v.uin, $(o).data('id')]});
                                            cancel();
                                        });

                                    } else $(this).addClass('disabled');
                                })
                            }
                        ));

                        content.append('<span class="separator" />');
                    }

                    content.append(v.description);

                    if (notes.length) content.append('<span class="separator" />');
                    $.each(notes, function(k,v) {
                        content.append(
                            $('<div />').addClass('note').text(v)
                        )
                    });

                    if (v.static > 1) {
                        content.append('<span class="separator" />').append(core.snippets.button(rucksack ? <?=__j('Alle ablegen')?> : <?=__j('Alle mitnehmen')?>, function() {
                            core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: $.objToArray(v.set, true)});
                        }));
                    }

                    var actions = [];
                    $.each(v.actions, function(k,v) {actions.push(v)});

                    if (actions.length) content.append('<span class="separator" />');
                    $.each(v.actions, function(k,v) {
                        content.append(
                            core.snippets.button(v)
                        )
                    });

                    if (v.is_pillbox) {
                        var pillrow;
                        content.append('<span class="separator" />').append(
                            pillrow = $('<div />').addClass('row')
                        );

                        $('<div />').addClass('cell rw-6 padded').append(
                            $('<div />').addClass('btn').text(<?=__j('Auffüllen')?>).click(function() {
                                core.command('act/inventory',{action: 'pilltake', items: [v.uin]});
                            })
                        ).appendTo(pillrow);

                        $('<div />').addClass('cell rw-6 padded').append(
                            $('<div />').addClass('btn').text(<?=__j('Teilen')?>).click(function() {
                                var ok = false;
                                var num;
                                while (!ok) {
                                    num = prompt(<?=__j('Wie viele Kapseln möchtest du aus dieser Packung herausnehmen?')?> + ' (1 - ' + (v.count - 1) + ')', 1);
                                    if (num == null) break;
                                    num = parseInt(num);
                                    if (isFinite(num) && num > 1 && num < v.count - 1) ok = true;
                                }
                                if (ok) core.command('act/inventory',{action: 'pilldrop', items: [v.uin], count: num});
                            })
                        ).appendTo(pillrow);
                    }
                }
            }));

        })
    };

    var render_block = function(data, target, headline, rucksack) {
        $(target).empty().append(
            $('<h3 />').text(headline)
        );
        $.each(data, function(k,v) {
            var item_target;
            $(target).append(
                $('<div />').addClass('row').append(
                    $('<b />').text(v.name)
                ).append(
                    item_target = $('<ul />')
                )
            );
            render_box(v.items, item_target, rucksack);
        });
    };

    core.parts.inventory = function(data, target) {
        var iv_a, iv_b;
        $(target).empty().append(
            $('<div />').addClass('cell rw-4 padded').append(
                iv_a = $('<div />').addClass('row inventory flatbox inventory_location')
            )
        ).append(
            $('<div />').addClass('cell rw-4 padded').append(
                iv_b = $('<div />').addClass('row inventory flatbox inventory_player')
            )
        ).append(
            $('<div />').addClass('cell rw-4 padded').append(
                iv_c = $('<div />').addClass('row inventory flatbox').addClass(data.action ? 'inventory_action' : 'inventory_hero')
            )
        );

        render_block(data.player, iv_a, <?=__j('Dein Rucksack')?>, true);
        render_block(data.location, iv_b, data.home ? <?=__j('Deine Truhe')?> : <?=__j('Items am Boden')?>, false);

        if (data.action) {
            iv_a.addClass('disabled');
            iv_b.addClass('disabled');

            iv_c.append($('<h3 />').text(data.action.name)).append($('<span />').text(data.action.desc));

            if (data.action.remaining) {
                var d = 1;
                for (var i = 1; i <= 3; i++)
                    if (data.action.remaining[i] > 0) d = i;

                if (d) {
                    var l = 12 / (d+1);
                    var timerow;
                    iv_c.append(timerow = $('<div />').addClass('row center'));

                    var elems = [<?=__j('Minuten')?>,<?=__j('Stunden')?>,<?=__j('Tage')?>,<?=__j('Wochen')?>];

                    for (i = d; i >= 0; i--)
                        timerow.append($('<div />').addClass('cell rw-' + l).append(
                            $('<div />').append(
                                $('<b />').text(elems[i])
                            ).append(
                                $('<span />').text(data.action.remaining[i])
                            )
                        ))
                }
            }

            if (data.action.abort)
                iv_c.append(
                    $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(function() {
                        if (confirm(<?=__j('Bist du sicher, dass du diese Aktion abbrechen willst?')?>))
                            core.command('act/cancel',{});
                    })
                )

        } else {
            iv_c.append($('<h3 />').text(<?=__j('Heldentaten')?>));
            $.each(data.heroics, function(k,v) {
                iv_c.append(
                    $('<div />').addClass('cell rw-6 padded').append(core.snippets.button(v, function() {
                        return confirm(<?=__j('Bist du sicher, dass du diese Heldentat ausführen möchtest?')?>)
                    }, 'tooltip'))
                )
            });
        }




    };
})();