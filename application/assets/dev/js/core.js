
/**
 * @var {Core} core
 */
core = {
    parts: {},
    snippets: {},

    version: '2.0.0-0-1-55',

    last: {},

    sessiondata: {},

    session: function(key, data) {
        return (typeof data == 'undefined')
            ? core.sessiondata[key]
            : (core.sessiondata[key] = data);
    },

    command: function(url, args, background, callback, no_clean) {
        if (!url)
            url = 'japi/game/data';
        else url = 'japi/' + url;

        if (!background) game.render.html.modal.work();
        game.network.query(url,args,function(data) {

            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                game.reset();
                return;
            }

            if (data.redirect) {
                game.clean();
                return game.network.load(data.redirect);
            }

            if (!background && !no_clean) game.clean(true);

            if (data.version && data.version != core.version) {
                game.reset();
                return;
            } if (callback)
                callback(data);
            else if (data) core.render(data, $('#content').empty());
        });
    },

    render: function(data, target) {
        console.log(data);

        core.last = data;

        if (core.parts.admin) core.parts.admin.controls($('<div />').addClass('cell rw-12 padded').appendTo($('<div />').addClass('row').appendTo(target)));

        if (data.location) {
            var location_box = $('<div />').addClass('row').appendTo(target);
            core.parts.location(data.location, location_box);
        }

        var action_box = $('<div />').addClass('row action_box ' + (data.location.meta.outside ? 'outside' : 'inside')).appendTo(target);

        if (data.location.meta.css) {
            location_box.addClass('custom custom-' + data.location.meta.css);
            action_box.addClass('custom custom-' + data.location.meta.css);
        }

        var auto_tab = $('<ul />').addClass('tabline').appendTo(action_box)
            .append($('<li>').attr('data-toggle', '#inv_container').text("Gegenst\u00e4nde & Heldentaten"))
            .append($('<li>').attr('data-toggle', '#settings_container').text("Zeitfluss & Verhalten"))
            .append(data.players ? $('<li>').attr('data-toggle', '#mp_container').text("Spieler\u00fcbersicht") : false)
            .find('>li').click(function() {
                var t = $($(this).data('toggle'));
                core.session('main.tabs.open', $(this).data('toggle'));
                $(this).addClass('active').siblings().removeClass('active');
                action_box.children('div').hide();
                t.show();
            }).first();

        if (data.inventory)
            core.parts.inventory(data.inventory, $('<div />').attr('id', 'inv_container').addClass('row').appendTo(action_box));

        if (data.settings)
            core.parts.settings(data.settings, $('<div />').attr('id', 'settings_container').addClass('row').appendTo(action_box));

        if (data.players)
            core.parts.mp_players(data.players, $('<div />').attr('id', 'mp_container').addClass('row').appendTo(action_box));

        if (data.status && data.clock)
            core.parts.status(data.status, data.clock, $('#persistent'));
        
        if (data.log)
            core.parts.log(data.log,$('<div />').addClass('row log_box').appendTo(target));

        var set_tab = auto_tab.parent().children().filter('[data-toggle=' + core.session('main.tabs.open') + ']');
        if (set_tab.length == 1) set_tab.click();
        else auto_tab.click();
    }
};(function() {
    core.parts.admin = {};

    core.parts.admin.loader = function(target, path, callback) {
        target.empty().append(core.snippets.wait());
        game.network.query(path, {}, function(data) {
            if (data.error) {
                core.parts.admin.controls(target.empty());
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            }
            else
                callback(target.empty().append($('<div />').addClass('btn small').text('MenǬ').click(function() {
                    core.parts.admin.controls(target);
                })).append('<br />'),data);
        });
    };

    core.parts.admin.execute = function(path, data) {
        game.render.html.modal.work();
        game.network.query(path, data, function(r) {
            if (r.error)
                alert(r.error.code + ' [' + r.error.name + ']: ' + r.error.message);
            else core.command();
        });
    };

    core.parts.admin.controls = function(target) {
        target.empty();
        var ret = $('<div />').addClass('row').appendTo(target);

        $('<div />').addClass('btn small').text('Create item...').click(function() {
            core.parts.admin.loader(target,'admin/japi/gamepanel/list_items',core.parts.admin.items)
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Unveil Map').click(function() {
            core.parts.admin.execute('admin/japi/gamepanel/unveil_map', {});
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Heal Player').click(function() {
            core.parts.admin.execute('admin/japi/gamepanel/regenerate', {});
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Siege...').click(function() {
            var n = parseInt(prompt('Number of zombies? (+/-)', '0'));
            if (!isFinite(n) || !n) return;
            core.parts.admin.execute('admin/japi/gamepanel/siege', {'z': n});
        }).appendTo(ret);
    };

    core.parts.admin.items = function(target,data) {

        var spawn = $('<div />').addClass('row flatbox').hide();
        var spawner = function(location) {
            var sets = [];
            spawn.find('.item').each(function() {
                sets.push($(this).data('data'));
            });
            core.parts.admin.execute('admin/japi/gamepanel/spawn_items', {inventory: !location, data: sets});
        };

        spawn.append(
            $('<div />').addClass('btn small').text('Send to me').click(function() {spawner(false);})
        ).append(
            $('<div />').addClass('btn small').text('Place at location').click(function() {spawner(true);})
        ).appendTo(target);
        var inv = $('<div />').addClass('row inventory flatbox').appendTo(target).hide();

        $.each(data.items, function(k,v) {
            var ul = inv.find('ul[data-cat="' + v.cat + '"]');
            if (!ul.length) {
                inv.append($('<b />').text(v.cat));
                inv.append(ul = $('<ul />').attr('data-cat', v.cat));
            }

            var li = core.snippets.item('[' + v.id + '] ' + v.desc, v.name, v.icon,0,false,true);

            li.click(function() {
                var count = prompt("Number of instances?", "1");
                if (count === null || !isFinite(count = parseInt(count)) || count <= 0) return;

                var data = {
                    'id': v.id,
                    'count': count,
                    'params': []
                };

                var param_ok = true;
                $.each(v.params, function(inner, param) {
                    if (param.force)
                        data.params[param.num] = param.default;
                    else {
                        var value = prompt(v.id + ' - Parameter ' + param.name + ' (' + (param.optional ? ('optional, default is "' + param.default + '"') : 'required') + ')', param.default);
                        if (value === null) {
                            if (param.optional) value = param.default;
                            else {
                                param_ok = false;
                                return false;
                            }
                        }

                        data.params[param.num] = value;
                    }
                });

                if (!param_ok) return;

                spawn.append(
                    core.snippets.item('[' + v.id + '] ' + v.desc, v.name, v.icon,count,false,false).click(function() {
                        $(this).remove();
                    }).data('data',data)
                );
            });

            ul.append(li);
        });

        spawn.slideDown();
        inv.slideDown();
    }
})();
(function() {
    var cancel = function() {
        $('.inventory_location, .inventory_player').find('li[data-id]').removeClass('disabled marked-target').data('click-override', false);
        game.render.html.hint(false);
    };

    var render_box = function(data, target, rucksack, remote) {
        $(target).empty();

        $.each(data, function(k,v) {
            var container = core.snippets.item(false, v.name, v.icon, v.static <= 1 ? v.count : v.static, v.static > 1, true).addClass(remote ? 'remote' : '');
            var flags = $.map(v.flags, function(m) {return m;});
            $(target).append(container);

            if (!remote) {
                if (!v.is_water)
                    container.click(function() {
                        var o;
                        if (o = $(this).data('click-override'))
                            o(this);
                        else core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: [v.uin], player: rucksack ? 0 : $('.inventory_player[data-pid-selected=1]').data('pid')});
                    });
                else
                    container.click(function() {
                        var o;
                        if (o = $(this).data('click-override'))
                            return o(this);

                        cancel();
                        $('.inventory_self').click();
                        var items = $('.inventory_location, .inventory_self').find('li[data-id]');
                        var hint = game.render.html.hint(true);
                        hint.append(
                            $('<span />').text("W\u00e4hle einen Wasserbeh\u00e4lter aus, in den du die gew\u00e4hlte Ration Wasser hineinsch\u00fctten willst.")
                        ).append(
                            $('<div />').addClass('btn').text("Abbrechen").click(cancel)
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
                        notes.push("Dies ist eine R\u00fcstung. Sie wendet w\u00e4hrend eines Kampfes Schaden von dir ab.");
                        break;
                    case 'weapon':
                        notes.push("Dies ist eine Waffe. Sie wird automatisch eingesetzt wenn du gegen Zombies k\u00e4mpfst.");
                        break;
                    case 'escape':
                        notes.push("Dieser Gegenstand hilft dir dabei, vor Zombies zu fliehen die dich Belagern. Er wird automatisch bei Bedarf eingesetzt.");
                        break;
                    case 'temp':
                        container.addClass('temp');
                        notes.push("Dieser Gegenstand verschwindet, wenn du ihn zur\u00fcckl\u00e4sst.");
                        break;
                    case 'event':
                        container.addClass('event');
                        notes.push("Dies ist ein Event-Gegenstand. Er verschwindet, wenn du das Event-Gebiet verl\u00e4sst oder das Event endet.");
                        break;
                    case 'carrier':
                        notes.push("Du kannst diesen Gegenstand mitf\u00fchren, ohne dass dein Rucksack belastet wird.");
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
                        subhead = subhead.add($('<i />').addClass('note center').text(v.label));
                    if (v.count && !v.fill)  subhead = subhead.add($('<i />').addClass('note center').text(v.count + (v.capacity ? (' / ' + v.capacity + ' ') : ' ' ) + v.stack));
                    if (v.weight) subhead = subhead.add($('<i />').addClass('note center').text("Gewicht" + ': ' + v.weight));

                    if (subhead.size())
                        content.append(subhead).append('<span class="separator" />');

                    if (v.custom_label) {
                        content
                            .append(
                                $('<div />').addClass('row').append(
                                    $('<div />').addClass('cell rw-12').append(
                                        $('<input>').val(v.label ? v.label : '').attr('type','text').attr('placeholder', "Beschriften ...").addClass('form_input').attr('autocomplete','off').on('keydown', function(e) {
                                            if (e.keyCode == 13) {
                                                e.preventDefault();
                                                core.command('act/inventory',{action: 'label', items: [v.uin], text: $(this).val()});
                                            }
                                        }))
                                )
                            )
                            .append($('<i />').addClass('note center').text("Du kannst diesen Gegenstand beliebig beschriften. Best\u00e4tige deine Beschriftung mit der Eingabetaste."))
                            .append('<span class="separator" />');
                    }

                    if (v.is_chem) {
                        content.append(
                            $('<i />').addClass('note justify').text("Du kannst diese Chemikalie mit beliebigen anderen Gegenst\u00e4nden kombinieren. Welchen Effekt das hat... das wirst du selbst herausfinden m\u00fcssen.")
                        ).append(
                            $('<div />').addClass('btn').text("Experimentieren ...").click(function() {
                                container.qtip().hide();

                                cancel();
                                var items = $('.inventory_location, .inventory_player').find('li[data-id]');
                                var hint = game.render.html.hint(true);
                                hint.append(
                                    $('<span />').text("W\u00e4hle einen Gegenstand, mit dem du die Chemikalie verbinden m\u00f6chtest.")
                                ).append(
                                    $('<div />').addClass('btn').text("Abbrechen").click(cancel)
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
                        ).append($('<i />').addClass('note justify').text("Klicke einen leeren Slot an, um Wasser aus einer anderen Quelle hinzuzugeben. Klicke einen gef\u00fcllten Slot an, um Wasser auszusch\u00fctten. Schwarz gef\u00e4rbte Slots k\u00f6nnen nicht ausgeleert werden."));
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
                                    $('<span />').text("W\u00e4hle eine Fl\u00fcssigkeit oder einen anderen Beh\u00e4lter aus, um diesen Beh\u00e4lter zu f\u00fcllen.")
                                ).append(
                                    $('<div />').addClass('btn').text("Abbrechen").click(cancel)
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
                    else notes.push("\u00dcber diesen Gegenstand stehen nur wenige Informationen zur Verf\u00fcgung ...");

                    if (notes.length && v.description) content.append('<span class="separator" />');
                    $.each(notes, function(k,v) {
                        content.append(
                            $('<div />').addClass('note').text(v)
                        )
                    });

                    if (v.ammobelt) {
                        var ammo_slots;
                        content.append('<span class="separator" />').append(
                            ammo_slots = $('<div />').addClass('center')
                        ).append($('<i />').addClass('note justify').text("Klicke Munition an, um sie abzulegen."));
                        $.each(v.ammobelt, function(k,vin) {
                            ammo_slots.append($('<div />').addClass('itembox pointer').append($('<img />').attr('src','media/icons/' + vin.icon + '.gif')).append($('<span />').text(vin.count)).click(function() {
                                var ok = false;
                                var num;
                                while (!ok) {
                                    num = prompt("Wie viel Munition m\u00f6chtest du ablegen?" + ' (1 - ' + (vin.count) + ')', vin.count);
                                    if (num == null) break;
                                    num = parseInt(num);
                                    if (isFinite(num) && num > 1 && num <= vin.count) ok = true;
                                }
                                if (ok) core.command('act/inventory',{action: 'belt', items: [v.uin], count: num, addr: vin.addr});
                            }))
                        });

                    }

                    if (v.static > 1 && !v.is_water) {
                        content.append('<span class="separator" />').append(core.snippets.button(rucksack ? "Alle ablegen" : "Alle mitnehmen", function() {
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
                            $('<div />').addClass('btn').text("Auff\u00fcllen").click(function() {
                                core.command('act/inventory',{action: 'pilltake', items: [v.uin]});
                            })
                        ).appendTo(pillrow);

                        $('<div />').addClass('cell rw-6 padded').append(
                            $('<div />').addClass('btn').text("Teilen").click(function() {
                                var ok = false;
                                var num;
                                while (!ok) {
                                    num = prompt("Wie viele Kapseln m\u00f6chtest du aus dieser Packung herausnehmen?" + ' (1 - ' + (v.count - 1) + ')', 1);
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

    core.parts.inventory = function(data, target) {
        var iv_a, iv_b;
        $(target).empty().append(
            $('<div />').addClass('cell rw-4 padded').append(
                iv_a = $('<div />').addClass('row inventory flatbox inventory_player inventory_self')
            )
        ).append(
            $('<div />').addClass('cell rw-4 padded').append(
                iv_b = $('<div />').addClass('row inventory flatbox inventory_location')
            )
        ).append(
            $('<div />').addClass('cell rw-4 padded').append(
                iv_c = $('<div />').addClass('row inventory flatbox').addClass(data.action ? 'inventory_action' : 'inventory_hero')
            )
        );

        render_block(data.player, iv_a, "Dein Rucksack", true);

        iv_a.append(
            $('<div />')
                .addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('weightbar').append($('<div />').css('width', (100*data.weight[0]/data.weight[1]) + '%'))))
                .attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                    render: function(event,api) {
                        var content = $(this).find('.qtip-content').empty();

                        content
                            .append($('<b />').addClass('header').text("Gewicht"))
                            .append($('<span />').text("Du kannst nur so viel Zeug mit dir rumschleppen wie du tragen kannst. Wenn dein Rucksack voll ist musst du wohl oder \u00fcbel Gegenst\u00e4nde liegen lassen."))
                            .append($('<span />').addClass('separator'))
                            .append($('<div />').addClass('center').text("Aktueller Wert" + ': ' + Math.round10(data.weight[0],-2) + ' / ' + Math.round10(data.weight[1], -2)))
                    }
                }))
        );

        render_block(data.location, iv_b, data.home ? "Deine Truhe" : "Items am Boden", false);

        if (data.action) {
            iv_a.parent.addClass('disabled');
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

                    var elems = ["Minuten","Stunden","Tage","Wochen"];

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
                    $('<div />').addClass('btn').text("Abbrechen").click(function() {
                        if (confirm("Bist du sicher, dass du diese Aktion abbrechen willst?"))
                            core.command('act/cancel',{});
                    })
                )

        } else {
            iv_c.append($('<h3 />').text("Heldentaten"));
            $.each(data.heroics, function(k,v) {
                iv_c.append(
                    $('<div />').addClass('cell rw-6 padded').append(core.snippets.button(v, function() {
                        return confirm("Bist du sicher, dass du diese Heldentat ausf\u00fchren m\u00f6chtest?")
                    }, 'tooltip'))
                )
            });

            if (core.last.players) {
                $.each(core.last.players.others, function(k,v) {
                    if (!v.escort) return;

                    var remote_inv;
                    iv_a.after(remote_inv = $('<div />').addClass('row inventory flatbox inventory_player'));

                    render_block(v.inventory.player, remote_inv, game.i18n("Rucksack von :name", {':name': v.name}), true, true);

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
})();(function() {
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

        var ranks = ["Neulingsk\u00e4mpfe","Tournament f\u00fcr Nachwuchsmetzler","Tournament f\u00fcr routinierte Schl\u00e4chter","Tournament f\u00fcr Profikiller","Master-Tournament"];
        var arenas = ["Cagematch","Boxring","Freiluft-Arena","Hauptplatz des Kolosseums"];

        round
            .text(data.level == 0 ? "Qualifikationsrunde" : game.i18n("Runde :round",{':round': data.level}))
            .attr('title', "F\u00fcr jeden gewonnenen Kampf steigst du im Kolosseum eine Ebene auf. Au\u00dferdem erh\u00e4lst du Seelenpunkte sowie n\u00fctzliche Gegenst\u00e4nde. Nat\u00fcrlich werden die K\u00e4mpfe mit jeder Runde gef\u00e4hrlicher...")
            .qtip(game.render.html.qtip.ingame('top'));

        rank.text(ranks[data.rank]);

        next_arena
            .append($('<b />').text("N\u00e4chster Kampf:")).append('<br />')
            .append($('<span />').text(arenas[data.arena]))
            .attr('title', "Jede Arena des Colosseums stellt dich vor andere Herausforderungen. Achte darauf wo der n\u00e4chste Kampf stattfindet, um dich optimal zu bewaffnen.")
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

        level.text("Detailgrad deiner Karte: ").append(tx = $('<b />').text(data.level + '%'));
        level.attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
            render: function() {
                var content = $(this).find('.qtip-content').empty().append(
                    $('<b />').addClass('header').text("Diesen Ort erkunden")
                ).append(
                    $('<span />').text("Um in diesem Spielmodus punkte zu sammeln, musst du so viele Ruinen wie m\u00f6glich kartographieren. Je gr\u00fcndlicher du arbeitest, desto schneller steigt der Detailgrad deiner Karte - aber du gehst auch ein gr\u00f6\u00dferes Risiko ein.")
                ).append('<span class="separator" />')
                .append(
                    $('<div />').addClass('btn btn-zv').text("\u00dcberblicken").click(function() {
                        core.command('location/scout', {speed: 1});
                    })
                ).append(
                    $('<div />').addClass('btn btn-zv').text("Skizzieren").click(function() {
                        core.command('location/scout', {speed: 2});
                    })
                ).append(
                    $('<div />').addClass('btn btn-zv').text("Vermessen").click(function() {
                        core.command('location/scout', {speed: 3});
                    })
                ).append(
                    $('<div />').addClass('btn btn-zv ' + (data.laser ? '' : 'disabled')).text("Lasermessger\u00e4t einsetzen").click(function() {
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
            .attr('title', "Ein h\u00fcbsch eingerichtetes Versteck reduziert die Chance, dass pl\u00f6tzlich ein RTL-Messie-Kamerateam (oder Tine Wittler) vor deiner T\u00fcr steht. So f\u00fchlst du dich direkt viel wohler.")
            .qtip(game.render.html.qtip.ingame('top'));

        repair
            .append($('<img />').attr('src', 'media/icons/decay.gif'))
            .append($('<span />').text(data.state + '%'))
            .attr('title', "Dein Versteck ist eine ziemliche Bruchbude - vermutlich hast du beim Bau nicht mal g\u00e4ngige Normen eingehalten. Tja, deswegen musst du dich nun mit Verfall herumschlagen. Mit der Zeit wird sich der Zustand deines Verstecks verschlechtern, wodurch die Hausverteidigung sinkt.")
            .qtip(game.render.html.qtip.ingame('top'));

        defense
            .append($('<img />').attr('src', 'media/icons/defense.gif'))
            .append($('<span />').text(data.defense == data.max_defense ? data.defense : (data.defense + '/' + data.max_defense)))
            .attr('title', "Die Hausverteidigung gibt an, wie vielen Zombies dein Versteck bei einer Belagerung standhalten kann. Wird dein Versteck von mehr Zombies belagert, so k\u00f6nnen diese deine Verteidigung durchbrechen und dich angreifen!")
            .qtip(game.render.html.qtip.ingame('top'));
    };

    var zombieradar = function(data, target) {

        // Create danger text
        var danger_text, zombie_text;
        switch (data.danger) {
            case 0:             danger_text = "Sicher"; break;
            case 1:             danger_text = "Geringe Gefahr"; break;
            case 2:             danger_text = "Moderate Gefahr"; break;
            case 3:             danger_text = "Betr\u00e4chtliche Gefahr"; break;
            case 4:             danger_text = "Hohe Gefahr"; break;
            case 5: default:    danger_text = "Sehr hohe Gefahr!"; break;
        }

        // Create zombie count
        if (data.zombies == 0)
            zombie_text = "Keine Zombies";
        else if (data.zombies == 1)
            zombie_text = "1 Zombie";
        else zombie_text = ":num Zombies";

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
        if (data.prop == 0 && !data.hideout) tooltip.push("Hier musst du vorerst keine Angst unerwarteten Angriffen haben.");
        else if (data.prop == 0 && data.hideout) tooltip.push("Du bist hier so lange sicher, wie dein Versteck den Zombies widerstehen kann.");
        else {
            tooltip.push("Dein Gesp\u00fchr sagt dir, dass du auf Zombie-Gruppen mit einer Gr\u00f6\u00dfe von bis zu :max Zombies gefasst sein solltest.");
            tooltip.push("Rechne damit, etwa alle :pc_min Minuten auf Zombies zu treffen.");
        }
        if (!data.hideout)
            if (data.inc == 0) tooltip.push("Die Zombies haben hier keine Gelegenheit, dir den Weg zu versperren.");
            else tooltip.push("Die Zombies k\u00f6nnten sich hier versammeln und dir den Fluchtweg abschneiden... So wies aussieht w\u00fcrden sie daf\u00fcr vermutlich um die :sg_min Minuten ben\u00f6tigen.");
        else
            if (data.inc == 0) tooltip.push("Dieses Versteck werden die Zombies niemals finden!");
            else tooltip.push("Es ist nur eine Frage der Zeit, bis dieses Versteck von Zombies umstellt wird. So wies aussieht w\u00fcrden sie daf\u00fcr vermutlich um die :sg_min Minuten ben\u00f6tigen.");

        radar.attr('title',
            game.i18n(tooltip.join('<br /><br />'), {':min': '<b>' + data.min + '</b>',':max': '<b>' + data.max + '</b>',':pc_min': '<b>' + data.prop + '</b>',':sg_min': '<b>' + data.inc + '</b>'})
        ).qtip(game.render.html.qtip.ingame('top'));

        siege.attr('title','-').qtip(game.render.html.qtip.ingame('top',{
            render: function(event,api) {
                var content = $(this).find('.qtip-content').empty();
                if (data.zombies == 0)
                    content.append("Es sieht so aus, als k\u00f6nntest du diesen Ort momentan ohne Probleme verlassen. Du solltest trotzdem regelm\u00e4\u00dfig nachschauen, ob Zombies eventuell den Weg blockieren.");
                else {
                    var fight, flee;
                    if (data.hideout)
                        content.append("Die Zombies haben dein Versteck aufgesp\u00fcrt. Von hier kannst du nicht mehr fliehen - du musst die Zombies bek\u00e4mpfen!");
                    else content.append("Es geht weder vor noch zur\u00fcck - Zombies blockieren den Ausgang! Du kannst entweder eine waghalsige Flucht versuchen oder den Weg freizur\u00e4umen. Eins steht fest: Von alleine werden diese Zombies hier nicht verschwinden...");

                    content
                        .append('<br /><br />')
                        .append(core.snippets.button("Weg freik\u00e4mpfen", function() {
                            api.hide();
                            core.command('location/fight');
                        }))
                        .append(core.snippets.button("Fluchtversuch", function() {
                            api.hide();
                            core.command('location/flee');
                        }).addClass(data.hideout ? 'disabled' : ''));
                }
                return true;
            }
        }));
    };

    core.parts.location = function(data, target) {
        var zradar, hideout, actions, spc_colosseum, spc_scout, desc;

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
            desc = $('<div />').addClass('cell rw-6 padded justify').text(data.meta.desc)
        );

        if (core.last.players) {
            var area = [];
            $.each(core.last.players.others, function(id, player) {
                if (player.local) area.push(player.name);
            });

            if (area.length) {
                var p = $('<p />').appendTo(desc).attr('title',"Hier siehst du Spieler, die sich momentan in deiner N\u00e4he befinden. Um mehr Details zu erfahren, klicke \"Spieler\u00fcbersicht\".").qtip(game.render.html.qtip.ingame('bottom'));
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
            $('<div />').addClass('cell padded justify rw-' + (data.doorways ? '10' : '12')).append(core.snippets.button("Karte", function() {
                core.popup.map();
            }))
        );

        if (data.doorways) {
            actions.append(
                $('<div />').addClass('cell padded justify rw-2').append(
                    $('<div />').addClass('btn').append($('<i>').addClass('fa fa-sign-in')).append('&nbsp;').click(function() {
                        var esc_popup = core.popup.spawn(400);

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text("Ort wechseln"));

                        esc_popup.append(
                            $('<div />').addClass('row').append(title = $('<div />').addClass('cell rw-12 padded').text("Du kannst von diesem Ort aus einen anderen Teil der Spielwelt betreten."))
                        );

                        var destination = $('<select />');
                        $.each(data.doorways, function(id, meta) {
                            $('<option />').attr('value', id).text(meta.name + ' (' + meta.location + ')').appendTo(destination);
                        });

                        esc_popup.append(
                            $('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Wo soll's denn hingehen?")))
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
                                    .prepend($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Wer soll alles mitkommen?")))
                                    .append($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Und wie siehts mit dir aus?")))
                                    .append($('<div />').addClass('cell rw-12 padded').append(
                                        bhav = $('<select />')
                                            .append($('<option />').val('1').text("Mitgehen"))
                                            .append($('<option />').val('0').text("Die Stellung halten"))
                                            .val('1')
                                    ));

                                bhav.selectric();
                                check_row.find(':checkbox').customRadioCheck();

                            }

                            esc_popup.append($('<div />').addClass('row')
                                    .append($('<div />').addClass('cell rw-8 padded').append(
                                        $('<div />').addClass('btn').text("Los gehts!").addClass(data.radar.zombies > 0 ? 'disabled' : '').click(function() {

                                            var cfg = {to: destination.val(), follow: 1};
                                            if (check_row.children().length) {
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
                                        $('<div />').addClass('btn').text("Abbrechen").click(function() {
                                            esc_popup.trigger('unpop');
                                        })))
                            );
                        }
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
            scoutmode(data.scouting, spc_scout)
    };
})();(function() {
    var renderers = {};

    renderers[0] =
        function(data) {
            return data.title
                ? $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<div />').addClass('sub').text(data.body))
                : $('<div />').append($('<div />').text(data.body))

        };

    renderers[1] =
        function(data) {
            var txt;
            switch (data['class']) {
                case 1:
                    txt = data.self ? "Du hast diesen Ort betreten." : game.i18n(":name hat diesen Ort betreten.", {':name': data.name});
                    break;
                case 2:
                    txt = data.self ? "Du hast diesen Ort verlassen." : game.i18n(":name hat diesen Ort verlassen.", {':name': data.name});
                    break;
                case 3:
                    txt = data.self ? "Du hast diesen Ort auf deinem Weg passiert." : game.i18n(":name hat diesen Ort auf seinem Weg passiert.", {':name': data.name});
                    break;
            }

            return $('<div />').text(txt);
        };

    renderers[2] =
        function(data) {
            var header;
            switch (data['class']) {
                case 1:
                    header = ":itemdef gefunden!";
                    break;
                case 2:
                    header = ":itemdef entdeckt!";
                    break;
                case 3:
                    header = ":name ist von uns gegangen...";
                    break;
                case 4:
                    header = ":name ist gestorben und hat sich in einen Zombie verwandelt!";
                    break;
                case 5:
                    header = ":name hat nun endlich seinen ewigen Frieden gefunden...";
                    break;
                case 6:
                    header = ":itemdef angelockt!";
                    break;
                case 7:
                    header = ":itemdef erworben!";
                    break;
                default:
                    header = ":itemdef erhalten!";
                    break;
            }


            var title = $('<div />');
            if (data.primary) header = game.i18n(header, {':name': data.primary});
            var pos = header.search(':itemdef');
            if (pos < 0) {
                var sub;
                title.data('expandable', true).append($('<div />').text(header)).append(
                    sub = $('<div />').addClass('sub')
                );

                switch (data['class']) {
                    case 3:
                        sub.append($('<p />').text(game.i18n(data.self ? "Du hast soeben deinen letzten Atemzug getan und deiner Gemeinschaft das wenige, was du hattest, hinterlassen. Das wars dann wohl..." : "Heute ist ein trauriger Tag f\u00fcr eure kleine Gemeinschaft, denn sie ist soeben wieder etwas geschrumpft. Nur einige sterbliche \u00dcberreste sind noch zur\u00fcck geblieben...",{':name': data.primary})));
                        break;
                    case 4:
                        sub.append($('<p />').text(game.i18n(data.self ? "Du hast dich soeben in einen Zombie verwandelt!" : "Heute ist ein trauriger Tag f\u00fcr eure kleine Gemeinschaft, denn sie ist soeben wieder etwas geschrumpft. Die Zombiehorden hingegen haben Zuwachs zu verzeichnen...",{':name': data.primary})));
                        break;
                    case 5:
                        sub.append($('<p />').text(game.i18n(data.self ? "Deine Freunde haben dir endlich den ewigen Frieden geschenkt." : "Es ist immer schwer, jemandem den man gekannt hat den Gnadensto\u00df zu geben. Nur einige sterbliche \u00dcberreste sind noch zur\u00fcck geblieben...",{':name': data.primary})));
                        break;
                }

                $.each(data.content, function (timestamp, list) {
                    $.each(list, function (uid, udata) {
                        $.each(udata.items, function (k, item) {
                            sub.append(core.snippets.item(true, item.name, item.icon, item.count, false, false));
                        });
                    });
                });
            } else {
                title.append($('<span />').text(header.slice(0,pos)));
                $.each(data.content, function(timestamp,list) {
                    $.each(list, function(uid, udata) {
                        $.each(udata.items, function(k, item) {
                            title.append(core.snippets.item(udata.player + ' (' + (new Date(timestamp * 1000)).toLocaleTimeString() + ')',item.name,item.icon,item.count,false,false));
                        });
                    });
                });
                title.append($('<span />').text(header.slice(pos+8)));
            }

            return title;
        };

    renderers[3] =
        function(data) {
            var txt;
            if (data.results)
                txt = data.self ? "Du hast :item mit :chem kombiniert, und dabei :list erhalten." : ":name hat :item mit :chem kombiniert, und dabei :list erhalten.";
            else
                txt = data.self ? "Du hast erfolglos :item mit :chem kombiniert..." : ":name hat erfolglos :item mit :chem kombiniert...";

            txt = game.i18n(txt, {
                ':name':data.name,
                ':item': '<span class="plog_item"></span>',
                ':chem': '<span class="plog_chem"></span>',
                ':list': '<span class="plog_list"></span>'
            });

            var title = $('<div />').html(txt);
            title.find('.plog_item').append(core.snippets.item(true, data.item.name,data.item.icon,data.item.count,false,false));
            title.find('.plog_chem').append(core.snippets.item(true, data.chem.name,data.chem.icon,data.chem.count,false,false));
            var list = title.find('.plog_list');
            $.each(data.results, function(k,v) {
                list.append(core.snippets.item(true, v.name,v.icon,v.count,false,false));
            });

            return title;
        };

    renderers[4] =
        function(data) {
            var txt = ":building aufgedeckt!";

            var title = $('<div />');
            var pos = txt.search(':building');
            if (pos >= 0) {

                title.append($('<span />').text(txt.slice(0,pos)));

                title.append($('<img />').attr('src','media/icons/places/' + data.icon));
                title.append($('<b />').text(' ' + data.ruin));

                title.append($('<span />').text(txt.slice(pos+9)));
                return title;
            } else return title.text(txt);
        };

    renderers[5] =
        function(data) {
            var details = $('<div />').addClass('sub');
            var even = false;

            var summary = {};
            $.each(data.battle, function(round, obj) {
                if (obj.type == 6 && !obj.zombie) {
                    summary[obj.name] = {
                        killed: false, damage_received: 0, damage_dealt: 0, kills: 0, items_lost: {}, energy_lost: 0, injuries: {}
                    }; return;
                }

                if (obj.type == 8 && !obj.zombie) {
                    summary[obj.name].killed = true;
                    return;
                }

                if (obj.type == 10) {
                    if (!summary[obj.player].injuries[obj.name])
                        summary[obj.player].injuries[obj.name] = {'name': obj.name, 'count': 1, 'icon': obj.icon};
                    else summary[obj.player].injuries[obj.name].count++;
                    return;
                }

                var add_lost_item = function(p, name, icon) {
                    var addr = name + '___' + icon;
                    if (!summary[p].items_lost[addr])
                        summary[p].items_lost[addr] = {'name': name, 'count': 1, 'icon': icon};
                    else summary[p].items_lost[addr].count++;
                };

                if (obj.type == 9) {
                    if (obj.attacker.is_zombie) {
                        summary[obj.defender.name].damage_received += (obj.damage - obj.protection.value);
                        if (obj.protection.value) {
                            $.each(obj.protection.covers, function (k, item) {
                                if (!item.stable) add_lost_item(obj.defender.name, item.name, item.icon);
                            });
                            $.each(obj.protection.armor, function (k, item) {
                                if (!item.stable) add_lost_item(obj.defender.name, item.name, item.icon);
                            });
                        }
                    } else {
                        summary[obj.attacker.name].damage_dealt += (obj.damage - obj.protection.value);
                        summary[obj.attacker.name].kills += obj.kills;
                        summary[obj.attacker.name].energy_lost = obj.weapon.energy;

                        if (obj.weapon.destroyed)
                            add_lost_item(obj.attacker.name, obj.weapon.name,obj.weapon.icon);
                        $.each(obj.weapon.ammo, function(k,icon) {
                            add_lost_item(obj.attacker.name, "Munition",icon);
                        });
                    }
                }
            });

            $('<div />').addClass('row log-battle-round').text("Zusammenfassung").appendTo(details);
            var last_summary = $();
            $.each(summary, function(player, data) {
                var row = $('<div />').addClass('row log-battle-summary').appendTo(details);
                last_summary = row;

                $('<div />').appendTo(row).addClass('cell rw-3 player').addClass(data.killed ? 'killed' : '').text(player);
                var stuff = $('<div />').appendTo(row).addClass('cell rw-4 stuff');
                var injuries = $('<div />').appendTo(row).addClass('cell rw-2 injuries');
                var items = $('<div />').appendTo(row).addClass('cell rw-3 items_lost');


                $('<span />').appendTo(stuff).addClass('damage_received').text(Math.round10(data.damage_received,-2)).attr('title', "Erlittener Schaden").qtip(game.render.html.qtip.ingame('top'));
                $('<span />').appendTo(stuff).addClass('energy_lost').text(Math.round10(data.energy_lost,-2)).attr('title', "Verbrauchte Energie").qtip(game.render.html.qtip.ingame('top'));
                $('<span />').appendTo(stuff).addClass('damage_dealt').text(Math.round10(data.damage_dealt,-2)).attr('title', "Angerichteter Schaden").qtip(game.render.html.qtip.ingame('top'));
                $('<span />').appendTo(stuff).addClass('zombies_killed').text(data.kills).attr('title', "Vernichtete Zombies").qtip(game.render.html.qtip.ingame('top'));

                $.each(data.items_lost, function(k,item) {
                    items.append(core.snippets.item("Dieser Gegenstand wurde w\u00e4hrend des Kampfes zerst\u00f6rt.",item.name,item.icon,item.count,true,false))
                });

                $.each(data.injuries, function(k,item) {
                    var s = $('<span />').appendTo(injuries).attr('title', item.name).qtip(game.render.html.qtip.ingame('top'));
                    if (item.count > 1) s.append($('<span />').text(item.count + 'x'));
                    s.append($('<img />').attr('src', 'media/icons/' + item.icon + '.gif'));
                });
            });

            last_summary.addClass('round-close');
            $('<div />').addClass('row log-battle-round').text("Kampfbeginn").appendTo(details);

            $.each(data.battle, function(round, obj) {
                var row = $('<div />').addClass('row').appendTo(details);

                var txt;
                if (obj.type == 6) {
                    if (!obj.zombie) txt = game.i18n(":name tritt dem Kampfgeschehen bei!", {':name': obj.name});
                    else {
                        var d;
                        if		(obj.distance < 5)	d = "in einer dunklen Ecke";
                        else if	(obj.distance < 10)	d = "in unmittelbarer N\u00e4he";
                        else if	(obj.distance < 25)	d = "in der Umgebung";
                        else if	(obj.distance < 50)	d = "in einiger Entfernung";
                        else if	(obj.distance < 75)	d = "weit entfernt";
                        else						d = "am Horizont";
                        txt = game.i18n(obj.ren ? ":zombies erscheint :distance!" : ":zombies tauchen :distance auf.", {':zombies': obj.ren ? obj.name : (obj.count + ' ' + obj.name), ':distance': d});
                    }

                    row.addClass('log-battle-enter').addClass(obj.zombie ? 'log-battle-enter-zombie' : 'log-battle-enter-citizen');
                    row.text(txt);
                }

                else if (obj.type == 7) {
                    row.addClass('log-battle-escape');
                    if (obj.v == -1) row.text("Es gibt kein Entkommen!").addClass('log-battle-escape-impossible');
                    if (obj.v ==  0) row.text("Eine Flucht scheint aussichtslos...").addClass('log-battle-escape-futile');
                    if (obj.v ==  1) row.text("Gerade noch so entkommen! Das war knapp...").addClass('log-battle-escape-success');
                }

                else if (obj.type == 8) {
                    if (!obj.zombie) txt = game.i18n(":name hat es hinter sich...", {':name': obj.name});
                    else  txt = game.i18n(obj.ren ? ":zombies wurde besiegt!" : "Die Meute :zombies wurde zerschlagen!", {':zombies': obj.name});

                    row.text(txt).addClass('log-battle-death').addClass(obj.zombie ? 'log-battle-death-zombie' : 'log-battle-death-citizen');
                }

                else if (obj.type == 11) {
                    row.prev().addClass('round-close');
                    even = false;
                    row.text(game.i18n("Runde :round", {':round': obj.round})).addClass('log-battle-round');
                }

                else if (obj.type == 10) {
                    even = !even;
                    row.addClass('log-battle-injury')
                        .append($('<span />').text(game.i18n(":name hat sich eine Verletzung zugezogen: ", {':name': obj.player})))
                        .append($('<img />').attr('src', 'media/icons/' + obj.icon + '.gif'))
                        .append($('<span />').text(obj.name));
                }

                else if (obj.type == 9) {
                    row.addClass('log-battle-attack').addClass(even ? 'log-battle-attack-even' : 'log-battle-attack-odd');
                    even = !even;

                    var msg_destroyed = "Wurde beim Angriff zerst\u00f6rt!";

                    var desc = $('<div />').addClass('cell rw-6').appendTo(row);
                    var damage = $('<div />').addClass('row').appendTo($('<div />').addClass('cell rw-6').appendTo(row));
                    var items = $('<div />').addClass('cell-small rw-9').appendTo(damage);
                    $('<div />').addClass('cell-small rw-1').append($('<i />').addClass('fa fa-chevron-right')).appendTo(damage);
                    var calc = $('<div />').addClass('cell-small rw-6').appendTo(damage);
                    $('<div />').addClass('cell-small rw-1').append($('<i />').addClass('fa fa-chevron-right')).appendTo(damage);
                    var result = $('<div />').addClass('cell-small rw-7').appendTo(damage);

                    if (obj.attacker.is_zombie) desc
                        .append($('<span />').addClass('zombie').text(obj.attacker.count + ' ' + obj.attacker.name))
                        .append($('<span />').text(obj.attacker.count == 1 ? "st\u00fcrzt sich auf" : "st\u00fcrzen sich auf"))
                        .append($('<span />').addClass('player').text(obj.defender.name));
                    else desc
                        .append($('<span />').addClass('player').text(obj.attacker.name))
                        .append($('<span />').text("attackiert"))
                        .append($('<span />').addClass('zombie').text(obj.defender.count + ' ' + obj.defender.name));

                    items.append(core.snippets.item(true,obj.weapon.name,obj.weapon.icon,1,true,false).addClass(obj.weapon.destroyed ? 'destroyed' : ''));
                    if (obj.weapon.energy)
                        items.append($('<span />').addClass('energy').text(obj.weapon.energy));
                    $.each(obj.weapon.ammo, function(k,icon) {
                        items.append(core.snippets.item(false,'',icon,1,true,false));
                    });

                    if (obj.protection.value) {
                        items.append($('<i />').addClass('fa fa-caret-right'));
                        $.each(obj.protection.covers,function(k,item) {
                            items.append(core.snippets.item(!item.stable ? msg_destroyed : true,item.name,item.icon,1,true,false).addClass(!item.stable ? 'destroyed' : ''));
                        });
                        $.each(obj.protection.armor,function(k,item) {
                            items.append(core.snippets.item(!item.stable ? msg_destroyed : true,item.name,item.icon,1,true,false).addClass(!item.stable ? 'destroyed' : ''));
                        });
                    }

                    if (obj.missed)
                        calc.append($('<span />').addClass('fa fa-ban')).append($('<span />').text("Verfehlt!"));
                    else {
                        calc.append($('<span />').addClass('calculation damage').append($('<span />').text(Math.round10(obj.damage,-1))).append($('<img />').attr('src','media/icons/atk1.gif')));
                        if (obj.protection.value)
                            calc.append($('<i />').addClass('fa fa-caret-right')).append($('<span />').addClass('calculation protection').append($('<span />').text(Math.round10(obj.protection.value,-1))).append($('<img />').attr('src','media/icons/atk2.gif')));
                    }

                    result.append($('<span />').addClass('final damage').append($('<span />').text(Math.round10(obj.damage - obj.protection.value, -2))).append($('<img />').attr('src','media/icons/damage.gif')));
                    if (obj.kills > 0)
                        result.append($('<span />').addClass('final kills').append($('<span />').text(obj.attacker.is_zombie ? '' : obj.kills)).append($('<img />').attr('src',obj.attacker.is_zombie ? 'media/icons/killc.gif' : 'media/icons/killz.gif')));
                }

            });

            return $('<div />').addClass('log-battle').data('expandable', true).append($('<div />').text(data.text)).append(details)
        };




    core.parts.log = function (data, target) {
        $.each(data, function(k,v) {
            var content;

            var rendered = renderers[v.type] ? renderers[v.type](v.data) : $('<div />').text('[RENDER ERROR] NO RENDERER PROVIDED FOR GIVEN MTYPE (' + v.type + ')!');
            var expandable = rendered.data('expandable');
            target.append(
                $('<div />').addClass('col rw-12 message' + (expandable ? ' pointer' : '')).append(
                    $('<div />').addClass(v.new ? 'timestamp new' : 'timestamp').text((new Date(v.time * 1000)).toLocaleTimeString())
                ).append(
                    content = $('<div />').addClass('content').append(rendered)
                ).click(function() {
                    $(this).find('.sub').slideToggle(200);
                })
            );
            content.find('.sub').hide();
        })
    };
})();
(function() {
    var render_others = function(data, target) {
        target.append($('<h3 />').text("Andere Spieler"));

        var row = $('<div />').addClass('row').appendTo(target);
        $.each(data, function(id, player) {
            var box = $('<div />').addClass('playerbox' + (player.escort ? ' escort' : '') + (player.local ? '' : ' unknown')).appendTo($('<div />').addClass('cell rw-4 padded').appendTo(row));

            box.append($('<b />').text(player.name));
            var bars = $('<div />').addClass('row').appendTo(box);

            box.attr('title', '-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty().css('width', 360);

                    var table;
                    content.append(
                        $('<b />').addClass('header').text(player.name)
                    ).append(table = $('<div />').addClass('row'));

                    var date = new Date(player.last_seen * 1000);

                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text("Beruf"))
                        .append($('<div />').addClass('cell rw-6 padded left').text(player.job))
                        .appendTo(content);

                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text("Spielgeschwindigkeit"))
                        .append($('<div />').addClass('cell rw-6 padded left').text(core.snippets.timestr(player.speed)))
                        .appendTo(content);
                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text("Letzte Aktivit\u00e4t"))
                        .append($('<div />').addClass('cell rw-6 padded left').text(date.toLocaleString()))
                        .appendTo(content);
                    $('<div />').addClass('row')
                        .append($('<div />').addClass('cell rw-6 padded b right').text(player.joke[0]))
                        .append($('<div />').addClass('cell rw-6 padded left').text(player.joke[1]))
                        .appendTo(content);
                }
            }));

            if (player.stats)
                core.parts.status_bars(bars, player.stats, true);
        })
    };

    var render_self = function(data, target) {
        var mpc_escort, mpc_ping;

        target
            .empty()
            .append($('<h3 />').text("Du"))

            .append($('<div />').append($('<label />').attr('title',"Ist diese Option aktiviert, erhalten andere Spieler begrenzte Kontrolle \u00fcber dich. Sie k\u00f6nnen dich bewegen, Gegenst\u00e4nde auf dich anwenden oder Gegenst\u00e4nde in deinen Rucksack legen.").qtip(game.render.html.qtip.ingame('right')).text("Befehle entgegennehmen").prepend(mpc_escort = $('<input />').attr('type', 'checkbox').prop('checked', data.escort))))
            .append($('<div />').append($('<label />').attr('title',"Aktiviere diese Option um anderen Spielern mitzuteilen, dass sie in den Chat kommen sollen. Der Chat-Aufruf wird nach 15 Minuten automatisch deaktiviert.").qtip(game.render.html.qtip.ingame('right')).text("Chat-Aufruf").prepend(mpc_ping = $('<input />').attr('type', 'checkbox').prop('checked', data.ping))))

            .find(':checkbox').customRadioCheck();

        $(mpc_escort).add(mpc_ping).click(function() {
            core.command('player/mp', {escort: mpc_escort.prop('checked') ? 1 : 0, ping: mpc_ping.prop('checked') ? 1 : 0}, true, function(ret) {
                if (!ret.success) {
                    game.render.html.notify('error', "Beim Speichern der Einstellungen ist ein Fehler aufgetreten.");
                    render_self(data,target);
                }
            });
        })
    };

    core.parts.mp_players = function(data, target) {

        var player_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-4 padded').appendTo(target));
        render_self(data.self, player_info);

        var others_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-8 padded').appendTo(target));
        render_others(data.others, others_info);

    };
})();core.popup = {
    spawn: function(dx,dy) {
        var popup = $('<div />').addClass('popup').css({
            height: dy,
            width: dx,
            top: 60,
            left: $(window).width()/2 - dx/2
        });

        var z = game.render.html.modal.blend(function() {
            popup.addClass('disabled').css({
                '-webkit-filter': 'blur(5px)',
                'filter': 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: 'scale(1.5)'
            }, 400, 'swing', function() {
                $(this).remove();
            });
        }, true, true);

        popup.css('z-index',z+1).on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).appendTo('body').css({
            opacity: 0,
            transform: 'scale(0.5)',
            'transition': 'filter 0.4s ease, -webkit-filter 0.4s ease',
            '-webkit-filter': 'blur(5px)',
            'filter': 'blur(5px)'
        }).animate({
            opacity: 1,
            transform: 'scale(1)'
        }, 500).css({
            '-webkit-filter': '',
            'filter': ''
        });

        return popup;
    },

    genericFilterLoader: function(callback, typeFilterData) {
        var popup = core.popup.spawn(500,300);

        var frame = $('<div />').addClass('row').appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 0,
                bottom: 36,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = $('<div />').addClass('row').css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 36,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn').text("Abbrechen").appendTo($('<div />').addClass('cell rw-4').appendTo(bottom)).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn').text("Anwenden").appendTo($('<div />').addClass('cell ro-4 rw-4').appendTo(bottom)).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters, class_cell;
        frame.append(
            $('<div />').addClass('row').append(
                $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text("Status")).append(typefilters = $('<div />').addClass('row')))
            ).append(
                class_cell = $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text("Kategorie")).append(classfilters = $('<div />').addClass('row')))
            )
        );

        $.each(typeFilterData, function(id, obj) {
            if (id == 'categories') return;
            var chk;
            typefilters.append(
                $('<div />').addClass('cell rw-6').append(
                    $('<label />').text(obj.name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('tid', id).prop('checked', obj.active).click(function() {
                            typeFilterData[id].active = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
        });

        var has_cats = false;
        $.each(typeFilterData.categories, function(name, active) {
            var chk;
            classfilters.append(
                $('<div />').addClass('cell rw-4').append(
                    $('<label />').text(name).prepend(
                        chk = $('<input />').attr('type', 'checkbox').data('cid', name).prop('checked', active).click(function() {
                            typeFilterData.categories[name] = $(this).is(':checked');
                        })
                    )
                )
            );
            chk.customRadioCheck();
            has_cats = true;
        });

        if (!has_cats)
            class_cell.hide();
        else class_cell.show();
    },

    genericBlueprintLoader: function(type, popup, data, frame, filters, close) {
        if (!popup) popup = core.popup.spawn(700,450);

        if (!frame || !filters || !close) {
            popup.empty();

            frame = $('<div />').addClass('row').appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: 24,
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            ).data('type-filters', {
                    'impossible': {active: true, name: "Unm\u00f6gliche Projekte"},
                    'locked': {active: true, name: "Gesperrte Projekte"},
                    'possible': {active: true, name: "Vorbereitete Projekte"},
                    'ready': {active: true, name: "M\u00f6gliche Projekte"},
                    'done': {active: true, name: "Abgeschlossene Projekte"},
                    'categories': {}
            }).on('filter', function() {
                    var typefilters = $(this).data('type-filters');

                    $(this).find('.blueprint').parent().hide();

                    var alias = $(this);
                    var all_enabled = true;
                    $.each(typefilters.categories, function(name, active) {
                        if (active)
                            alias.find('.blueprint[data-cats*="|' + name + '|"]').parent().show();
                        else all_enabled = false;
                    });
                    if (all_enabled) alias.find('.blueprint[data-cats="||"]').parent().show();

                    if (!typefilters.impossible.active)  $(this).find('.blueprint.red').parent().hide();
                    if (!typefilters.locked.active)      $(this).find('.blueprint.plain').parent().hide();
                    if (!typefilters.possible.active)    $(this).find('.blueprint.green').parent().hide();
                    if (!typefilters.ready.active)       $(this).find('.blueprint.green.active').parent().hide();
                    if (!typefilters.done.active)        $(this).find('.blueprint.blue').parent().hide();
            });

            $('<div />').addClass('row').append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text('Angezeigte Projekte filtern...').click(function() {
                core.popup.genericFilterLoader(function(a) {
                    frame.data('type-filters', a).trigger('filter');
                }, JSON.parse(JSON.stringify(frame.data('type-filters'))));
            }));
            close.append($('<div />').addClass('btn small').append($('<i/>').addClass('fa fa-times')).click(function() {
                popup.trigger('unpop');
            }));
        }

        var build_func = function(bdata) {
            frame.empty();
            var categories = {};
            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;
                $.each(v.categories, function(i,cat) {
                    categories[cat] = true;
                });
                frame.append($('<div />').addClass('cell padded rw-4').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
                    var prev_scroll = $('.popup').find('>*:first-child').scrollTop();
                    popup.addClass('disabled');
                    core.command('location/' + type, {build: k}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        frame.trigger('filter');
                        $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        popup.off('unpop').on('unpop', function() {
                            setTimeout(function() {
                                core.command();
                            }, 100);
                        })
                    }, true)
                })));
            });

            var tf = frame.data('type-filters');
            $.each(categories, function(name, t) {
                tf.categories[name] = typeof tf.categories[name] !== "undefined" ? tf.categories[name] : true;
            });
            frame.data('type-filters', tf);
        };

        if (!data) {
            frame.append(core.snippets.wait());
            core.command('location/' + type, {}, true, build_func);
        } else build_func(data);
    },

    builder: function() {
        core.popup.genericBlueprintLoader('builder');
    },
    maker: function() {
        core.popup.genericBlueprintLoader('maker');
    },
    fighter: function() {
        core.popup.genericBlueprintLoader('fighter');
    },

    map: function() {
        var dx = 800, dy = 602, border = 30, iconsize = 22;
        var gx = Math.floor(dx/iconsize), gy = Math.floor(dy/iconsize);
        var bsize = 64;
        var grid = {};

        for (var i = 0; i < gx; i++) {
            grid[i*iconsize] = {};
            for (var j = 0; j < gy; j++)
                grid[i*iconsize][j*iconsize] = false;
        }

        var popup = core.popup.spawn(dx+2, dy + bsize);

        popup.append(core.snippets.wait());

        core.command('map/data', {}, true, function(data) {
            popup.empty();

            var canvas = $('<canvas />').attr({
                'width':dx,
                'height':dy
            }).addClass('map').appendTo(popup);
            var ctx = canvas.get(0).getContext("2d");

            var bottom = $('<div />').addClass('row').css({
                height: bsize,
                'margin-top': dy
            }).appendTo(popup);

            var infopanel = $('<div />').hide().addClass('map panel').appendTo(
                $('<div />').addClass('cell rw-6 ro-3').css({
                    position: 'relative',
                    height: '100%'
                }).appendTo(bottom)
            );

            var sdmin = {}, sdmax = {};
            var init = true;

            var logf = function(k,v) {
                if (init) {
                    sdmax.x = sdmin.x = v.x;
                    sdmax.y = sdmin.y = v.y;
                    init = false;
                } else {
                    sdmax.x = Math.max(sdmax.x, v.x);
                    sdmax.y = Math.max(sdmax.y, v.y);
                    sdmin.x = Math.min(sdmin.x, v.x);
                    sdmin.y = Math.min(sdmin.y, v.y);
                }
            };

            var scalef = function(k,v) {
                v.x = border + (v.x - sdmin.x)/(sdmax.x - sdmin.x) * (dx-border*2);
                v.y = border + (v.y - sdmin.y)/(sdmax.y - sdmin.y) * (dy-border*2);
            };

            var p = function(v,dx,dy) {
                return {x: v.x-dx/2, y: v.y-dy/2}
            };

            var gridify = function(jqo) {
                if (jqo.data('grid'))
                    return false;
                else {
                    var px = jqo.data('x');
                    var py = jqo.data('y');

                    //Closest grid
                    var d = false;
                    var current = false;
                    $.each(grid, function(sx,line) {
                        $.each(line, function(sy,f) {
                            var x = parseInt(sx); var y = parseInt(sy);
                            var tmp;
                            if (!f && (((tmp = Math.pow(px-x,2)+Math.pow(py-y,2)) < d) || d === false )) {
                                d = tmp;
                                current = [x,y];
                            }
                        })
                    });

                    if (current) {
                        jqo.css({
                            left: current[0],
                            top: current[1]
                        });
                        grid[current[0]][current[1]] = true;
                    }

                    jqo.data('grid',true);
                    return true;
                }
            };

            var draw = function(mark, color) {
                if (!mark)  mark = [];
                if (!color) color = '#39ACE5';

                ctx.clearRect(0, 0, dx, dy);

                var subdraw = function(x0,x1,y0,y1,color, hidden) {

                    var line = Math.min(4,Math.max(2,Math.floor(Math.sqrt(Math.pow(x0-x1,2)+Math.pow(y0-y1,2))/15)));

                    ctx.save();
                    ctx.lineWidth = line;
                    ctx.strokeStyle = color;
                    ctx.fillStyle = color;
                    ctx.shadowColor = color;
                    ctx.shadowBlur = 15;
                    ctx.shadowOffsetX = 0;
                    ctx.shadowOffsetY = 0;

                    ctx.beginPath();
                    ctx.moveTo(x0,y0);
                    ctx.lineTo(x1,y1);
                    ctx.stroke();

                    ctx.lineWidth = 0;

                    ctx.arc(x0,y0, line/2, 0, 2 * Math.PI, false);
                    ctx.fill();
                    ctx.arc(x1,y1, line/2, 0, 2 * Math.PI, false);
                    ctx.fill();
                    ctx.restore();
                };

                $.each(data.network, function(start, nodes) {
                    $.each(nodes, function(k, end) {
                        var fstp = mark.indexOf(start); var lstp = mark.indexOf(end);

                        if (fstp >= 0 && lstp >= 0 && fstp != lstp && Math.abs(fstp-lstp) == 1)
                            return;

                        if (data.nodes[start].active && data.nodes[end].active)
                            subdraw(data.nodes[start].x,data.nodes[end].x,data.nodes[start].y,data.nodes[end].y,'#888888');
                    });
                });

                $.each(data.network, function(start, nodes) {
                    $.each(nodes, function(k, end) {

                        var fstp = mark.indexOf(start); var lstp = mark.indexOf(end);

                        if (!(fstp >= 0 && lstp >= 0 && fstp != lstp && Math.abs(fstp-lstp) == 1))
                            return;

                        subdraw(data.nodes[start].x,data.nodes[end].x,data.nodes[start].y,data.nodes[end].y, color);
                    });
                });
            };

            $.each(data.locations, logf);
            $.each(data.nodes, logf);

            var dif = Math.abs(Math.abs(sdmax.x - sdmin.x) - Math.abs(sdmax.y - sdmin.y));
            if (Math.abs(sdmax.x - sdmin.x) > Math.abs(sdmax.y - sdmin.y)) {
                sdmin.y -= dif/2;
                sdmax.y += dif/2;
            } else {
                sdmin.x -= dif/2;
                sdmax.x += dif/2;
            }

            $.each(data.locations, scalef);
            $.each(data.nodes, scalef);

            $.each(data.locations, function(k,v) {

                $.each(v.nodes, function(nnum, nk) {
                    data.nodes[nk].active = true;
                });

                var pos = p(v,iconsize,iconsize);
                popup.append(
                    $('<div />').addClass(k == data.current ? 'map location active' : (!data.read_only && v.energy <= data.radius ? 'map location' : 'map location unreachable')).attr({
                        'data-location':k,
                        'data-x': pos.x,
                        'data-y': pos.y
                    }).css({
                        top: pos.y,
                        left: pos.x
                    }).append(
                        $('<img />').attr('src','media/icons/places/' + v.icon)
                    ).mouseenter(function() {
                        draw($.objToArray(v.nodes,true), !data.read_only && v.energy <= data.radius ? '#39ACE5' : '#E3573B');

                        $(this).siblings().each(function() {
                            var id = $(this).data('location');
                            if (id != v.current && id != k && ($.objToArray(v.route, true).indexOf(id) >= 0))
                                $(this).addClass(!data.read_only && v.energy <= data.radius ? 'travel' : 'untravel');
                        });

                        infopanel.empty().stop().fadeIn(100).append(
                            $('<h3 />').text(v.name)
                        ).append(
                            $('<div />').addClass('row center').append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/distance.gif' )
                                ).append(
                                    $('<span />').text(v.distance)
                                )
                            ).append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/status_energy.gif' )
                                ).append(
                                    $('<span />').text(v.energy)
                                )
                            ).append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/zombie.gif' )
                                ).append(
                                    $('<span />').text(v.zombies)
                                )
                            ).append(
                                $('<div />').addClass('cell rw-3').append(
                                    $('<img />').attr('src', 'media/icons/status_weight.gif' )
                                ).append(
                                    $('<span />').text(v.weight === null ? 0 : v.weight )
                                )
                            )
                        )
                    }).mouseleave(function() {
                        draw();
                        infopanel.stop().fadeOut(100);
                        $(this).siblings('.travel, .untravel').removeClass('travel untravel');
                    }).click(function() {
                        if (data.read_only || v.energy > data.radius || k == data.current) return;

                        if (core.last.players) {
                            var esc_popup = core.popup.spawn(400);

                            var title;
                            esc_popup.append($('<h2 />').addClass('center').text(v.name));

                            esc_popup.append(
                                $('<div />').addClass('row').append(title = $('<div />').addClass('cell rw-12 padded').text("Wenn du dich alleine f\u00fcrchtest, kannst du andere Spieler bitten, dich zu begleiten. Oder noch besser, schick sie am besten direkt vor, nicht dass noch jemand (z.B. du) verletzt wird!"))
                            );

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
                                    .prepend($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Wer soll alles mitkommen?")))
                                    .append($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Und wie siehts mit dir aus?")))
                                    .append($('<div />').addClass('cell rw-12 padded').append(
                                        bhav = $('<select />')
                                            .append($('<option />').val('2').text("Mitgehen und helfen"))
                                            .append($('<option />').val('1').text("Nur mitgehen"))
                                            .append($('<option />').val('0').text("Die Stellung halten"))
                                            .val('1')
                                    ));

                                bhav.selectric();
                                check_row.find(':checkbox').customRadioCheck();

                            } else title.text("Bist du sicher, dass du diesen Ort betreten m\u00f6chtest? Er ist weit weg, und riecht auch bestimmt nicht sehr gut...");

                            esc_popup.append($('<div />').addClass('row')
                                .append($('<div />').addClass('cell rw-8 padded').append(
                                    $('<div />').addClass('btn').text("Los gehts!").click(function() {

                                        var cfg = {to: k, follow: 1};
                                        if (check_row.children().length) {
                                            cfg.follow = parseInt(bhav.val()) > 0 ? 1 : 0;
                                            cfg.support = parseInt(bhav.val()) == 2 ? 1 : 0;
                                            cfg.co = [];
                                            $.each(check_row.find(':checkbox:checked'), function() {
                                                cfg.co.push($(this).attr('data-id'))
                                            })
                                        }

                                        esc_popup.trigger('unpop');
                                        popup.addClass('disabled');
                                        core.command('map/go', cfg, true, function(data) {
                                            popup.removeClass('disabled');
                                            if (data.success) {
                                                popup.trigger('unpop');
                                                core.command();
                                            }
                                        });
                                    })))
                                .append($('<div />').addClass('cell rw-4 padded').append(
                                    $('<div />').addClass('btn').text("Abbrechen").click(function() {
                                        esc_popup.trigger('unpop');
                                    })))
                            );
                        } else {
                            popup.addClass('disabled');
                            core.command('map/go', {to: k, follow: 1}, true, function(data) {
                                popup.removeClass('disabled');
                                if (data.success) {
                                    popup.trigger('unpop');
                                    core.command();
                                }
                            });
                        }


                    })
                )
            });

            popup.find('.map.location').each(function() {
                gridify($(this));
            });

            draw();
        });
    }
};(function() {

    var fill_timesettings_var = function(data, target, lock) {
        var set_row;
        target.append($('<h3 />').text("Spielgeschwindigkeit"))
            .append($('<p />').addClass('justify').text(game.i18n("Du kannst die Spielgeschwindigkeit jederzeit deinen Bed\u00fcrfnissen anpassen. Bedenke jedoch, dass eine \u00c4nderung nur alle :minutes m\u00f6glich ist.", {':minutes': core.snippets.timestr(data.interval)})))
            .append(set_row = $('<div />').addClass('row center'));

        $.each(data.selection, function(k,v) {
            set_row.addClass(lock ? 'disabled' : '').prepend(
                $('<label />').attr('title', core.snippets.timestr(v)).append(
                    $('<input />').attr({
                        name: 'set_time',
                        type: 'radio',
                        value: k
                    }).prop("checked", k == data.player).click(function() {
                        if (!confirm("Bist du sicher, dass du die Spielgeschwindigkeit \u00e4ndern m\u00f6chtest?"))
                            return false;
                        core.command('player/reflux', {set: $(this).val()})
                    })
                ).qtip(game.render.html.qtip.ingame('bottom'))
            )
        });
        set_row.find('input').customRadioCheck();
        set_row.find('>label').css('margin', 0);

        target.append($('<div />').addClass('center row').append($('<b />').text("Aktuelle Geschwindigkeit")).append($('<span />').text(core.snippets.timestr(data.game))));

        if (lock) {
            var ct = $('<p />').appendTo(target);
            core.snippets.countdown(lock, function(s, v) {
                if (v == 0) {
                    set_row.removeClass('disabled');
                    ct.remove();
                    game.render.html.notify('success', "Die Sperre ist abgelaufen - ab sofort kannst du die Spielgeschwindigkeit wieder \u00e4ndern!");
                    return false;
                }

                ct.html(game.i18n("\u00c4nderung in <i> :time <\/i> wieder m\u00f6glich.", {':time': s}));
                return true;
            })
        }
    };

    var fill_timesettings_stat = function(data, target, lock) {
        var set_row, button;
        target.append($('<h3 />').text("Spiel pausieren"))
            .append($('<p />').addClass('justify').text(game.i18n("Du kannst das Spiel jederzeit anhalten. Allerdings muss eine Pause mindestens :minutes1 dauern und du musst :minutes2 warten, bis du erneut pausieren kannst.", {':minutes1': core.snippets.timestr(data.duration),':minutes2': core.snippets.timestr(data.interval)})))
            .append(set_row = $('<div />').addClass('row center'));

        $('<div />').addClass('cell rw-12 padded').appendTo(set_row).append(
            button = $('<div />').addClass('btn').addClass(lock ? 'disabled' : '').text("Pausieren").click(function() {
                if (confirm("Bist du sicher, dass du das Spiel jetzt pausieren willst?"))
                    core.command('player/pause', {set: 1});
            })
        );

        if (lock) {
            var ct = $('<p />').appendTo(target);
            core.snippets.countdown(lock, function(s, v) {
                if (v == 0) {
                    button.removeClass('disabled');
                    ct.remove();
                    game.render.html.notify('success', "Die Sperre ist abgelaufen - du kannst das Spiel ab sofort wieder pausieren!");
                    return false;
                }

                ct.html(game.i18n("N\u00e4chte Pause in <i> :time <\/i> m\u00f6glich.", {':time': s}));
                return true;
            })
        }
    };

    var fill_battleai = function(target, data) {
        var button;

        target.empty().append($('<h3 />').text("Kampfverhalten"));

        var bhav_select, bhav = $('<div />').addClass('row').appendTo(target);
        bhav.append($('<b />').text("Kampfstrategie"));
        bhav.append($('<div />').addClass('cell rw-6 padded').append($('<label />').attr('title',"Der ausgew\u00e4hlte Kampfstil beeinflusst deine Waffen- und Gegnerauswahl. Offensive Spieler werden versuchen, so viel Schaden anzurichten wie m\u00f6glich. Defensive Spieler werden versuchen, Zombies so gut es geht auf Abstand zu halten.").qtip(game.render.html.qtip.ingame('top')).prepend(bhav_select = $('<select />'))));

        bhav_select
            .append($('<option />').text("Defensiv").attr('value','1'))
            .append($('<option />').text("Ausgeglichen").attr('value','2'))
            .append($('<option />').text("Offensiv").attr('value','3'))
            .val(data.type).on('change', function() {
                button.removeClass('disabled')
            }).selectric();

        var sw_energy, sw_breakable, sw_ammocache;
        var sw = $('<div />').addClass('row').appendTo(target);
        sw.append($('<b />').text("Verwendung einzelner Waffenarten sperren"));
        sw
            .append($('<div />').addClass('cell rw-6 padded').append($('<label />').attr('title',"Ist diese Option aktiviert, wirst du im Kampf keine Waffen einsetzen, die Energie verbrauchen.").qtip(game.render.html.qtip.ingame('top')).text("Energiewaffen").prepend(sw_energy = $('<input />').attr('type', 'checkbox').prop('checked', data.weapons.energy).prop('disabled', (data.weapons.energy === 'locked')))))
            .append($('<div />').addClass('cell rw-6 padded').append($('<label />').attr('title',"Ist diese Option aktiviert, wirst du im Kampf keine Waffen verwenden, die beim Einsatz zerst\u00f6rt werden (z.B. Wasserbombe).").qtip(game.render.html.qtip.ingame('top')).text("Wurfgeschosse").prepend(sw_breakable = $('<input />').attr('type', 'checkbox').prop('checked', data.weapons.throw))))
            .append($('<div />').addClass('cell rw-6 padded').append($('<label />').attr('title',"Ist diese Option aktiviert, wirst du im Kampf keine Waffen verwenden, die einen internen Munitionsspeicher haben (z.B. Wasserpistole).").qtip(game.render.html.qtip.ingame('top')).text("Verbrauchswaffen").prepend(sw_ammocache = $('<input />').attr('type', 'checkbox').prop('checked', data.weapons.tank))));

        var mun = $('<div />').addClass('row').appendTo(target);
        mun.append($('<b />').text("Verwendung einzelner Munitionstypen sperren"));

        var mun_elems = {};

        $.each(data.ammo, function(k,v) {
            mun.append($('<div />').addClass('cell rw-2 padded').append($('<label />').attr('title', game.i18n("Ist diese Option aktiviert, werden im Kampf keine Waffen verwendet, die diese Munition (:item) verwenden.", {':item': v.name})).qtip(game.render.html.qtip.ingame('top')).append($('<img />').attr('src', 'media/icons/'+ v.icon + '.gif')).prepend(mun_elems[k] = $('<input />').attr('type', 'checkbox').prop('checked', v.locked))))
        });

        target.find(':checkbox').click(function() {
            button.removeClass('disabled')
        }).customRadioCheck();

        target.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-6 ro-6').append(
            button = $('<div />').addClass('btn btn-icon disabled')
                .append($('<span />').addClass('btn-icon-inner').append($('<i />').addClass('fa fa-check')))
                .append($('<span />').text("Speichern"))
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
                            game.render.html.notify('error', "Beim Speichern der Einstellungen ist ein Fehler aufgetreten.");
                            fill_battleai(target, data);
                        } else button.addClass('disabled')
                    })
                })
        )));

    };

    core.parts.settings = function(data, target) {
        var time_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-5 padded').appendTo(target));
        if (data.clock.time_mode == 0) fill_timesettings_stat(data.clock.time_settings, time_settings, data.clock.locked);
        if (data.clock.time_mode == 1) fill_timesettings_var(data.clock.time_settings, time_settings, data.clock.locked);

        var bai_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-7 padded').appendTo(target));
        fill_battleai(bai_settings, data.ai);
    };
})();(function() {
    core.snippets.timestr = function(i) {
        var cache = [["Woche","Wochen"],["Tag","Tage"],["Stunde","Stunden"],["Minute", "Minuten"],["Sekunde", "Sekunden"]];
        var times = [604800,86400,3600,60,1];

        i = Math.abs(i);
        if (i == 0) return ("0 " + cache[cache.length - 1][1]);

        var split = [];
        $.each(times, function(k,v) {
            split[k] = Math.floor(i/v);
            i -= split[k] * v;
        });

        split = $.map(split, function(v, k) {
            if (v == 0) return null;
            return v + " " + cache[k][v == 1 ? 0 : 1]
        });

        if (split.length == 1) return split[0];
        else {
            var tmp = split.splice(-1,1);
            return split.join(', ') + " " + "und" + " " + tmp[0];
        }
    };

    core.snippets.countdown = function(initial, callback) {
        if (callback(core.snippets.timestr(initial), initial) && initial >= 0)
            window.setTimeout(function() {core.snippets.countdown(initial - 1, callback)}, 1000);
    };

    core.snippets.item = function(show_title, name, icon, count, is_static, in_inventory) {
        var container = $(in_inventory ? '<li />' : '<div />').addClass(in_inventory ? '' : 'item').append(
            $('<img />').attr('src', 'media/icons/' + icon + '.gif')
        );

        if (count) {
            if (is_static && count > 1) container.append($('<div />').addClass('staticCount').text(count));
            else if (!is_static && count > 0) container.append($('<div />').addClass('instanceCount').text(count));
        }

        if (show_title)
            container.attr('title','-').qtip(game.render.html.qtip.ingame('bottom',{
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty();
                    if (typeof show_title === "string")
                        content.append($('<b />').addClass('header').text(name)).append($('<span />').text(show_title));
                    else content.append($('<span />').text(name));
                }
            }));

        return container;
    };

    //Ext mode: extend (default), static, tooltip
    core.snippets.button = function(action, call, ext_mode) {
        if (typeof action === "string")
            return $('<div />').addClass('btn').text(action).click(call);
        else {

            ext_mode = ext_mode || 'extend';

            var button = $('<div />');
            var ext = $('<div />').addClass('row consequence');

            var block;

            var g = ext_mode == 'tooltip' ? 12 : 6;

            if (action.remaining != 0) {
                if ($.objToArray(action.requires).length) {

                    ext.append(
                        block = $('<div />').addClass('cell rw-'+g+' padded').append(
                            $('<i />').addClass('separator').text("Erfordert")
                        )
                    );
                    $.each(action.requires, function(k,v) {
                        block.append(
                            $('<div />').addClass('group').append(
                                v.icon ? $('<img />').attr('src', 'media/icons/' + v.icon + '.gif') : $('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')
                            ).append(
                                $('<span />').text(v.value)
                            )
                        );
                    })
                }

                if ($.objToArray(action.effects).length) {
                    ext.append(
                        block = $('<div />').addClass('cell rw-'+g+' padded').append(
                            $('<i />').addClass('separator').text("Effekte")
                        )
                    );
                    $.each(action.effects, function(k,v) {
                        block.append(
                            $('<div />').addClass('group').addClass(v.color || 'default').append(
                                v.icon ? $('<img />').attr('src', 'media/icons/' + v.icon + '.gif') : $('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')
                            ).append(
                                $('<span />').text(v.value)
                            )
                        );
                    })
                }

                if ($.objToArray(action.sides).length) {
                    ext.append(
                        block = $('<div />').addClass('cell rw-'+g+' padded').append(
                            $('<i />').addClass('separator').text("Effekte auf ausgew\u00e4hlten Spieler")
                        )
                    );
                    $.each(action.sides, function(k,v) {
                        block.append(
                            $('<div />').addClass('group').addClass(v.color).append(
                                v.icon ? $('<img />').attr('src', 'media/icons/' + v.icon + '.gif') : $('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')
                            ).append(
                                $('<span />').text(v.value)
                            )
                        );
                    })
                }
            } else button.addClass('disabled');

            button
                .addClass('btn btn-zv ' + (action.skin ? 'btn-zv-skinned-' + action.skin : '') )
                .append(
                    $('<span />').text(action.description)
                ).click(function () {
                    // Hide all QTips
                    $('.qtip').qtip('hide');

                    if (action.popup) {
                        core.popup[action.popup]();
                        return;
                    }

                    if (call && call() === false) return;

                    if (action.escort) {
                        var popup = core.popup.spawn(400);

                        popup.append($('<h2 />').addClass('center').text(action.description));

                        popup.append(
                            $('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').text("Bitte w\u00e4hle einen Spieler aus, auf den du diese Aktion anwenden willst. Du kannst nur Spieler ausw\u00e4hlen, die sich am gleichen Ort befinden wie du und Befehle von dir entgegennehmen."))
                        );

                        if (core.last.players.others)
                            $.each(core.last.players.others, function(id, player) {
                                popup.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append(
                                    $('<div />').addClass('btn btn-zv' + (player.escort ? '' : ' disabled')).text(player.name).click(function() {
                                        if (!player.escort || !confirm(game.i18n("Bist du sicher, dass du diese Aktion auf :name anwenden m\u00f6chtest?", {':name': player.name}))) return;

                                        popup.trigger('unpop');
                                        core.command('act/item', {action: action.action, item: action.target, co: player.id});
                                    })
                                )))
                            });

                        popup.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append(
                            $('<div />').addClass('btn').text("Abbrechen").click(function() {
                                popup.trigger('unpop');
                            }))
                        ));

                    } else core.command('act/item', {action: action.action, item: action.target});
                });

            switch (ext_mode) {
                case 'static':
                    button.append(ext);
                    break;
                case 'tooltip':case 'nested':
                    if (!action.tooltip && action.remaining < 0 && !ext.children().size()) break;

                    var template = (ext_mode == 'nested') ? game.render.html.qtip.help : game.render.html.qtip.ingame;

                    button.attr('title','-').qtip(template((ext_mode == 'nested') ? 'right' : 'bottom',{
                        render: function(event,api) {
                            $(this).css('width',$(this).css('max-width'));

                            var content = $(this).find('.qtip-content').empty().append(
                                (ext_mode == 'nested') ? null : $('<b />').addClass('header').text(action.description)
                            );

                            if (action.tooltip)
                                content.append($('<span />').text(action.tooltip)).append('<span class="separator" />');
                            if (ext.children().size())
                                content.append(ext).append('<span class="separator" />');
                            if (action.remaining >= 0)
                                content.append($('<div />').addClass('note').text(game.i18n("Du kannst diese Aktion noch :num mal einsetzen.", {':num': action.remaining})));
                        }},true)
                    );
                    break;
                case 'extend':default:
                    button.append(ext.hide()).mouseenter(function() {
                        if (ext.children().size()) ext.stop().slideDown('fast');
                    }).mouseleave(function() {
                        if (ext.children().size()) ext.stop().slideUp('slow');
                    });
                    break;
            }

            return button;
        }
    };

    core.snippets.wait = function() {
        return $('<div />').addClass('center').append(
            $('<i/>').addClass('fa fa-circle-o-notch fa-spin')
        ).append($('<span />').text("Wird geladen ..."))
    };

    /**
     *
     * @param {Blueprint} blueprint
     * @param {int} energy
     * @param {int} zombies
     * @param {Blueprint[]} lib
     * @param {Function} callback
     * @returns {*}
     */
    core.snippets.blueprint = function(blueprint, energy, zombies, lib, callback) {
        var button = $('<div />').addClass('blueprint').attr('title','-').attr('data-bid', blueprint.id);
        var ext = $('<div />').addClass('row details');

        var mt_in = $('<div />').addClass('cell rw-12').appendTo(ext);
        var mt_out = $('<div />').addClass('cell rw-12').appendTo(ext);
        var mt_zmb = $('<div />').addClass('cell rw-12').appendTo(ext);

        button.attr('data-cats', '|' + $.objToArray(blueprint.categories, true).join('|') + '|');

        if (blueprint.build)
            button.addClass('blue');
        else if (blueprint.build_possible && blueprint.slot_open) {
            button.addClass('green');
            var active = true;
            $.each(blueprint.material_in, function(k,v) {
                active = active && (v.have >= v.count);
            });
            if (active) {
                button.addClass('active');
                if (typeof callback == "function")
                    button.click(callback);
            }
        }
        else if (!blueprint.slot_open)
            button.addClass('red');
        else button.addClass('plain');

        if (blueprint.energy || $.objToArray(blueprint.material_in).length)
            mt_in.append($('<i/>').text("Erfordert"));
        if (blueprint.decay_speed || blueprint.repair || blueprint.defense || $.objToArray(blueprint.material_out).length)
            mt_out.append($('<i/>').text("Produziert"));

        if (blueprint.energy)
            mt_in.append(
                $('<div />').addClass('group').append(
                    $('<img />').attr('src', 'media/icons/status_energy.gif')
                ).append(
                    $('<span />').append($('<b />').addClass(energy >= blueprint.energy ? 'green' : 'red').text(blueprint.energy))
                )
            );

        if (blueprint.defense)
            mt_out.append(
                $('<div />').addClass('group').append(
                    $('<img />').attr('src', 'media/icons/defense.gif')
                ).append(
                    $('<span />').append($('<span />').addClass(blueprint.defense > 0 ? 'green' : 'red').text((blueprint.defense > 0 ? '+' : '') + blueprint.defense))
                )
            );

        if (blueprint.repair)
            mt_out.append(
                $('<div />').addClass('group').append(
                    $('<img />').attr('src', 'media/icons/decay.gif')
                ).append(
                    $('<span />').append($('<span />').addClass(blueprint.repair > 0 ? 'green' : 'red').text((blueprint.repair > 0 ? '+' : '') + blueprint.repair + "%"))
                )
            );

        if (blueprint.decay_speed)
            mt_out.append(
                $('<div />').addClass('group').append(
                    $('<img />').attr('src', 'media/icons/decay' + (blueprint.decay_speed > 0 ? '2' : '3') + '.gif')
                )
            );

        if (blueprint.zombies)
            mt_zmb.append($('<i/>').text("T\u00f6tet")).append(
                $('<div />').addClass('group').append(
                    $('<img />').attr('src', 'media/icons/zombie.gif')
                ).append(
                    Math.min(zombies, blueprint.zombies[0]) == Math.min(zombies, blueprint.zombies[1])
                        ? $('<span />').append($('<b />').addClass('green').text(Math.min(zombies, blueprint.zombies[0]))).append($('<span />').text('/' + zombies))
                        : $('<span />').append($('<b />').addClass('green').text(Math.min(zombies, blueprint.zombies[0]) + ' - ' + Math.min(zombies, blueprint.zombies[1]))).append($('<span />').text('/' + zombies))
                )
            );

        var f = function(target, hideScale) {
            return function(k,v) {
                target.append(
                    $('<div />').addClass('group').append(
                        v.icon ? $('<img />').attr('src', 'media/icons/' + v.icon + '.gif') : $('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')
                    ).append(
                        hideScale
                            ? $('<span />').append($('<b />').text(v.count))
                            : $('<span />').append($('<b />').addClass(v.have >= v.count ? 'green' : 'red').text(v.have)).append($('<span />').text('/' + v.count))
                    )
                );
            }
        };

        $.each(blueprint.material_in, f(mt_in));
        $.each(blueprint.material_out, f(mt_out,true));

        button.qtip(game.render.html.qtip.ingame('bottom',{
            render: function() {
                var content = $(this).find('.qtip-content').empty().append(
                    $('<b />').addClass('header').text(blueprint.name)
                );

                if (blueprint.description)
                    content.append($('<span />').text(blueprint.description)).append('<span class="separator" />');

                if (blueprint.build)
                    content.append($('<div />').addClass(blueprint.zombies ? 'point failure' : 'point success').text(blueprint.zombies ? "Diese Verteidigungsm\u00f6glichkeit wurde bereits eingesetzt." : "Dieses Projekt wurde bereits gebaut."));
                else if (!blueprint.slot_open) {
                    content.append($('<div />').addClass('point failure').text("Du hast bereits ein \u00e4hnliches Projekt gebaut."));
                } else {

                    if (blueprint.steps_max > 1)
                        content.append($('<span />').text(game.i18n(blueprint.zombies ? "Hiermit kannst du :num mal Zombies angreifen." : "Du kannst dieses Projekt :num mal bauen.",{':num': blueprint.steps_max}))).append('<span class="separator" />');
                    else if (blueprint.steps_max == 0 && !blueprint.zombies)
                        content.append($('<span />').text("Dieses Projekt kann unbegrenzt oft gebaut werden.")).append('<span class="separator" />');

                    content.append($('<span />').text("Vorraussetzungen"));

                    $.each(blueprint.requires, function(k,v) {
                        var cache = [];
                        var ok = false;
                        $.each(v, function(ki,vi) {
                            if (lib[vi]) {
                                cache.push(lib[vi].name);
                                if (lib[vi].build) ok = true;
                            }
                        });
                        if (cache.length)
                            content.append($('<div />').addClass('point').addClass(ok ? 'success' : 'failure').text(cache.join(', ')));
                        else content.append($('<div />').addClass('point success').text("Keine besonderen Vorraussetzungen"));
                    });

                    content.append('<span class="separator" />');

                    var cache = [];
                    $.each(lib,function(key,bp) {
                        $.each(bp.requires,function(k,req) {
                            $.each(req,function(k,vi) {
                                if (vi == blueprint.id)
                                    cache.push(bp.name);
                            });
                        });
                    });

                    if (cache.length) {
                        content.append($('<span />').text("Erm\u00f6glicht"));
                        $.each(cache, function(k,v) {
                            content.append($('<div />').addClass('point').text(v));
                        });
                        content.append('<span class="separator" />');
                    }

                    cache = [];
                    $.each(blueprint.occupies, function(k,occ) {
                        $.each(lib, function(key, bp) {
                            if (bp.id != blueprint.id && !bp.hidden && $.inArray(bp.id, cache) < 0 && $.inArray(occ, $.objToArray(bp.occupies, true)) >= 0)
                                cache.push(bp.id)
                        });
                    });

                    if (cache.length) {
                        content.append($('<span />').text("Verhindert"));
                        $.each(cache, function(k,v) {
                            content.append($('<div />').addClass('point').text(lib[v].name));
                        })
                    }
                }
            }})
        );

        var desc;
        button.append(
            desc = $('<span />').text(blueprint.name)
        ).append($('<div />').addClass('ribbon')).append(ext);

        if (!blueprint.build && blueprint.steps_max > 1)
            desc.append(
                $('<i/>').text( '(' + (1+blueprint.steps_current) + ' / ' + blueprint.steps_max + ')')
            );

        return button;
    }
})();(function() {
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
            case 1: return "Hunger";
            case 2: return "Durst";
            case 3: return "Gesundheit";
            case 4: return "M\u00fcdigkeit";
            case 5: return "Energie";
            case 6: return "Alkohol";
            case 7: return "Verstrahlung";
            case 8: return "Zombie-Infektion";
            case 9: return "Eisige K\u00e4lte";
            default: return'???';
        }
    };

    var num_decode_description = function(n) {
        switch (n) {
            case 1: return "Mit leerem Magen f\u00e4llt der Kampf ums \u00dcberleben schwer. Iss regelm\u00e4\u00dfig, ansonsten verlierst du Energie und Gesundheit.";
            case 2: return "Es ist nicht leicht, in der \u00d6dnis Wasser zu finden - nichtsdestotrotz ist es essentiell f\u00fcr dein \u00dcberleben.";
            case 3: return "Du stirbst, wenn deine Gesundheit den Wert 0 erreicht. Gesundheit regeneriert sich von alleine, wenn du genug gegessen und getrunken hast. Du kannst deine Gesundheit aber auch durch die Verwendung verschiedener Items verbessern.";
            case 4: return "Zombies m\u00fcssen nicht schlafen - du hingegen schon! Du solltest \u00dcberm\u00fcdung um jeden Preis vermeiden, also schlafe regelm\u00e4\u00dfig.";
            case 5: return "Du brauchst Energie, um Aktionen durchf\u00fchren zu k\u00f6nnen. Energie regeneriert sich von alleine, wenn du bei guter Gesundheit und nicht hungrig\/durstig bist. Es gibt allerdings auch einige Items, die Energie regenerieren.";
            case 6: return "Mit ordentlich Promille im Blut wird das Leben nach der Apokalypse gleich viel ertr\u00e4glicher. Leider wird es auch k\u00fcrzer, denn wenn du v\u00f6llig abgef\u00fcllt in der Ecke liegst, kannst du dich nicht wirklich gut gegen Zombies verteidigen. Wenigstens um die Langzeitsch\u00e4den an deiner Leber brauchst du dich nicht mehr zu sorgen ...";
            case 7: return "Du warst Strahlung ausgesetzt! Das ist relativ ungesund, und dein K\u00f6rper kann die strahlenden Partikel nur langsam abbauen. W\u00e4hrend eine geringe Strahlendosis noch vertretbar ist, k\u00f6nnen h\u00f6here Strahlenmengen schnell dein Leben bedrohen!";
            case 8: return "Ohje, das ist gar nicht gut... Offensichtlich bist du mit dem Zombievirus infiziert. Du solltest unbedingt ein Heilmittel finden, ansonsten wirst du sehr bald ins Unleben \u00fcbertreten...";
            case 9: return "Der kalte Wind bl\u00e4st dir um die Ohren... allzu lange kann du hier nicht bleiben, wenn du nicht erfrieren willst.";
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

        table.append($('<h4 />').addClass('center').text("Absolute Einfl\u00fcsse"));
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

        table.append($('<h4 />').addClass('center').text("Relative Einfl\u00fcsse"));
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

        table.append($('<h4 />').addClass('center').text("Berechnung"));

        $('<div />').addClass('row line').appendTo(table).append($('<div />').addClass('cell rw-6 right').text(Math.round(100*sum_absolute_p)/100)).append($('<div />').addClass('cell rw-6 left').text(-Math.round(100*sum_absolute_n)/100));
        $('<div />').addClass('row line').appendTo(table).append($('<div />').addClass('cell rw-6 right').text(Math.round(sum_relative_p * 100) + "%")).append($('<div />').addClass('cell rw-6 left').text(Math.round(sum_relative_n * 100) + "%"));
        $('<div />').addClass('row line sum').appendTo(table).append($('<div />').addClass('cell rw-6 right sum-green').text(Math.round(100 * sum_p)/100)).append($('<div />').addClass('cell rw-6 left sum sum-red').text(-Math.round(100 * sum_n)/100));

        table.append($('<h4 />').addClass('center').text("Gesamt"));
        $('<div />').addClass('row line sum').appendTo(table).append($('<div />').addClass('cell rw-12 center').addClass(sum >= 0 ? 'positive' : 'negative').text(Math.round(100*sum)/100));
        return table;
    };

    var make_small_bar = function(type, value) {
        if (type === null) return $('<div />').addClass('cell rw-4').html('&nbsp;');
        return $('<div />').addClass('cell rw-4 smallpad').append(
            $('<div />').addClass('varbar').append(
                $('<div />').css({width: value + '%', background: num_decode_color(type)})
            )
        );
    };

    var make_bar = function(type, value, effects, inline) {
        var bar = $('<div />').addClass('cell rw-' + (inline ? 12 : 4)).append(
            $('<div />').addClass('bar').append(
                $('<img />').attr('src', 'media/icons/status_' + num_decode_str(type) + '.gif')
            ).append(
                $('<div />').addClass('background').append(
                    $('<div />').css({width: value + '%', background: num_decode_color(type)})
                )
            )
        );

        if (!inline) bar.attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function (event, api) {
                    var content = $(this).find('.qtip-content').empty().append(
                        $('<b />').addClass('header').text(num_decode_title(type))
                    ).append(
                        $('<div />').addClass('note center').text(game.i18n("Aktueller Wert: :num", {':num': value}))
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
                $('<img />').addClass('status').attr('src','media/icons/status_' + num_decode_str(v) + '.gif').attr('title',small ? '' : '-').qtip(game.render.html.qtip.ingame('bottom', {
                        render: function (event, api) {
                            var content = $(this).find('.qtip-content').empty().append(
                                $('<b />').addClass('header').text(num_decode_title(v))
                            ).append(
                                $('<div />').addClass('row').append(make_bar(v,bars[v].value,bars[v].buffs, true))
                            ).append(
                                $('<div />').addClass('note center').text(game.i18n("Aktueller Wert: :num", {':num': Math.round(100*bars[v].value)/100}))
                            ).append(
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
                $('<img />').addClass('buff').attr('src','media/icons/' + v.icon + '.gif').attr('title',small ? '' : '-').qtip(game.render.html.qtip.ingame('bottom', {
                        render: function (event, api) {
                            var content = $(this).find('.qtip-content').empty().append(
                                $('<b />').addClass('header').text(v.name)
                            ).append(
                                $('<span />').text(v.desc)
                            );
                        }
                    })
                ).appendTo(target);
            });
        }

        return bar;
    };

    core.parts.status_bars = function(target, data, small) {
        var order = [5,3,1,4,null,2];

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
            main = $('<div />').addClass('row').append(
                $('<div />').addClass('cell rw-9').append(
                    bars = $('<div />').addClass('row')
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

        var timestr, datestr;
        $('<div />').addClass('datebox').append(
            $('<div />').addClass('row').append(
                $('<div />').addClass('cell rw-4').append(
                    $('<i />').addClass('fa fa-clock-o')
                ).append(
                    timestr = $('<span />')
                )
            ).append(
                $('<div />').addClass('cell rw-8').append(
                    $('<i />').addClass('fa fa-calendar')
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
                countdown.text("Weiter");
                clockbox.addClass('pointer').click(function() {
                    core.command();
                });
                return;
            }

            var datetime = new Date(ingame);
            var date = ["So","Mo","Di","Mi","Do","Fr","Sa"][datetime.getDay()] + ', ' + datetime.toLocaleDateString();
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
                    $('<div />').text(scm[i] < 10 ? '0' + scm[i] : scm[i]).appendTo(countdown);

            window.requestAnimationFrame(updater);
        };

        updater();
    }
})();/**
 * @name Material
 * @type Object
 * @property {int} count
 * @property {int} have
 * @property {string} icon
 * @property {string} name
 */

/**
 * @name Blueprint
 * @type Object
 * @property {bool} build
 * @property {bool} build_possible
 * @property {string[]} categories
 * @property {int} decay_speed
 * @property {int} defense
 * @property {string} description
 * @property {int} energy
 * @property {bool} hidden
 * @property {string} id
 * @property {Material[]} material_in
 * @property {Material[]} material_out
 * @property {string} name
 * @property {string[]} occupies
 * @property {int} repair
 * @property {string[]} requires
 * @property {bool} slot_open
 * @property {int} steps_current
 * @property {int} steps_max
 * @property {bool|int[]} zombies
 */
