(function() {
    var cancel = function() {
        var inventories = $('.inventory_location, .inventory_self');
        inventories.find('li[data-id]').removeClass('disabled marked-target').data('click-override', false);
        inventories.find('li.control').removeClass('disabled');

        game.render.html.hint(false);
    };

    var render_box = function(data, target, rucksack, remote) {
        $(target).empty();

        if (!remote)
            $(target).append($('<li />').addClass('control').append($('<i />').addClass('fa fa-caret-square-o-' + (rucksack ? 'right' : 'left'))).attr('title', rucksack ? <?=__j('Alle ablegen')?> : <?=__j('Alle mitnehmen')?>).click(function() {
                var cache = [];
                $.each(data, function(k,v) {
                    cache = cache.concat($.objToArray(v.set, true));
                });
                core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: cache, player: rucksack ? 0 : $('.inventory_player[data-pid-selected=1]').data('pid')});
            }).qtip(game.render.html.qtip.ingame('top')));

        $.each($.objToArray(data, true).sort(function(a,b) {return (a.addr < b.addr) ? -1 : (a.addr == b.addr ? 0 : 1)}) , function(k,v) {
            var container = core.snippets.item(false, v.name, v.icon, v.static <= 1 ? ((v.weapon && v.weapon.shots !== false) ? v.weapon.shots : v.count) : v.static, v.static > 1, true).addClass(remote ? 'remote' : '');
            var flags = $.map(v.flags, function(m) {return m;});
            $(target).append(container);

            if (!remote) {
                if (!v.is_water)
                    container.click(function(e, force) {
                        var o;
                        if (o = $(this).data('click-override'))
                            o(this);
                        else if (!game.touch() || force) core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: [v.uin], player: rucksack ? 0 : $('.inventory_player[data-pid-selected=1]').data('pid')});
                    });
                else
                    container.click(function() {
                        var o;
                        if (o = $(this).data('click-override'))
                            return o(this);

                        cancel();
                        $('.inventory_self').click();
                        var inventories = $('.inventory_location, .inventory_self');
                        inventories.find('li.control').addClass('disabled');
                        var items = inventories.find('li[data-id]');
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
            }

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
                    if (v.label && !v.custom_label)
                        subhead = subhead.add($('<i />').addClass('info center').text(v.label));
                    if (v.count && !v.fill)  subhead = subhead.add($('<i />').addClass('info center').text(v.count + (v.capacity ? (' / ' + v.capacity + ' ') : ' ' ) + v.stack));
                    if (v.weight) subhead = subhead.add($('<i />').addClass('info center').text(<?=__j('Gewicht')?> + ': ' + v.weight));

                    if (subhead.size())
                        content.append(subhead).append('<span class="separator" />');

                    if (v.custom_label) {
                        content
                            .append(
                                $('<div />').addClass('row').append(
                                    $('<div />').addClass('cell rw-12').append(
                                        $('<input>').val(v.label ? v.label : '').attr('type','text').attr('placeholder', <?=__j('Beschriften ...')?>).addClass('form_input').attr('autocomplete','off').on('keydown', function(e) {
                                            if (e.keyCode == 13) {
                                                e.preventDefault();
                                                core.command('act/inventory',{action: 'label', items: [v.uin], text: $(this).val()});
                                            }
                                        }))
                                )
                            )
                            .append($('<div />').addClass('note').text(<?=__j('Du kannst diesen Gegenstand beliebig beschriften. Bestätige deine Beschriftung mit der Eingabetaste.')?>))
                            .append('<span class="separator" />');
                    }

                    if (v.is_chem) {
                        content.append(
                            $('<div />').addClass('note').text(<?=__j('Du kannst diese Chemikalie mit beliebigen anderen Gegenständen kombinieren. Welchen Effekt das hat... das wirst du selbst herausfinden müssen.')?>)
                        ).append(
                            $('<div />').addClass('btn').text(<?=__j('Experimentieren ...')?>).click(function() {
                                container.qtip().hide();

                                cancel();
                                var inventories = $('.inventory_location, .inventory_self');
                                inventories.find('li.control').addClass('disabled');
                                var items = inventories.find('li[data-id]');
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
                        ).append($('<div />').addClass('note').text(<?=__j('Klicke einen leeren Slot an, um Wasser aus einer anderen Quelle hinzuzugeben. Klicke einen gefüllten Slot an, um Wasser auszuschütten. Schwarz gefärbte Slots können nicht ausgeleert werden.')?>));
                        for (i = 0; i < v.count; i++)
                            fillbox.append($('<div />').addClass('fillbox fillbox-filled ' + (v.fill.fixed ? 'fillbox-fixed' : '')).click(function() {
                                if (!v.fill.fixed)
                                    core.command('act/inventory',{action: 'spill', items: [v.uin]});
                            }));
                        for (i = v.count; i < v.fill.capacity; i++)
                            fillbox.append($('<div />').addClass('fillbox pointer').click(function() {
                                container.qtip().hide();

                                cancel();
                                $('.inventory_self').click();
                                var items = $('.inventory_location, .inventory_self').find('li[data-id]');
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

                    if (v.description)
                        content.append(v.description);
                    else notes.push(<?=__j('Über diesen Gegenstand stehen nur wenige Informationen zur Verfügung ...')?>);

                    if (v.armor) {
                        content.append('<span class="separator" />');
                        $('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Typ')?>))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.armor.type))
                            .appendTo(content);
                        $('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Schützt')?>))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.armor.cover))
                            .appendTo(content);
                        $('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Zustand')?>))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.armor.condition))
                            .appendTo(content);
                    }

                    if (v.weapon) {
                        content.append('<span class="separator" />');
                        $('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Schaden')?>))
                            .append($('<div />').addClass('cell rw-6 padded left').text((v.weapon.damage[0] == v.weapon.damage[1] ? v.weapon.damage[0] : (v.weapon.damage[0] + ' - ' + v.weapon.damage[1]))))
                            .appendTo(content);
                        $('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Genauigkeit')?>))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.accuracy === true ? <?=__j('Distanzabhängig')?> : (v.weapon.accuracy + '%')))
                            .appendTo(content);
                        if (v.weapon.ammo) {
                            var ammo_cont = $('<div />');
                            $.each(v.weapon.ammo, function(ak,av) {
                                ammo_cont.append($('<img />').attr('src', 'media/icons/' + av + '.gif'));
                            });
                            $('<div />').addClass('row')
                                .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Munition')?>))
                                .append($('<div />').addClass('cell rw-6 padded left').append(ammo_cont))
                                .appendTo(content);
                        }
                        if (v.weapon.shots !== false)
                            $('<div />').addClass('row')
                                .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Füllstand')?>))
                                .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.shots == 0 ? <?=__j('Leer!')?> : game.i18n(<?=__j(':num Schuss')?>,{':num': v.weapon.shots})))
                                .appendTo(content);
                        if (v.weapon.energy)
                            $('<div />').addClass('row')
                                .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Energie')?>))
                                .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.energy).append($('<img />').attr('src', 'media/icons/status_energy.gif')))
                                .appendTo(content);
                        $('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-6 padded b right').text(<?=__j('Zerstörbar')?>))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.breakable ? <?=__j('Ja')?> : <?=__j('Nein')?>))
                            .appendTo(content);
                    }

                    if (notes.length && v.description) content.append('<span class="separator" />');
                    $.each(notes, function(k,v) {
                        content.append(
                            $('<div />').addClass('note').text(v)
                        )
                    });


                    if (v.deco) {
                        content.append('<span class="separator" />');
                        content.append($('<div />').addClass('row')
                            .append($('<div />').addClass('cell rw-2 right').append($('<img />').attr('src','media/icons/deco_' + (v.deco > 0 ? 'positive' : 'negative') + '.gif')))
                            .append($('<div />').addClass('cell rw-3 center').addClass(v.deco > 0 ? 'text-green' : 'text-red').text(v.deco > 0 ? ('+' + v.deco) : v.deco))
                            .append($('<div />').addClass('cell rw-7 b').addClass(v.deco > 0 ? 'text-green' : 'text-red').text(v.deco > 0 ? <?=__j('Dekorativer Gegenstand')?> : <?=__j('Abstoßender Gegenstand')?>))
                        );
                    }

                    if (v.ammobelt) {
                        var ammo_slots;
                        content.append('<span class="separator" />').append(
                            ammo_slots = $('<div />').addClass('center')
                        ).append($('<div />').addClass('note').text(<?=__j('Klicke Munition an, um sie abzulegen.')?>));
                        $.each(v.ammobelt, function(k,vin) {
                            ammo_slots.append($('<div />').addClass('itembox pointer').append($('<img />').attr('src','media/icons/' + vin.icon + '.gif')).append($('<span />').text(vin.count)).click(function() {
                                var ok = false;
                                var num;
                                while (!ok) {
                                    num = prompt(<?=__j('Wie viel Munition möchtest du ablegen?')?> + ' (1 - ' + (vin.count) + ')', vin.count);
                                    if (num == null) break;
                                    num = parseInt(num);
                                    if (isFinite(num) && num >= 1 && num <= vin.count) ok = true;
                                }
                                if (ok) core.command('act/inventory',{action: 'belt', items: [v.uin], count: num, addr: vin.addr});
                            }))
                        });

                    }

                    if (v.static > 1 && !v.is_water) {
                        content.append('<span class="separator" />').append(core.snippets.button(rucksack ? <?=__j('Alle ablegen')?> : <?=__j('Alle mitnehmen')?>, function() {
                            core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: $.objToArray(v.set, true), player: rucksack ? 0 : $('.inventory_player[data-pid-selected=1]').data('pid')});
                        }));
                    }

                    var actions = [];
                    $.each(v.actions, function(k,v) {actions.push(v)});

                    if (actions.length) content.append('<span class="separator" />');
                    $.each(v.actions, function(k,v) {
                        content.append(
                            core.snippets.button(v, false, 'nested')
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
                                    if (isFinite(num) && num >= 1 && num <= v.count - 1) ok = true;
                                }
                                if (ok) core.command('act/inventory',{action: 'pilldrop', items: [v.uin], count: num});
                            })
                        ).appendTo(pillrow);
                    }

                    if (game.touch()) {
                        $('<div />').addClass('btn green').text(<?=__j('Aufnehmen / Ablegen')?>).click(function() {
                            container.trigger('click', [true]);
                        }).css('margin-top', 10).appendTo(content)
                    }
                }
            }));

        })
    };

    var render_block = function(data, target, headline, rucksack, remote) {
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
            render_box(v.items, item_target, rucksack, remote);
        });
    };

    var iv_switch = function() {
        if (!$(this).is('.inventory_self')) cancel();
        $(this).siblings('.inventory_player').css('cursor','pointer').attr('data-pid-selected', 0).children('.row').slideUp(200);
        $(this).css('cursor','default').attr('data-pid-selected', 1).children('.row').slideDown(200);

        core.session('main.mp.inventory.open', $(this).data('pid'));
    };

    core.parts.inventory = function(data, target, include_heroics) {
        include_heroics = include_heroics || data.action;

        var iv_a, iv_b, iv_c;
        $(target).empty().append(
            $('<div />').addClass(include_heroics ? 'cell rw-4 rw-lg-6 padded' : 'cell rw-6 padded').append(
                iv_a = $('<div />').addClass('row inventory flatbox inventory_player inventory_self')
            )
        ).append(
            $('<div />').addClass(include_heroics ? 'cell rw-4 rw-lg-6 padded' : 'cell rw-6 padded').append(
                iv_b = $('<div />').addClass('row inventory flatbox inventory_location')
            )
        ).append(
            include_heroics ? $('<div />').addClass('cell rw-4 rw-lg-12 padded').append(
                iv_c = $('<div />').addClass('row inventory flatbox').addClass(data.action ? 'inventory_action' : 'inventory_hero')
            ) : null
        );

        render_block(data.player, iv_a, <?=__j('Dein Rucksack')?>, true);

        iv_a.append(
            $('<div />')
                .addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('weightbar').append($('<div />').css('width', (100*data.weight[0]/data.weight[1]) + '%'))))
                .attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                    render: function(event,api) {
                        var content = $(this).find('.qtip-content').empty();

                        content
                            .append($('<b />').addClass('header').text(<?=__j('Gewicht')?>))
                            .append($('<span />').text(<?=__j('Du kannst nur so viel Zeug mit dir rumschleppen wie du tragen kannst. Wenn dein Rucksack voll ist musst du wohl oder übel Gegenstände liegen lassen.')?>))
                            .append($('<span />').addClass('separator'))
                            .append($('<div />').addClass('center').text(<?=__j('Aktueller Wert')?> + ': ' + Math.round10(data.weight[0],-2) + ' / ' + Math.round10(data.weight[1], -2)))
                    }
                }))
        );

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
                                $('<h4 />').text(elems[i])
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

            if (include_heroics) {
                iv_c.append($('<h3 />').text(<?=__j('Heldentaten')?>));
                $.each(data.heroics, function(k,v) {
                    iv_c.append(
                        $('<div />').addClass(game.touch() ? 'cell rw-12 padded' : 'cell rw-6 rw-sm-12 padded').append(core.snippets.button(v, function() {
                            return confirm(<?=__j('Bist du sicher, dass du diese Heldentat ausführen möchtest?')?>)
                        }, 'tooltip'))
                    )
                });
            }


            if (core.last.players) {
                $.each(core.last.players.others, function(k,v) {
                    if (!v.escort) return;

                    var remote_inv;
                    iv_a.after(remote_inv = $('<div />').addClass('row inventory flatbox inventory_player'));

                    render_block(v.inventory.player, remote_inv, game.i18n(<?=__j('Rucksack von :name')?>, {':name': v.name}), true, true);

                    remote_inv.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('weightbar').append($('<div />').css('width', (100*v.inventory.weight[0]/v.inventory.weight[1]) + '%')))));
                    remote_inv.attr('data-pid', v.id).attr('data-pid-selected', 0).click(iv_switch).children('.row').hide();
                });
                iv_a.attr('data-pid',0).click(iv_switch).children('.row').hide();

                var opener = $('.inventory_player[data-pid=' + core.session('main.mp.inventory.open') + ']');
                if (opener.length != 1) opener = iv_a;
                opener.children('.row').show().click();
            }

        }
    };

    core.parts.heroics = function(data, target) {
        var iv_c;
        $(target).empty().append(
            $('<div />').addClass('cell rw-6 ro-3 rw-lg-8 ro-lg-2 rw-md-10 ro-md-1 rw-sm-12 ro-sm-0 padded').append(
                iv_c = $('<div />').addClass('row inventory flatbox').addClass('inventory_hero')
            )
        );

        if (data.action) iv_c.addClass('disabled');

        iv_c.append($('<h3 />').text(<?=__j('Heldentaten')?>));

        var has = false;
        $.each(data.heroics, function(k,v) {
            has = true;
            iv_c.append(
                $('<div />').addClass(game.touch() ? 'cell rw-12 padded' : 'cell rw-6 rw-sm-12 padded').append(core.snippets.button(v, function() {
                    return confirm(<?=__j('Bist du sicher, dass du diese Heldentat ausführen möchtest?')?>)
                }, 'tooltip'))
            )
        });

        if (!has)
            iv_c.append($('<div />').addClass('note').text(<?=__j('Du kannst derzeit keine Heldentaten einsetzen.')?>))
    };
})();