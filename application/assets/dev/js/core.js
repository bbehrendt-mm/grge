
/**
 * @var {Core} core
 */
core = {
    parts: {},
    snippets: {},

    version: '2.1.0-0-0-255',

    last: {},
    plugins: {},

    cache: {},

    sessiondata: {},


    /**
     * Retrieves an object from cache
     * @param id {string} Identifier
     * @param [initalization] {object|function} Default return value.
     * @returns {*}
     */
    cache_get: function(id, initalization) {
        if (typeof this.cache[id] !== 'undefined') return this.cache[id];
        else if (!initalization) return null;
        else if (typeof initalization === 'function') return initalization(id);
        else return initalization;
    },

    cache_put: function(id, data) {
        this.cache[id] = data;
    },

    session: function(key, data) {
        return (typeof data == 'undefined')
            ? core.sessiondata[key]
            : (core.sessiondata[key] = data);
    },

    renderLog: function(target) {
        this.command('game/logs',{}, true, function(data) {
            if (data.log)
                core.parts.log(data.log,$('<div />').addClass('row log_box').appendTo(target.empty()));
        }, true)
    },

    command: function(url, args, background, callback, no_clean, finished) {
        if (!url)
            url = 'japi/game/data';
        else url = 'japi/' + url;

        if (!background) game.render.html.modal.work();

        var scroll = $(document).scrollTop();

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
                game.reset(true);
                return;
            } if (callback)
                callback(data);
            else if (data) core.render(data, $('#content').empty());

            if (finished) finished(data);

            $(document).scrollTop(scroll)
        });
    },

    render: function(data, target) {
                    console.log(data);
        
        core.last = data;

        if (core.parts.admin) core.parts.admin.controls($('<div />').addClass('cell rw-12 padded').appendTo(NF.row().appendTo(target)));

        if (data.location) {
            var location_box = NF.row().appendTo(target);
            core.parts.location(data.location, location_box);
        }

        var action_box = $('<div />').addClass('row action_box ' + (data.location.meta.outside ? 'outside' : 'inside')).appendTo(target);

        if (data.location.meta.css) {
            location_box.addClass('custom custom-' + data.location.meta.css);
            action_box.addClass('custom custom-' + data.location.meta.css);
        }

        if (data.players && data.players.messages)
            action_box.append($('<div />').addClass('note control').text("Du hast neue Nachrichten!"));

        var auto_select = $('<select />').appendTo($('<div />').addClass('cell rw-12 padded hide-desktop control').appendTo(action_box))
            .append($('<option />').val('#inv_container').text(game.storage.get('settings','heroid_ui') == 'tab' ? "Gegenst\u00e4nde" : "Gegenst\u00e4nde & Heldentaten"))
            .append($('<option />').val('#rpg_container').text("Kampfausr\u00fcstung"))
            .append((game.storage.get('settings','heroid_ui') == 'tab') ? $('<option />').val('#inv_heroics').text("Heldentaten") : null)
            .append($('<option />').val('#settings_container').text("Zeitfluss & Verhalten"))
            .append($('<option />').val('#game_info').text("Spieldetails"))
            .append(data.players ? $('<option />').val('#mp_container').text(data.players.multiplayer ? ((data.players.messages ? '[!!!] ' : '') + "Spieler & NPCs") : "NPCs") : false)
            .change(function() {
                $('[data-toggle="' + $(this).val() + '"]').click();
            })
            .selectric();

        var auto_tab = $('<ul />').addClass('tabline hide-mobile').appendTo(action_box)
            .append($('<li>').attr('data-toggle', '#inv_container').text(game.storage.get('settings','heroid_ui') == 'tab' ? "Gegenst\u00e4nde" : "Gegenst\u00e4nde & Heldentaten"))
            .append($('<li>').attr('data-toggle', '#rpg_container').text("Kampfausr\u00fcstung"))
            .append((game.storage.get('settings','heroid_ui') == 'tab') ? $('<li>').attr('data-toggle', '#inv_heroics').text("Heldentaten") : null)
            .append($('<li>').attr('data-toggle', '#settings_container').text("Zeitfluss & Verhalten"))
            .append($('<li>').attr('data-toggle', '#game_info').text("Spieldetails"))
            .append(data.players ? $('<li>').attr('data-toggle', '#mp_container').text(data.players.multiplayer ? "Spieler & NPCs" : "NPCs").prepend(data.players.multiplaye && data.players.messages ? $('<img />').attr('src','media/icons/new.png') : false) : false)
            .find('>li').click(function() {
                var t = $($(this).data('toggle'));
                auto_select.val($(this).data('toggle')).selectric();
                core.session('main.tabs.open', $(this).data('toggle'));
                $(this).addClass('active').siblings().removeClass('active');
                action_box.children('div:not(.control)').hide();
                t.show();
            }).first();

        if (data.inventory) {
            core.parts.inventory(data.inventory, $('<div />').attr('id', 'inv_container').addClass('row').appendTo(action_box), game.storage.get('settings', 'heroid_ui') != 'tab');
            if (game.storage.get('settings', 'heroid_ui') == 'tab')
                core.parts.heroics(data.inventory, $('<div />').attr('id', 'inv_heroics').addClass('row').appendTo(action_box));
        }

        if (data.rpg && data.inventory)
            core.parts.rpg(data.rpg, data.inventory.player, $('<div />').attr('id', 'rpg_container').addClass('row').appendTo(action_box));

        if (data.settings)
            core.parts.settings(data.settings, $('<div />').attr('id', 'settings_container').addClass('row').appendTo(action_box));

        if (data.game)
            core.parts.info(data.game, $('<div />').attr('id', 'game_info').addClass('row').appendTo(action_box));

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
    var ui_skip_ahead = function() {
        var in_w, in_d, in_h, in_m;

        core.popup.spawn(300, 'auto')
            .append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded').text('Weeks (W)'))
                .append($('<div />').addClass('cell rw-6 padded').append(in_w = $('<input />').addClass('form_input').attr('type','text').val('0')))
        ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded').text('Days (D)'))
                .append($('<div />').addClass('cell rw-6 padded').append(in_d = $('<input />').addClass('form_input').attr('type','text').val('0')))
        ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded').text('Hours (H)'))
                .append($('<div />').addClass('cell rw-6 padded').append(in_h = $('<input />').addClass('form_input').attr('type','text').val('0')))
        ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded').text('Minutes (M)'))
                .append($('<div />').addClass('cell rw-6 padded').append(in_m = $('<input />').addClass('form_input').attr('type','text').val('5')))
                .append(NF.row()
                    .append($('<div />').addClass('cell rw-6 padded').append($('<div />').addClass('btn').text('OK').click(function() {
                        var v_w = parseInt(in_w.val()),v_d = parseInt(in_d.val()),v_h = parseInt(in_h.val()),v_m = parseInt(in_m.val());

                        if (!isFinite(v_m) || !isFinite(v_h) || !isFinite(v_d) || !isFinite(v_w)) {
                            alert('Please enter numeric values only!');
                            return;
                        }

                        if (v_m < 0 || v_h < 0 || v_d < 0 || v_w < 0) {
                            alert('Negative values are not allowed!');
                            return;
                        }

                        if (v_m % 5) {
                            alert('Minutes must be a multiple of 5!');
                            return;
                        }

                        if (v_m < 0 || v_h < 0 || v_d < 0 || v_w < 0) {
                            alert('Negative values are not allowed!');
                            return;
                        }

                        v_h += Math.floor(v_m/60); v_m %= 60;
                        v_d += Math.floor(v_h/24); v_h %= 24;
                        v_w += Math.floor(v_d/7); v_d %= 7;
                        var ticks = v_m/5 + v_h * 12 + v_d * 288 + v_w * 2016;

                        if (confirm('Skip ahead ' + v_w + ' Weeks, ' + v_d + ' Days, ' + v_h + ' Hours and ' + v_m + ' Minutes (' + ticks + ' Ticks) ?'))
                            core.parts.admin.execute('admin/japi/gamepanel/skip', {'ticks': ticks});

                    }))).append($('<div />').addClass('cell rw-6 padded').append($('<div />').addClass('btn').text('Manual').click(function() {

                        var p = parseInt(prompt('Enter number of ticks', '1'));

                        if (!isFinite(p) || p < 0) {
                            alert('Invalid value!');
                            return;
                        }

                        in_w.val(Math.floor(p/2016)); p %= 2016;
                        in_d.val(Math.floor(p/288)); p %= 288;
                        in_h.val(Math.floor(p/12)); p %= 12;
                        in_m.val(p * 5);
                    }))))
        );
    };

    var ui_show_items = function(target,data) {

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
    };

    var ui_custom_battle = function(target, data) {
        core.parts.admin.controls(target);

        var maker = function() {
            var select;

            var div = $('<div />').attr('data-obj','maker').addClass('flatbox').append(NF.row()
                    .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append(select = $('<select />')))
                    .append($('<div />').addClass('cell rw-2 rw-sm-5 padded').append($('<input />').addClass('form_input').attr({placeholder: '#', name: 'acb_num', type: 'number'})))
                    .append($('<div />').addClass('cell rw-2 rw-sm-5 padded').append($('<input />').addClass('form_input').attr({placeholder: 'Distance', name: 'acb_dist', type: 'number'})))
                    .append($('<div />').addClass('cell rw-2 padded center').append($('<i />').addClass('fa fa-trash pointer').click(function() {
                        $(this).parents('[data-obj=maker]').remove();
                    })))
            );


            $.each(data.zombies, function(k,v) {
                select.append($('<option />').attr('value', v.id).text(v.name));
            });

            select.selectric();
            return NF.row().append($('<div />').addClass('cell rw-12 padded').append(div));
        };

        var popup = core.popup.spawn(550, 'auto');
        var t;

        popup.append(NF.row()
            .append(t = $('<div />').addClass('cell rw-12 padded'))
            .append($('<div />').addClass('cell rw-6 padded').append($('<div />').addClass('btn').text('OK').click(function() {
                    var cfg = [];
                    popup.find('[data-obj=maker]').each(function() {
                        var type = $(this).find('select').val();
                        var count = parseInt($(this).find('[name=acb_num]').val());
                        var dist = parseInt($(this).find('[name=acb_dist]').val());

                        if (type && isFinite(count * dist) && (count * dist > 0))
                            cfg.push({type: type, count: count, distance: dist});
                    });

                    core.parts.admin.execute('admin/japi/gamepanel/custom_battle', {data: cfg});
                    popup.trigger('unpop');

            })))
            .append($('<div />').addClass('cell rw-6 padded').append($('<div />').addClass('btn').append($('<i />').addClass('fa fa-plus-circle')).click(function() {
                    t.append(maker());
                }).click()))
        );
    };

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
        var ret = NF.row().appendTo(target);

        $('<div />').addClass('btn small').text('Create item...').click(function() {
            core.parts.admin.loader(target,'admin/japi/gamepanel/list_items', ui_show_items)
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Unveil Map').click(function() {
            core.parts.admin.execute('admin/japi/gamepanel/unveil_map', {});
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Heal Player').click(function() {
            core.parts.admin.execute('admin/japi/gamepanel/regenerate', {});
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Force Battle').click(function() {
            core.parts.admin.execute('admin/japi/gamepanel/force_battle', {});
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Custom Battle...').click(function() {
            core.parts.admin.loader(target,'admin/japi/gamepanel/list_zombies', ui_custom_battle)
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Siege...').click(function() {
            var n = parseInt(prompt('Number of zombies? (+/-)', '0'));
            if (!isFinite(n) || !n) return;
            core.parts.admin.execute('admin/japi/gamepanel/siege', {'z': n});
        }).appendTo(ret);

        $('<div />').addClass('btn small').text('Skip ahead...').click(ui_skip_ahead).appendTo(ret);

        $('<div />').addClass('btn small').text('Purge log').click(function() {
            core.parts.admin.execute('admin/japi/gamepanel/purge_log', {});
        }).appendTo(ret);
    };
})();
(function() {

    core.parts.info = function(data, target) {
        var details = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-6 ro-3 rw-lg-8 ro-lg-2 rw-md-12 ro-md-0 padded').appendTo(target));

        details.append($('<h3 />').text("Aktuelles Spiel"))
            .append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Spielmodus"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.mode))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Beruf"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.job))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Level"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.level))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Spieldauer"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.gametime))
            ).append(data.gametime == data.lifetime ? false : NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Lebensdauer"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.lifetime))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Punkte"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.points))
            ).append(NF.row()
                .append($('<div />').addClass('cell rw-6 padded right b').text("Get\u00f6tete Zombies"))
                .append($('<div />').addClass('cell rw-6 padded left').text(data.kills))
            );
    };
})();(function() {
    var cancel = function() {
        var inventories = $('.inventory_location, .inventory_self');
        inventories.find('li[data-id]').removeClass('disabled marked-target').data('click-override', false);
        inventories.find('li.control').removeClass('disabled');

        game.render.html.hint(false);
    };

    var render_box = function(data, target, rucksack, remote) {
        $(target).empty();

        if (!remote)
            $(target).append($('<li />').addClass('control').append($('<i />').addClass('fa fa-caret-square-o-' + (rucksack ? 'right' : 'left'))).attr('title', rucksack ? "Alle ablegen" : "Alle mitnehmen").click(function() {
                var cache = [];
                $.each(data, function(k,v) {
                    cache = cache.concat($.objToArray(v.set, true));
                });
                core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: cache, player: $('.inventory_player[data-pid-selected=1]').data('pid')});
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
                        else if (!game.touch() || force) core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: [v.uin], player: $('.inventory_player[data-pid-selected=1]').data('pid')});
                    });
                else
                    container.click(function() {
                        var o;
                        if (o = $(this).data('click-override'))
                            return o(this);

                        cancel();
                        if ($('[data-pid-selected="1"]').attr('data-allow-access') != "true")
                            $('.inventory_self').click();
                        var inventories = $('.inventory_location, .inventory_player[data-allow-access="true"]');
                        inventories.find('li.control').addClass('disabled');
                        var items = inventories.find('li[data-id]');
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
                                    core.command('act/inventory',{action: 'fill', items: [v.uin, $(o).data('id')], player: $('[data-pid-selected="1"]').attr('data-pid')});
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
                    case 'equipped':case 'primary':
                        container.addClass(v);
                        break;
                    case 'armor':
                        notes.push("Dies ist eine R\u00fcstung. Sie wendet w\u00e4hrend eines Kampfes Schaden von dir ab.");
                        break;
                    case 'weapon':
                        notes.push("Dies ist eine Waffe. Hast du sie ausger\u00fcstet, wird sie im Kampf gegen Zombies automatisch eingesetzt.");
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
                        subhead = subhead.add($('<i />').addClass('info center').text(v.label));
                    if (v.count && !v.fill)  subhead = subhead.add($('<i />').addClass('info center').text(v.count + (v.capacity ? (' / ' + v.capacity + ' ') : ' ' ) + v.stack));
                    if (v.weight) subhead = subhead.add($('<i />').addClass('info center').text("Gewicht" + ': ' + v.weight));

                    if (subhead.size())
                        content.append(subhead).append('<span class="separator" />');

                    if (v.custom_label) {
                        content
                            .append(
                                NF.row().append(
                                    $('<div />').addClass('cell rw-12').append(
                                        $('<input>').val(v.label ? v.label : '').attr('type','text').attr('placeholder', "Beschriften ...").addClass('form_input').attr('autocomplete','off').on('keydown', function(e) {
                                            if (e.keyCode == 13) {
                                                e.preventDefault();
                                                api.hide();
                                                core.command('act/inventory',{action: 'label', items: [v.uin], text: $(this).val(), player: $('[data-pid-selected="1"]').attr('data-pid')});
                                            }
                                        }))
                                )
                            )
                            .append(
                                NF.row().append(NF.cell(true, 12).append($('<div />').addClass('note').text("Du kannst diesen Gegenstand beliebig beschriften. Best\u00e4tige deine Beschriftung mit der Eingabetaste.")))
                            )
                            .append('<span class="separator" />');
                    }

                    if (v.rpg) {
                        content
                            .append(NF.row()
                                .append($('<div />').addClass('cell rw-3 center padded').append($('<div />').addClass('rpg stat ini').addClass(v.rpg.ini > 0 ? 'plus' : (v.rpg.ini == 0 ? 'null' : 'minus')).text(v.rpg.ini)))
                                .append($('<div />').addClass('cell rw-3 center padded').append($('<div />').addClass('rpg stat atk').addClass(v.rpg.atk > 0 ? 'plus' : (v.rpg.atk == 0 ? 'null' : 'minus')).text(v.rpg.atk)))
                                .append($('<div />').addClass('cell rw-3 center padded').append($('<div />').addClass('rpg stat def').addClass(v.rpg.def > 0 ? 'plus' : (v.rpg.def == 0 ? 'null' : 'minus')).text(v.rpg.def)))
                                .append($('<div />').addClass('cell rw-3 center padded').append($('<div />').addClass('rpg stat acc').addClass(v.rpg.acc > 0 ? 'plus' : (v.rpg.acc == 0 ? 'null' : 'minus')).text(v.rpg.acc)))
                            ).append('<span class="separator" />');
                    }

                    if (v.is_chem) {
                        content.append(
                            $('<div />').addClass('note').text("Du kannst diese Chemikalie mit beliebigen anderen Gegenst\u00e4nden kombinieren. Welchen Effekt das hat... das wirst du selbst herausfinden m\u00fcssen.")
                        ).append(
                            $('<div />').addClass('btn').text("Experimentieren ...").click(function() {
                                container.qtip().hide();

                                cancel();
                                var inventories = $('.inventory_location, .inventory_self');
                                inventories.find('li.control').addClass('disabled');
                                var items = inventories.find('li[data-id]');
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
                                            core.command('act/inventory',{action: 'mix', items: [v.uin, $(o).data('id')], player: $('[data-pid-selected="1"]').attr('data-pid')});
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
                        ).append(
                            NF.row().append(NF.cell(true, 12).append($('<div />').addClass('note').text("Klicke einen leeren Slot an, um Wasser aus einer anderen Quelle hinzuzugeben. Klicke einen gef\u00fcllten Slot an, um Wasser auszusch\u00fctten. Schwarz gef\u00e4rbte Slots k\u00f6nnen nicht ausgeleert werden.")))
                        );
                        for (i = 0; i < v.count; i++)
                            fillbox.append($('<div />').addClass('fillbox fillbox-filled ' + (v.fill.fixed ? 'fillbox-fixed' : '')).click(function() {
                                if (!v.fill.fixed)
                                    core.command('act/inventory',{action: 'spill', items: [v.uin], player: $('[data-pid-selected="1"]').attr('data-pid')});
                            }));
                        for (i = v.count; i < v.fill.capacity; i++)
                            fillbox.append($('<div />').addClass('fillbox pointer').click(function() {
                                container.qtip().hide();

                                cancel();
                                if ($('[data-pid-selected="1"]').attr('data-allow-access') != "true")
                                    $('.inventory_self').click();
                                var items = $('.inventory_location, .inventory_player[data-allow-access="true"]').find('li[data-id]');
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
                                            core.command('act/inventory',{action: $(o).is('[data-is-fillable]') ? 'defill' : 'fill', items: [v.uin, $(o).data('id')], player: $('[data-pid-selected="1"]').attr('data-pid')});
                                            cancel();
                                        });

                                    } else $(this).addClass('disabled');
                                })
                            }
                        ));

                        if (v.count > 1 && !v.fill.fixed)
                            fillbox.append($('<div />').addClass('fillbox pointer').css('vertical-align', 'top').append(NF.fa('arrow-down')).click(function() {
                                core.command('act/inventory',{action: 'spill', items: [v.uin], all: true, player: $('[data-pid-selected="1"]').attr('data-pid')});
                            }));

                        content.append('<span class="separator" />');
                    }

                    if (v.description)
                        content.append(v.description);
                    else notes.push("\u00dcber diesen Gegenstand stehen nur wenige Informationen zur Verf\u00fcgung ...");

                    if (v.armor) {
                        content.append('<span class="separator" />');

                        var bar_col = '';
                        if (v.armor.hp >= 1) bar_col = 'blue';
                        else if (v.armor.hp >= 0.7) bar_col = 'green';
                        else if (v.armor.hp >= 0.4) bar_col = 'yellow';
                        else if (v.armor.hp >= 0.2) bar_col = 'orange';
                        else bar_col = 'red';

                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text("Typ"))
                            .append(NF.cell(true, 6, 0, 'left').text(v.armor.type))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text("Zustand"))
                            .append(NF.cell(true, 6, 0, 'left').text(v.armor.condition))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 6, 'left').append(
                                $('<div />').addClass('genbar ' + bar_col).append($('<div />').css('width', (v.armor.hp * 100) + '%'))
                            ))
                            .appendTo(content);
                    }

                    if (v.weapon) {
                        content.append('<span class="separator" />');
                        NF.row()
                            .append($('<div />').addClass('cell rw-6 padded b right').text("Schaden"))
                            .append($('<div />').addClass('cell rw-6 padded left').text((v.weapon.damage[0] == v.weapon.damage[1] ? v.weapon.damage[0] : (v.weapon.damage[0] + ' - ' + v.weapon.damage[1]))))
                            .appendTo(content);
                        NF.row()
                            .append($('<div />').addClass('cell rw-6 padded b right').text("Genauigkeit"))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.accuracy === true ? "Distanzabh\u00e4ngig" : (v.weapon.accuracy + '%')))
                            .appendTo(content);
                        if (v.weapon.ammo) {
                            var ammo_cont = $('<div />');
                            $.each(v.weapon.ammo, function(ak,av) {
                                ammo_cont.append($('<img />').attr('src', 'media/icons/' + av + '.gif'));
                            });
                            NF.row()
                                .append($('<div />').addClass('cell rw-6 padded b right').text("Munition"))
                                .append($('<div />').addClass('cell rw-6 padded left').append(ammo_cont))
                                .appendTo(content);
                        }
                        if (v.weapon.shots !== false)
                            NF.row()
                                .append($('<div />').addClass('cell rw-6 padded b right').text("F\u00fcllstand"))
                                .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.shots == 0 ? "Leer!" : game.i18n(":num Schuss",{':num': v.weapon.shots})))
                                .appendTo(content);
                        if (v.weapon.energy)
                            NF.row()
                                .append($('<div />').addClass('cell rw-6 padded b right').text("Energie"))
                                .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.energy).append($('<img />').attr('src', 'media/icons/status_energy.gif')))
                                .appendTo(content);
                        NF.row()
                            .append($('<div />').addClass('cell rw-6 padded b right').text("Zerst\u00f6rbar"))
                            .append($('<div />').addClass('cell rw-6 padded left').text(v.weapon.breakable ? "Ja" : "Nein"))
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
                        content.append(NF.row()
                            .append($('<div />').addClass('cell rw-2 right').append($('<img />').attr('src','media/icons/deco_' + (v.deco > 0 ? 'positive' : 'negative') + '.gif')))
                            .append($('<div />').addClass('cell rw-3 center').addClass(v.deco > 0 ? 'text-green' : 'text-red').text(v.deco > 0 ? ('+' + v.deco) : v.deco))
                            .append($('<div />').addClass('cell rw-7 b').addClass(v.deco > 0 ? 'text-green' : 'text-red').text(v.deco > 0 ? "Dekorativer Gegenstand" : "Absto\u00dfender Gegenstand"))
                        );
                    }

                    if (v.ammobelt) {
                        var ammo_slots;
                        content.append('<span class="separator" />').append(
                            ammo_slots = $('<div />').addClass('center')
                        ).append($('<div />').addClass('note').text("Klicke Munition an, um sie abzulegen."));
                        $.each(v.ammobelt, function(k,vin) {
                            ammo_slots.append($('<div />').addClass('itembox pointer').append($('<img />').attr('src','media/icons/' + vin.icon + '.gif')).append($('<span />').text(vin.count)).click(function() {
                                var ok = false;
                                var num;
                                while (!ok) {
                                    num = prompt("Wie viel Munition m\u00f6chtest du ablegen?" + ' (1 - ' + (vin.count) + ')', vin.count);
                                    if (num == null) break;
                                    num = parseInt(num);
                                    if (isFinite(num) && num >= 1 && num <= vin.count) ok = true;
                                }
                                if (ok) core.command('act/inventory',{action: 'belt', items: [v.uin], count: num, addr: vin.addr, player: $('[data-pid-selected="1"]').attr('data-pid')});
                            }))
                        });

                    }

                    if (v.static > 1 && !v.is_water) {
                        content.append('<span class="separator" />').append(core.snippets.button(rucksack ? "Alle ablegen" : "Alle mitnehmen", function() {
                            core.command('act/inventory',{action: rucksack ? 'drop' : 'take', items: $.objToArray(v.set, true), player: $('.inventory_player[data-pid-selected=1]').data('pid')});
                        }));
                    }

                    var actions = []; var targets = {};
                    $.each(v.actions, function(k,v) {
                        btn = core.snippets.button(v, false, 'nested');
                        targets[btn.attr('data-target')] = true;
                        actions.push(btn)
                    });
                    targets = $.objToArray(targets);

                    if (actions.length) {
                        content.append('<span class="separator" />');

                        var auto_tab = $('<ul />').addClass('tabline hide-mobile').appendTo(content);

                        content.append($('<div />').addClass('btn').hide());
                        $.each(actions, function(k,v) {
                            content.append(v);
                        });

                        $.each(targets, function(kt, tar) {
                                auto_tab.append($('<li>').attr('data-toggle-target', tar).text(tar))
                        });
                        auto_tab.find('>li').click(function() {
                            var tar = $(this).attr('data-toggle-target');
                            $.each(actions, function(ka, act) {
                                act.toggle(act.attr('data-target') == tar);
                            });
                            $(this).addClass('active').siblings().removeClass('active');
                        }).first().click();

                        if (targets.length <= 1) auto_tab.hide();
                    }

                    if (v.is_pillbox) {
                        var pillrow;
                        content.append('<span class="separator" />').append(
                            pillrow = NF.row()
                        );

                        $('<div />').addClass('cell rw-6 padded').append(
                            $('<div />').addClass('btn').text("Auff\u00fcllen").click(function() {
                                core.command('act/inventory',{action: 'pilltake', items: [v.uin], player: $('[data-pid-selected="1"]').attr('data-pid')});
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
                                    if (isFinite(num) && num >= 1 && num <= v.count - 1) ok = true;
                                }
                                if (ok) core.command('act/inventory',{action: 'pilldrop', items: [v.uin], count: num, player: $('[data-pid-selected="1"]').attr('data-pid')});
                            })
                        ).appendTo(pillrow);
                    }

                    if (game.touch()) {
                        $('<div />').addClass('btn green').text("Aufnehmen \/ Ablegen").click(function() {
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
                NF.row().append(
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

        render_block(data.player, iv_a, "Dein Rucksack", true);

        iv_a.append(
            $('<div />')
                .addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('weightbar').append($('<div />').css('width', (100*Math.max(0,Math.min(1,data.weight[0]/data.weight[1])) ) + '%'))))
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

            if (include_heroics) {
                iv_c.append($('<h3 />').text("Heldentaten"));
                $.each(data.heroics, function(k,v) {
                    iv_c.append(
                        $('<div />').addClass(game.touch() ? 'cell rw-12 padded' : 'cell rw-6 rw-sm-12 padded').append(core.snippets.button(v, function() {
                            return confirm("Bist du sicher, dass du diese Heldentat ausf\u00fchren m\u00f6chtest?")
                        }, 'tooltip'))
                    )
                });
            }


            if (core.last.players) {
                $.each(core.last.players.others, function(k,v) {
                    if (!(v.allow === true || v.allow[1])) return;
                    if (!v.inventory) return;

                    var remote_inv;
                    iv_a.after(remote_inv = $('<div />').addClass('row inventory flatbox inventory_player'));

                    render_block(v.inventory.player, remote_inv, game.i18n("Rucksack von :name", {':name': v.name}), true, !(v.allow === true || v.allow[3]));

                    remote_inv.append(NF.row().append($('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('weightbar').append($('<div />').css('width', (100*v.inventory.weight[0]/v.inventory.weight[1]) + '%'))))
                        .attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                            render: function(event,api) {
                                var content = $(this).find('.qtip-content').empty();

                                content
                                    .append($('<b />').addClass('header').text("Gewicht"))
                                    .append($('<div />').addClass('center').text("Aktueller Wert" + ': ' + Math.round10(v.inventory.weight[0],-2) + ' / ' + Math.round10(v.inventory.weight[1], -2)))
                            }
                        }))
                    );
                    remote_inv.attr({
                        'data-pid': v.id,
                        'data-pid-selected': 0,
                        'data-allow-access': (v.allow === true || v.allow[5])
                    }).click(iv_switch).children('.row').hide();
                });

                iv_a.attr({
                    'data-pid': 0,
                    'data-pid-selected': 0,
                    'data-allow-access': true
                }).click(iv_switch).children('.row').hide();

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

        iv_c.append($('<h3 />').text("Heldentaten"));

        var has = false;
        $.each(data.heroics, function(k,v) {
            has = true;
            iv_c.append(
                $('<div />').addClass(game.touch() ? 'cell rw-12 padded' : 'cell rw-6 rw-sm-12 padded').append(core.snippets.button(v, function() {
                    return confirm("Bist du sicher, dass du diese Heldentat ausf\u00fchren m\u00f6chtest?")
                }, 'tooltip'))
            )
        });

        if (!has)
            iv_c.append($('<div />').addClass('note').text("Du kannst derzeit keine Heldentaten einsetzen."))
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
                        .append($('<b />').addClass('header').text("Beladung"))
                        .append($('<div />').text("Du f\u00e4hrst ein Wohnmobil, keinen LKW - wenn du mehr einl\u00e4dst als der Motor ziehen kann, wirst du nicht vom Fleck kommen."))
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
                                .append($('<div />').text("Dein Wohnmobil ist schon etwas betagt... und war auch nie f\u00fcr eine wilde Flucht vor Zombies auf schlecht befestigten Stra\u00dfen vorgesehen. Fr\u00fcher oder sp\u00e4ter wirst du anhalten und Reparaturen vornehmen m\u00fcssen."))
                                .append('<span class="separator" />')
                                .append($('<div />').addClass('center').text("Zustand" + ': ' + v.count + ' / ' + v.max));

                            if (v.max == v.count) content.append($('<div />').text("Hier muss im Moment nichts repariert werden."));
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
                            } else content.append($('<div />').text("W\u00e4hrend der Fahrt kannst du keine Reparaturen vornehmen!"))
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
                            .append($('<b />').addClass('header').text("Amaturenbrett"))
                            .append($('<div />').text("Hier siehst du, wie weit du schon gekommen bist. Um Punkte zu sammeln musst du so weit wie m\u00f6glich fahren."))
                            .append('<span class="separator" />')
                            .append($('<div />').text(game.i18n("Du bist bereits :distance km gefahren und hast :breaks St\u00e4dte aufgesucht.", {':distance': Math.round(data.distance), ':breaks': data.stops})));
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
            data_space.append($('<div />').addClass('btn btn-zv btn-zv-skinned-hero').text("Losfahren").click(function() {
                if ((data.stops > 0 || confirm("Sobald du losgefahren bist, k\u00f6nnen keine weiteren Spieler deiner Partie beitreten. Fortfahren?")) && confirm("Denk daran: Du kannst nicht wieder hierher zur\u00fcckkehren. Wenn du jetzt losf\u00e4hrst verlierst du alle Gegenst\u00e4nde, die sich au\u00dferhalb des Wohnwagens befinden. Wenn du andere Spieler zur\u00fcckl\u00e4sst, werden sie einsam in der Wildniss sterben. Wirklich losfahren?"))
                    core.command('location/caravan', {'do': 'go'});
            }));
        } else {
            data_space.append($('<div />').addClass('btn btn-zv btn-zv-skinned-hero').text("N\u00e4chste Stadt suchen").click(function() {
                if (confirm("M\u00f6chtest du wirklich anhalten?"))
                    core.command('location/caravan', {'do': 'stop'});
            }));
            data_space.append($('<div />').addClass('btn btn-zv btn-zv-skinned-hero').text("Zwischenstop einlegen").click(function() {
                if (confirm("M\u00f6chtest du wirklich anhalten?"))
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
                        .append($('<span />').text("Ein h\u00fcbsch eingerichtetes Versteck reduziert die Chance, dass pl\u00f6tzlich ein RTL-Messie-Kamerateam (oder Tine Wittler) vor deiner T\u00fcr steht. So f\u00fchlst du dich direkt viel wohler."))
                        .append(NF.row()
                            .append($('<div />').addClass('cell rw-9 padded right').text("Grundwert"))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[0] < 0 ? 'negative' : (data.deco[0] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[0]))
                            .append($('<div />').addClass('cell rw-9 padded right').text("Zustand des Verstecks"))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[1] < 0 ? 'negative' : (data.deco[1] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[1]))
                            .append($('<div />').addClass('cell rw-9 padded right').text("Verbesserungen"))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[2] < 0 ? 'negative' : (data.deco[2] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[2]))
                            .append($('<div />').addClass('cell rw-9 padded right').text("Gegenst\u00e4nde"))
                            .append($('<div />').addClass('cell rw-1 padded center').append($('<img />').attr('src', 'media/icons/deco_' + (data.deco[3] < 0 ? 'negative' : (data.deco[3] > 0 ? 'positive' : 'neutral')) + '.gif')))
                            .append($('<div />').addClass('cell rw-2 padded').text(data.deco[3]))
                        );
                }
            }));

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
        ).qtip(game.render.html.qtip.ingame('bottom'));

        siege.attr('title','-').qtip(game.render.html.qtip.ingame('bottom',{
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

    var locationradar = function(data, target) {
        $(target).empty().append(
            $('<div />').addClass('cell rw-12 padded').append(

                $('<div />').addClass('widget radar').append(
                    $('<span />').text("Erkundungsrate")
                ).append(
                    $('<div />').addClass('discoverybar').append($('<div />').css('width', data + '%'))
                )
                    .attr('title', '-')
                    .qtip(game.render.html.qtip.ingame('top', {
                        render: function(ev,api) {
                            var content = $(this).find('.qtip-content').empty()
                                .append($('<span />').text(data >= 100 ? "Du hast diesen Ort vollst\u00e4ndig ausgekundschaftet - von hier aus wirst du keine neuen Ruinen entdecken k\u00f6nnen." : "Du bist momentan auf der Suche nach neuen Orten. Jedes mal, wenn der Ereigniscountdown abl\u00e4uft, hast du die Chance einen neuen Ort zu entdecken."))
                            if (data <= 100)
                                content
                                    .append($('<span />').addClass('separator'))
                                    .append($('<div />').addClass('center').text(game.i18n("Aktueller Wert: :num", {':num': Math.round(data) + '%'})))
                        }
                    }))
            )
        );
    };

    var garden = function(data, target) {
        var content;
        target.append($('<div />').addClass('cell rw-12 widget epic padded').append(content = NF.row().append($('<h3 />').text("Kleines Gew\u00e4chshaus"))));

        if (!data.planted)
            content.append($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Das Beet ist momentan leer.")));
        else {

            var bar_growth, bar_quality, bar_water, bar_fert;

            content
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text("Fortschritt")).append($('<br />')).append(bar_growth = $('<div />').addClass('gardenbar growth').append($('<div />').css('width', (data.harvest * 100) + '%'))))
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text("Qualit\u00e4t")).append($('<br />')).append(bar_quality = $('<div />').addClass('gardenbar quality').append($('<div />').css('width', (data.quality * 100) + '%'))))
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text("Wasser")).append($('<br />')).append(bar_water = $('<div />').addClass('gardenbar water').addClass(data.time_water ? '' : 'dry').append($('<div />').css('width', (data.water * 100) + '%'))))
                .append($('<div />').addClass('cell rw-3 padded').append($('<b />').text("D\u00fcnger")).append($('<br />')).append(bar_fert = $('<div />').addClass('gardenbar fertilizer').append($('<div />').css('width', (data.fertilizer * 100) + '%'))))
            ;

            var q;
            if          (data.quality >= 1.00)  q = "Excellent";
            else if     (data.quality >= 0.90)  q = "Ausgezeichnet";
            else if     (data.quality >= 0.80)  q = "Sehr gut";
            else if     (data.quality >= 0.65)  q = "Gut";
            else if     (data.quality >= 0.50)  q = "Durchschnittlich";
            else if     (data.quality >= 0.35)  q = "Verbesserungsw\u00fcrdig";
            else if     (data.quality >= 0.20)  q = "Schlecht";
            else if     (data.quality >= 0.10)  q = "Sehr schlecht";
            else                                q = "Unbrauchbar";

            bar_growth.attr('title', game.i18n("Deine Pflanzen sind in :time erntebereit!", {':time': '<b>' + data.time + '</b>'})).qtip(game.render.html.qtip.ingame('top'));
            bar_quality.attr('title', game.i18n("Die Qualit\u00e4t bestimmt die Anzahl der Fr\u00fcche, die du bei der Ernte erhalten wirst. Derzeit zeichnet sich folgende Qualit\u00e4t ab: :quality", {':quality': '<b>' + q + '</b>'})).qtip(game.render.html.qtip.ingame('top'));
            bar_water.attr('title', "Wenn der Wasservorrat deiner Pflanzen aufgebraucht ist, musst du neues nachf\u00fcllen. Warte damit nicht zu lange, andernfalls sinkt die Erntequalit\u00e4t." + ' ' + game.i18n(data.time_water ? "Du kannst in :time neues Wasser hinzuf\u00fcgen." : (data.time_water2 ? "Wenn du bis in :time kein neues Wasser hinzugef\u00fcgt hast, wird die Erntequalit\u00e4t abnehmen!" : ('<b>' + "Deine Pflanzen verdorren! F\u00fcge schnell neues Wasser hinzu!" + '</b>')), {':time': '<b>' + (data.time_water || data.time_water2) + '</b>'})).qtip(game.render.html.qtip.ingame('top'));
            bar_fert.attr('title', "Die St\u00e4rke deiner D\u00fcngung bestimmt die H\u00f6he der Effekte der geernteten Pflanzen. Wenn du nach Erreichen der maximalen D\u00fcngest\u00e4rke noch weiter d\u00fcngst, hat dies nur noch Einfluss auf die Art der Effekte, nicht jedoch deren H\u00f6he.").qtip(game.render.html.qtip.ingame('top'));
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
                    if (confirm("Bist du sicher, dass du diese Gegenst\u00e4nde einsetzen m\u00f6chtest, um die Pflanzen zu d\u00fcngen?"))
                        core.command('act/item', {action: v.action, item: v.target});
                }).attr('title', "Welchen Effekt deine geernteten Pflanzen haben h\u00e4ngt davon ab, womit du sie d\u00fcngst. Nahrung macht sie saftiger, Drogen geben ihnen einen heilenden Effekt und Chemikalien lassen sie aufputschend wirken." + '<br /><br />' + v.tooltip).qtip(game.render.html.qtip.ingame('bottom'));
            }
        });
    };

    var raven = function(data, target) {
        var content;
        target.append($('<div />').addClass('cell rw-12 widget epic padded').append(content = NF.row().append($('<h3 />').text("Raben-Bootcamp"))));

        if (data.time)
            content.append($('<div />').addClass('cell rw-12 padded').append($('<b />').text(game.i18n("Der Rabe muss sich noch :time ausruhen.",{':time': data.time}))));

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
                $('<h2 />').addClass('center').text("Zielgebiet ausw\u00e4hlen")
            ).append(
                NF.row().append($('<div />').addClass('cell rw-12 padded').append(
                    $('<div />').addClass('note').text(game.i18n("Hier kannst du ausw\u00e4hlen, wie weit der Rabe fliegen soll, um eine Ruine auszuw\u00e4hlen. F\u00fcr eine gr\u00f6\u00dfere Distanz musst du selbstverst\u00e4ndlich mehr Futter springen lassen. Der Rabe wird zuf\u00e4llig eine Ruine (die kein Aussichtspunkt und auch kein Versteck ist) in dem gew\u00e4hlten Bereich ausw\u00e4hlen und dort dreimal nach Gegenst\u00e4nden suchen. Gefundene Gegenst\u00e4nde wird er zu dir bringen, zumindest so lange er sie tragen kann. Falls er nichts findet oder die gefundenen Gegenst\u00e4nde ihn nicht auslasten, wird er Gegenst\u00e4nde vom Boden aufheben. Der Rabe kann nicht mehr als :capacity Gegenst\u00e4nde mit einem Gesamtgewicht von :size tragen!",{':size': data.size, ':capacity': data.capacity}))
                )).append($('<div />').addClass('cell rw-12 padded').append(
                    select = $('<select />').addClass('form_input').change(function() {
                        food.empty().attr('title', "Gew\u00f6hnliche Nahrung").append($('<img />').attr('src','media/icons/items/basefood/generic.gif')).append($('<span />').text(' x ' + Math.max(1,$(this).val() * 2))).qtip(game.render.html.qtip.ingame('bottom'));
                    })
                        .append($('<option />').attr('value',0).text(game.i18n("N\u00e4here Umgebung (Distanz bis :m2)",{':m1': 0, ':m2': 15})))
                        .append($('<option />').attr('value',1).text(game.i18n("Entfernte Regionen (Distanz zwischen :m1 und :m2)",{':m1': 16, ':m2': 50})))
                        .append($('<option />').attr('value',2).text(game.i18n("Arsch der Welt (Distanz \u00fcber :m1)",{':m1': 51, ':m2': 900})))
                ))
            ).append(
                NF.row()
                    .append($('<div />').addClass('cell rw-10 rw-sm-12 padded').text("F\u00fcr die gew\u00e4hlte Distanz ben\u00f6tigt der Rabe folgendes Futter:"))
                    .append(food = $('<div />').addClass('cell rw-2 rw-sm-12 padded'))
            ).append(
                NF.row()
                    .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<div />').addClass('btn').text("Abbrechen").click(function() {
                        popup.trigger('unpop');
                    })))
                    .append($('<div />').addClass('cell rw-6 rw-sm-12 padded').append($('<div />').addClass('btn').text("Raben aussenden").click(function() {
                        popup.trigger('unpop');
                        fetch_btn.trigger('click', [select.val()]);
                    })))
            );

            select.trigger('change').selectric();

        })));
    };

    var fence = function(data, target) {
        var content;
        target.append($('<div />').addClass('cell rw-12 widget epic padded').append(content = NF.row().append($('<h3 />').text("Laserzaun"))));

        content.append(NF.row()
                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded center').append($('<img />').attr('src', 'media/icons/defense.gif')).append($('<span />').addClass('margin-left').text(data.status ? '�^z' : '0')).attr('title', "Die durch den Laserzaun zus\u00e4tzlich generierte Verteidigung wird auf die Hausverteidigung addiert.").qtip(game.render.html.qtip.ingame('top')))
                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded center').append($('<img />').attr('src', 'media/icons/items/energy.gif')).append($('<span />').addClass('margin-left').text(data.energy)).attr('title', "Zeigt die Menge an Energie an, die deinem Versteck momentan zur Verf\u00fcgung steht. Geht die Energie zur Neige, solltest du mit dem Generator neue erzeugen.").qtip(game.render.html.qtip.ingame('top')))
                .append($('<div />').addClass('cell rw-4 rw-sm-12 padded center').append($('<img />').attr('src', 'media/icons/clock.gif')).append($('<span />').addClass('margin-left').text(data.time ? data.time : '---')).attr('title', "Dies ist die Zeit, die der Laserzaun mit deinem aktuellen Energievorrat noch laufen kann, bevor er wegen Energiemangel automatisch heruntergefahren wird.").qtip(game.render.html.qtip.ingame('top')))
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
                .append(esc = NF.row().attr('title', "W\u00e4hle die NPCs aus, die dich begleiten sollen.").qtip(game.render.html.qtip.ingame('top')));
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
                    if (player.npc) area_npc.push(player.name)
                    else area.push(player.name);
                }
            });

            if (area.length + area_npc.length) {
                var p = $('<p />').appendTo(desc).attr('title',"Hier siehst du Spieler, die sich momentan in deiner N\u00e4he befinden. Um mehr Details zu erfahren, klicke \"Spieler\u00fcbersicht\".").qtip(game.render.html.qtip.ingame('bottom'));
                $.each(area, function(k,name) {
                    p.append($('<span />').addClass('inline-player').text(name));
                });
                $.each(area_npc, function(k,name) {
                    p.append($('<span />').addClass('inline-npc green').text(name));
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
                $('<div />').addClass('cell rw-12 padded justify').append(core.snippets.button("Weihnachtsbaum schm\u00fccken", function() {
                    var inner;
                    var popup = core.popup.spawn(515, 772).append($('<div />').css({height: 768, width: 511, background: 'url("media/img/tree.jpg") center/cover no-repeat'}).append(inner = $('<div />').addClass('row')));

                    inner.append($('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append(
                        $('<div />').addClass('note')
                            .append($('<span />').text("Aktueller Dekorationswert:" + ' '))
                            .append($('<b />').text(data.xmasfair.deco))
                            .append($('<img />').attr('src', 'media/icons/deco_event.gif'))
                            .append($('<br />'))
                            .append($('<span />').text("Wenn du dir bei der Dekoration des Weihnachtsbaums M\u00fche gibst, wirst du beim Verlassen des Weihnachtsmarktes BrainCoins sowie ein paar n\u00fctzliche Geschenke erhalten."))
                    )));

                    $.each(data.xmasfair.tree, function(k, v) {
                        var req;

                        inner.append(
                            $('<div />').addClass('cell rw-6 rw-md-12').append(
                                $('<div />').addClass(v.current[0] >= v.current[1] ? 'hotbox disabled' : 'hotbox')
                                    .append($('<b />').text(v.name).css({'min-height': 54, display: 'block'}))

                                    .append(
                                        $('<div />').addClass('row')
                                            .append($('<div />').addClass('cell rw-6 padded').text("Info"))
                                            .append($('<div />').addClass('cell rw-6 padded').text("Erfordert"))
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
                data.radar.zombies > 0 ? $('<div />').addClass('note margin-bottom').text(data.hideout ? "Zombies blockieren den Weg. Besiege sie, um diesen Ort verlassen zu k\u00f6nnen." : "Zombies blockieren den Weg. Besiege sie oder versuche zu fliehen, um diesen Ort verlassen zu k\u00f6nnen.") : false
            ).append(core.snippets.button("Karte", function() {
                core.popup.map();
            })).addClass(data.lomap ? 'disabled' : '')
        );

        if (data.doorways) {
            actions.append(
                $('<div />').addClass('cell padded justify').addClass(data.lomap ? 'rw-4' : 'rw-2').append(
                    $('<div />').addClass('btn').append($('<i>').addClass('fa fa-sign-in')).append('&nbsp;').click(function() {
                        var esc_popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text("Ort wechseln"));

                        esc_popup.append(
                            NF.row().append(title = $('<div />').addClass('cell rw-12 padded').text("Du kannst von diesem Ort aus einen anderen Teil der Spielwelt betreten."))
                        );

                        if (data.xmasfair)
                            esc_popup.append(
                                $('<div />').addClass('row').append(title = $('<div />').addClass('cell rw-12 padded b text-red').text("Wenn du den Weihnachtsmarkt verl\u00e4sst, kannst du nicht mehr zur\u00fcckkehren. Falls du noch \u00fcber weitere Tickets verf\u00fcgst, werden diese dich zu anderen Weihnachtsm\u00e4rkten bringen. Event-Gegenst\u00e4nde werden bei der Reise aus deinem Inventar entfernt. "))
                            );

                        var destination = $('<select />');
                        $.each(data.doorways, function(id, meta) {
                            $('<option />').attr('value', id).text(meta.name == meta.location ? meta.name : (meta.name + ' (' + meta.location + ')')).appendTo(destination);
                        });

                        esc_popup.append(
                            NF.row().append($('<div />').addClass('cell rw-12 padded').append($('<b />').text("Wo soll's denn hingehen?")))
                        ).append(
                            NF.row().append($('<div />').addClass('cell rw-12 padded').append(destination))
                        );
                        destination.selectric();

                        if (core.last.players) {

                            var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                            if (core.last.players.others)
                                $.each(core.last.players.others, function(id, player) {
                                    if (player.allow === true || player.allow[6])
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
                        }

                        esc_popup.append(NF.row()
                                .append($('<div />').addClass('cell rw-8 padded').append(
                                    $('<div />').addClass('btn').text("Los gehts!").addClass(data.radar.zombies > 0 ? 'disabled' : '').click(function() {

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
                                    $('<div />').addClass('btn').text("Abbrechen").click(function() {
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
})();(function() {
    var renderers = {};

    renderers[0] =
        function(data) {
            return data.title
                ? $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<div />').addClass('sub').text(data.body))
                : $('<div />').append($('<div />').text(data.body))

        };

    renderers[6] =
        function(data) {
            return $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<pre />').addClass('sub').text(data.body));
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

    renderers[5] =
        function(data) {
            var header;

            var title = $('<div />');

            title.append($('<span />').text(data.msg)).data('expandable', true).append(
                sub = $('<div />').addClass('sub')
            );

            var videobtn = $('<div />').addClass('btn btn-icon')
                .append($('<span />').addClass('btn-icon-inner').append($('<i />').addClass('fa fa-video-camera')))
                .append($('<span />').text("Kampf ansehen"))
                .click(function(e) {
                    e.stopPropagation();
                    var popup = core.popup.spawn(644);

                    var fav = (data.gallery || videobtn.data('nogallery')) ? $('<div />').addClass('b').text("Dieses Video befindet sich bereits in deiner Gallerie.") : $('<div />').addClass('btn').text("In meine Kampfgallerie aufnehmen")
                        .click(function() {
                            var label = prompt("Bitte gib deinem Kampf einen Titel, unter dem er in deiner Gallerie erscheinen soll.", game.i18n("Kampf #:id", {':id': data.bid}))

                            if (label) {
                                fav.addClass('disabled');

                                core.command('player/favbattle', {v: data.bid, l: label}, true, function(data) {
                                    if (data.success) {
                                        game.render.html.notify('success', "Deine Videogallerie wurde aktualisiert!");
                                        fav.replaceWith($('<div />').addClass('b').text("Dieses Video befindet sich bereits in deiner Gallerie."));
                                        videobtn.data('nogallery', true);
                                    } else {
                                        game.render.html.notify('error', "Das Video konnte nicht in deine Gallerie kopiert werden ...");
                                        fav.removeClass('disabled');
                                    }
                                });
                            }
                        });

                    popup
                        .append($('<iframe>').attr({src: 'embed/battle?v=' + data.bid, sandbox: 'allow-scripts allow-same-origin', seamless: 'seamless', height: 400, width: 640}))
                        .append($('<br />'))
                        .append(NF.row()
                            .append($('<div />').addClass('cell rw-12 padded').append(
                                $('<div />').addClass('note')
                                    .text("Hast du einen besonders beeindruckenden Kampf erlebt, kannst du ihn in deine Kampfgallerie kopieren. Von dort aus kannst du ihn jederzeit auch nach Beendigung des Spiels ansehen, deinen Freunden pr\u00e4sentieren und sogar in andere Webseiten einbinden.")
                                    .append(fav)
                            ))
                        )
                });

            var current_row;
            sub
                .append(NF.row().append(NF.cell(true, 12).text(data.bdy)))
                .append(current_row = NF.row());

            current_row.append($('<div />').addClass('cell rw-4 rw-md-6 rw-sm-12 padded').append(
                $('<div />').addClass('note').text("Keine Lust auf langweilige Kampfstatistiken? Dann schau dir doch einfach ein Video des Kampfes an!").append(videobtn)
            ));

            $.each(data.sum, function(k, grp) {
                $.each(grp, function(ki, line) {
                    current_row.append($('<div />').addClass('cell rw-4 rw-md-6 rw-sm-12 padded').append($('<div />').addClass('flatbox').append(entry = NF.row())));

                    var injuries, items;

                    entry.css('opacity', line.count <= line.death ? 0.75 : 1)
                        .append(NF.cell(false, 12, 0, 'center').text(line.unique && line.count == 1 ? line.name : (line.count + ' ' + line.name)))
                        .append(NF.cell(false, 6, 0, 'center')
                            .append(line.death > 0 ? NF.icon('media/icons/death.gif', line.death) : null)
                            .append(NF.icon('media/icons/damage.gif', Math.round10(Number(line.dmg_taken), -1)))
                            .append(NF.icon('media/icons/status_energy.gif', Math.round10(Number(line.energy), -1)))
                        ).append(injuries = NF.cell(false, 6, 0, 'center')).append(items = NF.cell(false, 12, 0, 'center'));

                    $.each(line.injuries, function(aicon, adata) {
                        injuries.append(NF.icon('media/icons/' + aicon + '.gif', '+')).attr('title', adata[1]);
                    });
                    $.each(line.used_ammo, function(aicon, acount) {
                        items.append(NF.icon('media/icons/' + aicon + '.gif', '-' + acount));
                    });
                    $.each(line.damaged_items, function(aicon, adata) {
                        items.append(NF.icon('media/icons/' + aicon + '.gif', '-' + adata[0])).attr('title', adata[1]);
                    });
                });

                sub.append(current_row = NF.row())
            });

            return title;
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
                case 8:
                    header = "Der Rabe hat :itemdef gebracht!";
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

    renderers[7] =
        function(data) {
            var header;
            switch (data['class']) {
                case 1:
                    header = data.self ? "Du hast :itemdef aufgehoben." : ":name hat :itemdef aufgehoben.";
                    break;
                case 2:
                    header = data.self ? "Du hast :itemdef abgelegt." : ":name hat :itemdef abgelegt.";
                    break;
                case 3:
                    header = data.self ? "Du hast :itemdef verwendet (:action)." : ":name hat :itemdef verwendet (:action).";
                    break;
            }


            var title = $('<div />');
            header = game.i18n(header, {':name': data.player, ':action': data.action});
            var pos = header.search(':itemdef');
            if (pos >= 0) {
                title.append($('<span />').text(header.slice(0,pos)));
                $.each(data.items, function(k, item) {
                    title.append(core.snippets.item(true,item.name,item.icon,data['class'] == 3 ? 0 : item.count,false,false));
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

    core.parts.log = function (data, target) {
        $.each(data, function(k,v) {
            var content;

            var rendered = renderers[v.type] ? renderers[v.type](v.data) : $('<div />').text('[RENDER ERROR] NO RENDERER PROVIDED FOR GIVEN MTYPE (' + v.type + ')!');
            var expandable = rendered.data('expandable');

            target.append(
                $('<div />').addClass('col rw-12 message' + (expandable ? ' pointer' : '')).append(
                    $('<div />').addClass(v.new ? 'timestamp new' : 'timestamp').text((new Date(v.time * 1000)).toLocaleTimeString())
                ).append($('<br />').addClass('hide-desktop'))
                .append(
                    content = $('<div />').addClass('content').append(rendered)
                ).click(function() {
                    if (!expandable) return;
                    rendered.trigger('expand').off('expand');
                    $(this).find('.sub').slideToggle(200);
                })
            );
            content.find('.sub').hide();
        })
    };
})();
(function() {
    var render_others = function(data, target, messages) {
        target.append($('<h3 />').text(core.last.players.multiplayer ? "Andere Spieler und NPCs" : "NPCs in deiner Umgebung"));

        var row = NF.row().appendTo(target);
        var found = false;
        $.each(data, function(id, player) {
            found = true;
            var box = $('<div />').addClass('playerbox' + (player.escort ? ' escort' : '') + (player.local ? '' : ' unknown') + (player.npc ? ' npc' : '')).appendTo($('<div />').addClass('cell rw-4 rw-lg-6 rw-md-4 rw-sm-12 padded').appendTo(row));

            box.append($('<b />').text(player.name));
            var bars = NF.row().appendTo(box);

            box.children('b').css('cursor', 'default').attr('title', '-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty().css('width', 360);

                    var table;
                    content.append(
                        $('<b />').addClass('header').text(player.name)
                    ).append(table = NF.row());

                    var date = new Date(player.last_seen * 1000);

                    if (player.npc)
                        NF.row()
                            .append(NF.cell(true, 12).text("Dies ist ein computergesteuerter Charakter (NPC)!"))
                            .appendTo(content);
                    else {
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text("Beruf"))
                            .append(NF.cell(true, 6, 0, 'left').text(player.job))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text("Spielgeschwindigkeit"))
                            .append(NF.cell(true, 6, 0, 'left').text(core.snippets.timestr(player.speed)))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text("Letzte Aktivit\u00e4t"))
                            .append(NF.cell(true, 6, 0, 'left').text(date.toLocaleString()))
                            .appendTo(content);
                        NF.row()
                            .append(NF.cell(true, 6, 0, 'b right').text(player.joke[0]))
                            .append(NF.cell(true, 6, 0, 'left').text(player.joke[1]))
                            .appendTo(content);
                    }

                }
            }));

            if (player.stats)
                core.parts.status_bars(bars, player.stats, true);

            //if (!(v.allow === true || v.allow[1])) return;
            if (player.npc) {


                if (player.inventory.action) {

                    var abortable = player.inventory.action.abort && (player.allow === true || player.allow[7]);
                    var action_row = NF.row().appendTo(box);

                    action_row.append(NF.cell(true, abortable ? 8 : 12, 0, 'b center').text(player.inventory.action.name));

                    if (abortable)
                        action_row.append(NF.cell(true, 4, 0, 'center').append(
                            $('<div />').addClass('btn small').append(NF.fa('times')).click(function () {
                                if (confirm(game.i18n("Bist du sicher, dass :name diese Aktion abbrechen soll?", {':name': player.name})))
                                    core.command('act/cancel', {p: player.id});
                            })
                        ));

                    if (player.inventory.action.remaining) {
                        var d = 1;
                        for (var i = 1; i <= 3; i++)
                            if (player.inventory.action.remaining[i] > 0) d = i;

                        if (d) {
                            var l = 12 / (d + 1);
                            var timerow;
                            action_row.append(NF.cell(true, 12).append(timerow = NF.row().addClass('center')));

                            var elems = ["Minuten","Stunden","Tage","Wochen"];

                            for (i = d; i >= 0; i--)
                                timerow.append($('<div />').addClass('cell rw-' + l).append(
                                    $('<div />').append(
                                        $('<h4 />').text(elems[i])
                                    ).append(
                                        $('<span />').text(player.inventory.action.remaining[i])
                                    )
                                ))
                        }
                    }
                }
            }

        });

        if (!found) row.append(NF.cell(true, 12, 0, 'center').text("Hier scheint niemand zu sein ..."));

        if (core.last.players.multiplayer)
            row.append(NF.cell(true, 12).append(
                NF.row()
                    .append($('<div />').addClass('cell rw-4 rw-md-5 rw-sm-12 padded').append($('<div />').addClass('btn btn-zv').text("Post").prepend(messages ? $('<img />').attr('src','media/icons/new.png') : false).click(function() {
                        game.network.load('game/pm');
                    })))
            ));
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

        if (core.last.players.multiplayer) {
            var player_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-4 rw-lg-6 rw-md-12 padded').appendTo(target));
            render_self(data.self, player_info);
        }

        var others_info = $('<div />').addClass('flatbox').appendTo($('<div />').addClass(core.last.players.multiplayer ? 'cell rw-8 rw-lg-6 rw-md-12 padded' : 'cell rw-8 ro-2 rw-lg-10 ro-lg-1 rw-md-12 ro-md-0 padded').appendTo(target));
        render_others(data.others, others_info, data.messages);

    };
})();(function() {
    core.plugins.Minimap = function(canvas) {
        this.canvas = $(canvas).get(0);
        this.size = Math.min(canvas.width, canvas.height);
        this.environments = [];
        this.status = 0;
        this.overlay = null;
        this.player = null;
        this.autorender = false;
        this.images = {final: false, count: 0, ready: 0, cache: []};
        if (!this.initialize())
            alert('plugin:minimap init failed');
    };

    core.plugins.Minimap.prototype.requestImage = function(path, last_one) {
        var alias = this;
        if (this.images.cache[path]) return true;
        if (last_one) this.images.final = true;

        this.images.count++;
        this.images.cache[path] = new Image();
        this.images.cache[path].addEventListener("load", function() {
            alias.images.ready++;
            if (alias.images.final && alias.images.count == alias.images.ready) {
                alias.status = 2;
                if (alias.autorender) alias.updateRenderer();
            }
        }, false);
        this.images.cache[path].src = path;
    };

    core.plugins.Minimap.prototype.getImage = function(path) {
        if (!this.images.cache[path]) return null;
        return this.images.cache[path];
    };

    /**
     * Returns true if the module is fully initialized
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.is_initialized = function() {
        return this.status >= 2;
    };

    /**
     * Returns true if the module is currently initializing. Will also return true if the module is already initialized and allow_initialized is true.
     * @param [allow_initialized] {boolean} If true, the module having been initialized already will cause this function to return true as well.
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.is_initializing = function(allow_initialized) {
        return this.status == 1 || (allow_initialized && this.is_initialized());
    };

    core.plugins.Minimap.prototype.postProcessing = function(imgData) {
        return imgData;
    };

    core.plugins.Minimap.prototype.reset = function(canvas, keepSurroundingData) {
        this.stage.enableDOMEvents(false);
        this.stage.canvas = $(canvas).get(0);
        this.stage.enableDOMEvents(true);

        if (!keepSurroundingData)
            $.map(this.environments, function(v) {
                return (v.x == v.y && v.x == 0) ? v : null;
            });
    };

    core.plugins.Minimap.prototype.startStop = function(start) {
        if (start) {
            createjs.Ticker.removeAllEventListeners('tick');
            var alias = this;
            createjs.Ticker.timingMode = 'synched';
            createjs.Ticker.framerate = 60;
            createjs.Ticker.addEventListener("tick", function() {
                alias.stage.update();
                var ctx = $(alias.canvas).get(0).getContext('2d');
                ctx.putImageData(alias.postProcessing(ctx.getImageData(0,0,alias.size,alias.size)),0,0);
            });
        } else createjs.Ticker.reset();
    };

    /**
     * Initializes the module and loads missing assets
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.initialize = function() {
        var alias = this;

        if (this.is_initializing(true)) return true;

        this.status = 1;
        this.stage = new createjs.Stage($(this.canvas).attr({height: this.size, width: this.size}).get(0));
        this.stage.regX = this.stage.regY = .5;

        this.requestImage('media/icons/minimap/floor.png', true);

        this.startStop(true);
        return true;
    };

    /**
     * Adds a screen to the map at the specified position
     * @param pos_x {int} X position
     * @param pos_y {int} Y position
     * @param top {int} North corridor ID (or 0 to omit)
     * @param bottom {int} South corridor ID (or 0 to omit)
     * @param left {int} West corridor ID (or 0 to omit)
     * @param right {int} South corridor ID (or 0 to omit)
     * @returns {core.plugins.Minimap}
     * @param zombies {int} Number of zombies
     * @param players {int} Number of other players
     */
    core.plugins.Minimap.prototype.addEnvironment = function(pos_x, pos_y, top, bottom, left, right, zombies, players) {
        $.map(this.environments, function(v) {
            return (v.x == pos_x && v.y == pos_y) ? null : v;
        });

        this.environments.push({x: pos_x, y: pos_y, container: null, top: top, bottom: bottom, left: left, right: right, zombies: zombies, players: players, size: (top || bottom || left || right) ? 0.25 : 0.65});
        this.updateRenderer();
        return this;
    };

    /**
     * Shifts the view by the given coordinates
     * @param x {int} X Shift
     * @param y {int} Y Shift
     * @param duration {int} Animation duration
     * @param [callback] {function} Callback function (called after animation finishes)
     * @returns {core.plugins.Minimap}
     */
    core.plugins.Minimap.prototype.shift = function(x, y, duration, callback) {
        var alias = this;
        setTimeout(callback, duration);

        createjs.Tween.get(this.player)
            .to({x: this.player.x + (x * 0.1 * this.size), y: this.player.y + (y * 0.1 * this.size)}, duration/5, createjs.Ease.getPowInOut(2))
            .to({x: this.player.x - (x * 0.2 * this.size), y: this.player.y - (y * 0.2 * this.size)}, duration/2, createjs.Ease.getPowInOut(2))
            .to({x: this.player.x, y: this.player.y}, duration/2, createjs.Ease.getPowInOut(2));

        $.each(this.environments, function(k,v) {
            createjs.Tween.get(v.container)
                .to({ x: alias.size * (v.x - x), y: alias.size * (v.y - y)}, duration, createjs.Ease.getPowInOut(4))
                .call(function() {
                    v.x -= x;
                    v.y -= y;
                });
        });
        return this;
    };

    /**
     * Updates the stage. Run this function after adding elements.
     * @returns {boolean}
     */
    core.plugins.Minimap.prototype.updateRenderer = function() {
        if (!this.is_initialized())
            return this.autorender = true;

        var alias = this;

        if (!this.overlay) {
            this.overlay = new createjs.Container();
            var lense_shadow = new createjs.Shape();
            lense_shadow.graphics.beginRadialGradientFill(['rgba(0,0,0,0)','rgba(0,0,0,1)'],[0,1],this.size/2,this.size/2,this.size/6,this.size/2,this.size/2,this.size/1.3).rect(0,0,alias.size,alias.size);

            this.player = new createjs.Bitmap('media/icons/minimap/citizen.png');
            this.player.x = this.player.y = this.size/2 - 12;


            this.overlay.addChild(lense_shadow, this.player);
        }

        $.each(this.environments, function(k,v) {
            var c_width = alias.size * v.size;
            var ceil_width = alias.size/20;
            var wall_height = alias.size/8;
            var m = alias.size/2 - c_width/2;

            if (!v.container) {
                v.container = new createjs.Container();

                var floor = new createjs.Shape();
                floor.graphics.beginBitmapFill(alias.getImage('media/icons/minimap/floor.png')).rect(0,0,alias.size,alias.size);

                var walls = new createjs.Shape();
                walls.graphics.beginFill('#000000')
                    .rect(0, 0, m + (v.top ? 0 : c_width), m)
                    .rect(0, alias.size, m, -m - (v.left ? 0 : c_width))
                    .rect(alias.size, alias.size, -m - (v.bottom ? 0 : c_width), -m)
                    .rect(alias.size, 0, -m, m + (v.right ? 0 : c_width));

                v.container.addChild(floor, walls);

                var dots = {
                    topleft: [m,m - wall_height],
                    topright: [alias.size-m,m - wall_height],
                    bottomleft: [m,alias.size-m],
                    bottomright: [alias.size-m,alias.size-m]
                };

                var frameColor = '#462D21';
                var wallColors = ['#00934C','#00D37A'];

                //Columns
                walls.graphics.beginFill(frameColor)
                    .rect(dots.topleft[0],dots.topleft[1],          -ceil_width,-ceil_width)
                    .rect(dots.topright[0],dots.topright[1],        ceil_width,-ceil_width)
                    .rect(dots.bottomleft[0],dots.bottomleft[1],    -ceil_width,ceil_width)
                    .rect(dots.bottomright[0],dots.bottomright[1],  ceil_width,ceil_width);

                if (v.top) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.topleft[0],dots.topleft[1],      -ceil_width, -(m - wall_height))
                        .rect(dots.topright[0],dots.topright[1],    ceil_width, -(m - wall_height));
                } else {
                    walls.graphics.beginFill(frameColor).rect(dots.topleft[0] - ceil_width, dots.topleft[1], c_width + 2* ceil_width, -ceil_width);
                    walls.graphics.beginLinearGradientFill([wallColors[0],wallColors[1],wallColors[1],wallColors[0]], [0,0.1,0.9,1],0,m - wall_height,0,m).rect(dots.topleft[0], dots.topleft[1], c_width, wall_height);
                }

                if (v.left) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.topleft[0],dots.topleft[1],          -m, -ceil_width)
                        .rect(dots.bottomleft[0],dots.bottomleft[1],    -m, ceil_width);
                    walls.graphics.beginLinearGradientFill([wallColors[0],wallColors[1],wallColors[1],wallColors[0]], [0,0.1,0.9,1],0,m - wall_height,0,m).rect(dots.topleft[0], dots.topleft[1],-m, wall_height);
                } else walls.graphics.beginFill(frameColor).rect(dots.topleft[0], dots.topleft[1] - ceil_width, -ceil_width, c_width + 2* ceil_width + wall_height);

                if (v.bottom) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.bottomleft[0],dots.bottomleft[1],      -ceil_width, m)
                        .rect(dots.bottomright[0],dots.bottomright[1],    ceil_width, m);
                } else walls.graphics.beginFill(frameColor).rect(dots.bottomleft[0] - ceil_width, dots.bottomleft[1], c_width + 2* ceil_width, ceil_width);

                if (v.right) {
                    walls.graphics.beginFill(frameColor)
                        .rect(dots.topright[0],dots.topright[1],          m, -ceil_width)
                        .rect(dots.bottomright[0],dots.bottomright[1],    m, ceil_width);
                    walls.graphics.beginLinearGradientFill([wallColors[0],wallColors[1],wallColors[1],wallColors[0]], [0,0.1,0.9,1],0,m - wall_height,0,m).rect(dots.topright[0], dots.topright[1],m, wall_height);
                } else walls.graphics.beginFill(frameColor).rect(dots.topright[0], dots.topright[1] - ceil_width, ceil_width, c_width + 2* ceil_width + wall_height);

                var i;
                var actors = [];
                for (i = 0; i < v.zombies + v.players; i++) {
                    var actor = new createjs.Bitmap(i >= v.zombies ? 'media/icons/minimap/citizen.png' : 'media/icons/minimap/zombie.png');
                    actor.x = m + Math.random() * (c_width - 24);
                    actor.y = m + Math.random() * (c_width - 24);
                    actors.push(actor);
                }
                actors.sort(function(a,b) {return a.y - b.y});

                $.each(actors, function(id, actor) {v.container.addChild(actor);});

                v.container.x = v.x * alias.size;
                v.container.y = v.y * alias.size;

                alias.stage.addChild(v.container);
            }
        });

        this.stage.addChild(this.overlay);
        return true;
    }
})();core.popup = {
    spawn: function(dx,dy) {

        var exp = function(w) {
            if (typeof w == 'undefined')
                w = 'auto';
            if (typeof w !== 'object')
                w = {desktop: w, lg: w, md: w, sm: w};

            w.desktop = w.desktop || w.lg || w.md || w.sm;
            w.lg = w.lg || w.desktop || w.md || w.sm;
            w.md = w.md || w.lg || w.sm || w.desktop;
            w.sm = w.sm || w.md || w.lg || w.desktop;
            return w;
        };

        var gt = function(w, ref) {
            var decoded;
            if (typeof w !== 'object') decoded = w;
            else if (!game.mobile) decoded = w.desktop;
            else decoded = w[game.mobile];

            if (typeof decoded == "string") {

                if (decoded == 'auto') {}
                else if (decoded[decoded.length-1] == '%')
                    decoded = ref * (parseFloat(decoded.substr(0,decoded.length-1)/100));
                else decoded = parseFloat(decoded);

            }

            return decoded;
        };

        dx = exp(dx);
        dy = exp(dy);

        var wrapper = $('<div />').addClass('popup-wrapper').appendTo($('body').css('overflow','hidden'));

        var popup = $('<div />').addClass('popup')
            .on('reposition', function() {

                var ldx = gt(dx, $(window).width());
                var ldy = gt(dy, window.innerHeight);

                $(this).css({
                    top: ldy == 'auto' ? 30 : (((ldy + 60) > window.innerHeight) ? 0 : 60),
                    left: ldx == 'auto' ? 0 : Math.max(0,$(window).width()/2 - ldx/2),
                    height: ldy,
                    width: ldx
                });
            }).trigger('reposition').appendTo(wrapper);

        var z = game.render.html.modal.blend(function() {
            popup.trigger('close').addClass('disabled').css({
                '-webkit-filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)',
                'filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)'
            }).animate({
                opacity: 0,
                transform: game.s.quality() > 1 ? 'scale(1.5)' : 'scale(1)'
            }, 400, 'swing', function() {
                $(this).parent().remove();
                if (!$('.popup').length)
                    $('body').css('overflow','auto');
            });
        }, true, true);

        wrapper.css('z-index',z+1);

        popup.on('unpop', function() {
            game.render.html.modal.unblend(z,true)
        }).css({
            opacity: 0,
            transform: game.s.quality() > 1 ? 'scale(0.5)' : 'scale(1)',
            'transition': 'filter 0.4s ease, -webkit-filter 0.4s ease',
            '-webkit-filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)',
            'filter': (game.mobile || game.s.quality() <= 2) ? '' : 'blur(5px)'
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
        var popup = core.popup.spawn({desktop: 500, md: '100%'},{desktop: 300, md: '100%'});

        var frame = NF.row().appendTo(
            $('<div />').css({
                position: 'absolute',
                width: '100%',
                left: 0,
                top: 0,
                bottom: 36,
                overflow: 'auto'
            }).appendTo(popup)
        );

        var bottom = NF.row().css({
            position: 'absolute',
            width: '100%',
            left: 0,
            bottom: 0,
            height: 36,
            overflow: 'auto'
        }).appendTo(popup);

        $('<div />').addClass('btn').text("Abbrechen").appendTo($('<div />').addClass('cell rw-4 rw-sm-6').appendTo(bottom)).click(function() {
            popup.trigger('unpop');
        });
        $('<div />').addClass('btn').text("Anwenden").appendTo($('<div />').addClass('cell ro-4 rw-4 rw-sm-6 ro-sm-0').appendTo(bottom)).click(function() {
            callback(typeFilterData);
            popup.trigger('unpop');
        });

        var typefilters, classfilters, class_cell;
        frame.append(
            NF.row().append(
                $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text("Status")).append(typefilters = NF.row()))
            ).append(
                class_cell = $('<div />').addClass('cell rw-12 padded').append($('<div />').addClass('flatbox').append($('<h3 />').text("Kategorie")).append(classfilters = NF.row()))
            )
        );

        $.each(typeFilterData, function(id, obj) {
            if (id == 'categories') return;
            var chk;
            typefilters.append(
                $('<div />').addClass('cell rw-6 rw-sm-12').append(
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
                $('<div />').addClass('cell rw-4 rw-sm-6').append(
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
        if (!popup) popup = core.popup.spawn({desktop: 700, lg: '100%'},{desktop: 450, lg: '100%'});

        if (!frame || !filters || !close) {
            popup.empty();

            frame = NF.row().appendTo(
                $('<div />').css({
                    position: 'absolute',
                    width: '100%',
                    left: 0,
                    top: 24,
                    bottom: 0,
                    overflow: 'auto'
                }).appendTo(popup)
            ).data('type-filters', {
                    'impossible': {active: false, name: "Unm\u00f6gliche Projekte"},
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

                    $(this).find('.blueprint-group').each(function() {
                        $(this).show();
                        if (!$(this).find('.blueprint:visible').length) $(this).hide();
                    });
            });

            NF.row().append(
                filters = $('<div />').addClass('cell rw-11')
            ).append(
                close = $('<div />').addClass('cell rw-1 right')
            ).appendTo(popup);

            filters.append($('<div />').addClass('btn small btn-exp').text("Angezeigte Projekte filtern...").click(function() {
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
            var categories = {}; var cat_count = 0;
            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;

                if (!$.objToArray(v.categories).length) v.categories = ["Sonstiges"];
                $.each(v.categories, function(i,cat) {
                    if (!categories[cat]) cat_count++;
                    categories[cat] = true;
                });
            });

            if (cat_count > 1)
                $.each(categories, function(n,t) {
                    frame.append($('<div />').attr('data-group-cat',n).addClass('blueprint-group').append($('<b />').addClass('header').text(n)).append($('<div />').addClass('blueprint-group-target row')));
                });

            $.each(bdata.blueprints, function(k,v) {
                if (v.hidden) return;
                if (!$.objToArray(v.categories).length) v.categories = ["Sonstiges"];
                var targets = $();
                if (cat_count > 1) $.each(v.categories, function(i,cat) {targets = targets.add(frame.find('div[data-group-cat="' + cat + '"]').find('.blueprint-group-target'));});
                else targets = frame;

                targets.append($('<div />').addClass('cell padded rw-4 rw-md-6').append(core.snippets.blueprint(v, bdata.energy, bdata.zombies, bdata.blueprints, function() {
                    if (v.confirm && !window.confirm(game.i18n(v.confirm, {':name': v.name})))
                        return;

                    var prev_scroll = $('.popup').find('>*:first-child').scrollTop();
                    popup.addClass('disabled');
                    core.command('location/' + type, {build: k}, true, function(new_data) {
                        core.popup.genericBlueprintLoader(type,popup,new_data,frame,filters,close);
                        popup.removeClass('disabled');
                        frame.trigger('filter');
                        $('.popup').find('>*:first-child').animate({scrollTop: prev_scroll}, 0);
                        popup.off('close').on('close', function() {
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
            frame.data('type-filters', tf).trigger('filter');
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
};core.popup.map = function() {

    var win_d;
    if (screen.width < 804 || screen.height < 604)
        win_d = [screen.width, screen.height];
    else win_d = [804,604];

    var popup = core.popup.spawn(win_d[0], win_d[1]).css('overflow','hidden');


    popup.attr('tabindex', 1).append(core.snippets.wait()).focus();

    var init = function(data) {

        var overlay, help, list, list_inner, dbl1, dbl2, control;

        popup.empty()
            .append($('<canvas />').attr({id: 'gamemap', height: win_d[1] - 4, width: win_d[0] - 4}))
            .append(
                overlay = $('<div />').addClass('map panel bottom hide no-interaction').addClass(win_d[0] < 804 ? 'wide' : '')
            ).append(
                control = $('<div />').addClass('map panel bottom').addClass(!game.touch() ? 'hide' : '' ).addClass(win_d[0] < 804 ? 'wide' : '')
            ).append(
                help = $('<div />').addClass('map panel left hide no-interaction').addClass(win_d[0] < 804 ? 'wide' : '')
                    .append(NF.row()
                        .append(NF.cell(true, 12, 0, 'center b').text("Maus"))
                        .append(NF.cell(true, 12, 0).append(NF.n('ul')
                            .append(NF.n('li', '', "<b>Linke Maustaste<\/b> halten und <b>Maus bewegen<\/b>, um den Kartenausschnitt zu verschieben.", true))
                            .append(NF.n('li', '', "<b>Mausrad<\/b> drehen, um zu zoomen.", true))
                            .append(NF.n('li', '', "<b>Mittlere Maustaste<\/b> dr\u00fccken, um die Karte zur\u00fcckzusetzen.", true))
                        ))
                        .append(NF.cell(true, 12, 0, 'center b').text("Tastatur"))
                        .append(NF.cell(true, 12, 0).append(NF.n('ul')
                            .append(NF.n('li', '', "<b>Pfeiltasten<\/b> benutzen, um den Kartenausschnitt zu verschieben.", true))
                            .append(NF.n('li', '', "<b>+<\/b> und <b>-<\/b>-Tasten verwenden, um zu zoomen.", true))
                            .append(NF.n('li', '', "<b>0<\/b> oder <b>R<\/b>-Tasten verwenden, um die Karte zur\u00fcckzusetzen.", true))
                        ))
                    )
            ).append(game.touch() || win_d[0] < 640 ? null : $('<div />').addClass('map panel left-top center').addClass(win_d[0] < 804 ? 'wide' : '')
                .append(NF.n('span', 'b', "Steuerung"))
                .append(dbl1 = NF.fa('angle-double-right').addClass('pointer').css('float', 'right'))
                .click(function() {
                    if (help.hasClass('hide')) {
                        help.removeClass('hide');
                        dbl1.removeClass('fa-angle-double-right').addClass('fa-angle-double-left')
                    } else {
                        help.addClass('hide');
                        dbl1.addClass('fa-angle-double-right').removeClass('fa-angle-double-left')
                    }
                })
            ).append(
                list = $('<div />').addClass('map panel right manual-color hide').addClass(win_d[0] < 804 ? 'wide' : '').css('overflow-y','auto').append(NF.row().append(list_inner = NF.cell(true, 12)))
            ).append($('<div />').addClass('map panel right-top center').addClass(win_d[0] < 804 ? 'wide' : '')
                .append(NF.n('span', 'b', "Orte"))
                .append(dbl2 = NF.fa('angle-double-left').addClass('pointer').css('float', 'left'))
                .click(function() {
                    if (list.hasClass('hide')) {
                        list.removeClass('hide');
                        dbl2.removeClass('fa-angle-double-left').addClass('fa-angle-double-right')
                    } else {
                        list.addClass('hide');
                        dbl2.addClass('fa-angle-double-left').removeClass('fa-angle-double-right')
                    }
                })
            );

        var map = new Gamemap('gamemap', data, win_d[0] - 4, win_d[1] - 4);
        control.append(NF.row('center')
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {popup.trigger('unpop');}).append(NF.fa('close')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.zoom( 0);}).append(NF.fa('crosshairs')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.zoom( 1);}).append(NF.fa('search-plus')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.zoom(-1);}).append(NF.fa('search-minus')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll(0, 48);}).append(NF.fa('arrow-up')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll(0,-48);}).append(NF.fa('arrow-down')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll( 48,0);}).append(NF.fa('arrow-left')))
            .append(NF.scell(true, 3, 0, 'panelbtn').click(function() {map.scroll(-48,0);}).append(NF.fa('arrow-right')))
        );

        $.each(data.locations, function(id, location) {
            if (id == data.current) return;
            list_inner.append(NF.row().append(NF.cell(false, 12, 0, 'hotbox').addClass(location.energy > data.radius ? 'disabled' : '').on('mouseover', function() {map.hover(id);}).on('mouseout', function() {map.unhover(id);}).on('click', function() {map.handler(id, 'click')})
                .append(NF.row('center').append(NF.cell(true, 12).append(NF.n('b', '', location.name)).append(NF.img('media/icons/places/' + location.icon).css('float','left'))))
                .append(NF.row('center').append(
                    NF.cell(false, 3)
                        .append(NF.img('media/icons/distance.gif'))
                        .append(NF.n('span','',location.distance))
                    ).append(
                    NF.cell(false, 3)
                        .append(NF.img('media/icons/status_energy.gif'))
                        .append(NF.n('span','',location.energy))
                    ).append(
                    NF.cell(false, 3)
                        .append(NF.img('media/icons/zombie.gif'))
                        .append(NF.n('span','',location.zombies))
                    ).append(
                    NF.cell(false, 3)
                        .append(NF.img('media/icons/status_weight.gif'))
                        .append(NF.n('span','',location.weight === null ? 0 : location.weight))
                    )
                )
            ).attr('data-sort',true).attr('data-sort-type', location.icon).attr('data-sort-cron', id).attr('data-sort-dist', location.distance).attr('data-sort-name', location.name))
        });

        list_inner.prepend(NF.row('center').append(NF.cell(true, 12).append(
            NF.select({'dist': "Entfernung", 'name': "Name", 'type': "Typ", 'cron': "Chronologisch"}, 'dist')
        ))).find('select').on('change', function() {
            var by = $(this).val();
            var items = list_inner.children('[data-sort]').sort(function(a, b) {
                var a_val = $(a).attr('data-sort-' + by);
                var b_val = $(b).attr('data-sort-' + by);

                if (!isNaN(a_val)) a_val = parseFloat(a_val);
                if (!isNaN(b_val)) b_val = parseFloat(b_val);

                return (a_val < b_val) ? -1 : (a_val > b_val) ? 1 : 0;
            });
            list_inner.append(items);

        }).trigger('change').selectric();

        popup.on('close', function() {
            map.end();
        }).on('mousewheel', function(event) {
            map.zoom(event.deltaY);
        }).on('mousedown', function(event) {
            if (event.which == 2) map.zoom(0);
        }).on('keydown', function(event) {
            switch (event.key) {
                case "+": map.zoom(1); return;
                case "-": map.zoom(-1); return;
                case "0":case "r": map.zoom(0); return;
            }
            switch (event.which) {
                case 37: map.scroll(-24,0); event.preventDefault(); return; //LEFT
                case 38: map.scroll(0,-24); event.preventDefault(); return; //UP
                case 39: map.scroll(24,0); event.preventDefault(); return; //RIGHT
                case 40: map.scroll(0, 24); event.preventDefault(); return; //DOWN
            }
        });

        map.load();
        map.setHandler(function(id, event) {
            switch (event) {
                case 'click':
                    if ((data.read_only && !data.locations[id].skip_ro) || data.locations[id].energy > data.radius || id == data.current) return;

                    if (data.locations[id].zombies && !confirm(game.i18n("Dieser Ort wird von :zombies Zombies belagert. Wenn du diesen Ort betrittst, wirst du k\u00e4mpfen m\u00fcssen. Weiter?", {':zombies': data.locations[id].zombies}))) return;

                    var route_zombies = [];
                    $.each(data.locations[id].route, function(rkey, rval) {
                        if (rval == data.locations[id].id || rval == data.current) return;
                        if (data.locations[rval].zombies > 0)
                            route_zombies.push(data.locations[rval].name);
                    });
                    if (route_zombies.length && !confirm(game.i18n("Auf dem Weg zu diesem Ort befinden sich Zombies (:locations). Du wirst gegen sie k\u00e4mpfen m\u00fcssen, wenn du dorthin m\u00f6chtest. Weiter?",{':locations': route_zombies.join(', ')}))) return;

                    var escortables = false;
                    if (core.last.players && core.last.players.others)
                        $.each(core.last.players.others, function(id, player) {
                            if (player.local && (player.allow === true || player.allow[6]))
                                escortables = true;
                        });

                    if (game.storage.get('settings','travel_confirm') != 'auto' || escortables || game.touch()) {
                        var esc_popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        var title;
                        esc_popup.append($('<h2 />').addClass('center').text(data.locations[id].name));

                        if (game.touch())
                            esc_popup.append(NF.row('center').append(
                                    NF.cell(false, 3)
                                        .append(NF.img('media/icons/distance.gif'))
                                        .append(NF.n('span','',data.locations[id].distance))
                                    ).append(
                                    NF.cell(false, 3)
                                        .append(NF.img('media/icons/status_energy.gif'))
                                        .append(NF.n('span','',data.locations[id].energy))
                                    ).append(
                                    NF.cell(false, 3)
                                        .append(NF.img('media/icons/zombie.gif'))
                                        .append(NF.n('span','',data.locations[id].zombies))
                                    ).append(
                                    NF.cell(false, 3)
                                        .append(NF.img('media/icons/status_weight.gif'))
                                        .append(NF.n('span','',data.locations[id].weight === null ? 0 : data.locations[id].weight))
                                    )
                                );

                        esc_popup.append(
                            NF.row().append(title = NF.cell(true, 12).text("Wenn du dich alleine f\u00fcrchtest, kannst du andere Spieler bitten, dich zu begleiten. Oder noch besser, schick sie am besten direkt vor, nicht dass noch jemand (z.B. du) verletzt wird!"))
                        );

                        var check_row = $('<form />').addClass('row').appendTo(esc_popup);

                        if (escortables && core.last.players && core.last.players.others)
                            $.each(core.last.players.others, function(id, player) {
                                if (player.allow === true ||player.allow[6])
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

                        esc_popup.append(NF.row()
                            .append($('<div />').addClass('cell rw-8 rw-sm-12 padded').append(
                                $('<div />').addClass('btn').text("Los gehts!").click(function() {

                                    var cfg = {to: id, follow: 1};
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
                            .append($('<div />').addClass('cell rw-4 rw-sm-12 padded').append(
                                $('<div />').addClass('btn').text("Abbrechen").click(function() {
                                    esc_popup.trigger('unpop');
                                })))
                        );
                    } else {
                        popup.addClass('disabled');
                        core.command('map/go', {to: id, follow: 1}, true, function(data) {
                            popup.removeClass('disabled');
                            if (data.success) {
                                popup.trigger('unpop');
                                core.command();
                            }
                        });
                    }

                    break;
                case 'mouseover':
                    if (!game.touch())
                        overlay.empty().append(
                            $('<h3 />').text(data.locations[id].name)
                        ).append(
                            NF.row('center').append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/distance.gif'))
                                    .append(NF.n('span','',data.locations[id].distance))
                            ).append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/status_energy.gif'))
                                    .append(NF.n('span','',data.locations[id].energy))
                            ).append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/zombie.gif'))
                                    .append(NF.n('span','',data.locations[id].zombies))
                            ).append(
                                NF.cell(false, 3)
                                    .append(NF.img('media/icons/status_weight.gif'))
                                    .append(NF.n('span','',data.locations[id].weight === null ? 0 : data.locations[id].weight))
                            )
                        ).removeClass('hide');
                    break;
                case 'mouseout':
                    overlay.addClass('hide');
                    break;
            }
        });
        map.begin();

    };


    core.command('map/data', {}, true, function(data) {

        if (typeof Gamemap === "undefined")

            $.ajax({
                url: 'web/map/?l=' + game.lang(),
                dataType: "script",
                headers: { 'X-Skip-ETag': game.storage.get('update','force_next_update',false) ? '1' : '0' },
                cache: true,
                success: function() {init(data)}
            }).fail(function( jqxhr, settings, exception ) {
                popup.empty().append($('<div />').addClass('center').text(exception.message));
            });
        else init(data);

    });
};(function() {

    var render_effect_list = function(f, mark) {
        var tmp = NF.row('center');
        $.each(f, function(id, v) {
            switch (parseInt(id)) {
                case NaN: break;
                case 1: id = 'ini'; break;
                case 2: id = 'atk'; break;
                case 3: id = 'def'; break;
                case 4: id = 'acc'; break;
            }

            tmp.append(NF.cell(true, 3).append($('<div />').addClass('rpg stat').addClass(v > 0 ? 'plus' : (v == 0 ? 'null' : 'minus')).addClass(id).text(v).css('opacity', (!mark || mark == id) ? 1 : 0.75)));
        });
        return tmp;
    };

    var render_stats = function(data, target) {
        var max_p = 0;
        var max_m = 0;

        $.each(data, function(id, block) {
            var row = NF.row().appendTo(target);

            var bar_p;
            var bar_m;
            row.append(NF.cell(true, {desktop: 10, md: 8, sm: 6}).append($('<div />').addClass('rpg statbar').append(bar_p = $('<div />').addClass('rpg barcontainer plus')).append( bar_m = $('<div />').addClass('rpg barcontainer minus'))));

            var t = 'unk';

            switch (parseInt(id)) {
                case 1: t = 'ini'; break;
                case 2: t = 'atk'; break;
                case 3: t = 'def'; break;
                case 4: t = 'acc'; break;
            }

            var acc_m = 0; var acc_p = 0;
            $.each(block, function(k, elem) {
                if (elem.value > 0) acc_p += elem.value;
                else acc_m -= elem.value;
            });

            $.each(block, function(k, elem) {
                var b;
                if (elem.value > 0) bar_p.append(b = $('<div />').addClass('block').data('w', Math.abs(elem.value)));
                else bar_m.append(b = $('<div />').addClass('block').data('w', Math.abs(elem.value)));

                switch (elem.type) {
                    case 0:
                        b.qtt('top', function() {
                            $(this)
                                .append(NF.n('b','header',"Menschlichkeit"))
                                .append(NF.n('p','',"Als Mensch bist du den meisten Zombies k\u00f6rperlich zumindest ein wenig \u00fcberlegen."))
                                .append(NF.separator())
                                .append(render_effect_list(elem.all, t))
                        });
                        break;
                    case 2:
                    case 1:
                    case 3:
                    case 4:
                        b.append($('<img />').attr('src','media/icons/' + elem.icon + '.gif')).qtt('top', function() {
                            $(this)
                                .append(NF.n('b','header',elem.name))
                                .append(NF.n('p','',"Dieser Ausr\u00fcstungsgegenstand beeinflusst deine Kampfwerte. Lege ihn ab, um diese Effekte zu beenden."))
                                .append(NF.separator())
                                .append(render_effect_list(elem.all, t))
                        });
                        break;
                    case 5:
                        b.append($('<img />').attr('src','media/icons/' + elem.icon + '.gif')).qtt('top', function() {
                            $(this)
                                .append(NF.n('b','header',elem.name))
                                .append(NF.n('p','',"Diese Waffe beeinflusst deine Kampfwerte. Im Gegensatz zu R\u00fcstungsgegenst\u00e4nden kommt dieser Einfluss jedoch nur zum Tragen, wenn die Waffe tats\u00e4chlich im Kampf verwendet wird."))
                                .append(NF.separator())
                                .append(render_effect_list(elem.all, t))
                        });
                        break;
                                                api.hide();
                }

            bar_p.data('r', acc_m);

            });

            max_p = Math.max(max_p, acc_p);
            max_m = Math.max(max_m, acc_m);

            var v = acc_p - acc_m; var txt = null;
            if (v < 0) txt = v + '';
            else if (v > 20) txt = '+' + (v-20);

            row.append($('<div />').addClass('cell rw-2 rw-md-4 rw-sm-6 padded').append($('<div />').addClass('rpg stat null').addClass(t).text(Math.min(Math.max(0,acc_p - acc_m),20)).append($('<span />').text(txt ? '(' + txt + ')' : ''))));
        });

        max_p = Math.max(20,max_p);
        max_m = Math.max(0,max_m);

        target.find('.block').each(function() {
            $(this).css('width', ($(this).data('w') * 100/($(this).parent().is('.plus') ? max_p : max_m)) + '%');
        });

        target.find('.rpg.barcontainer').each(function() {
            $(this).css({
                width: 100 * (($(this).is('.plus') ? max_p : max_m)/(max_p+max_m)) + '%',
                left: 100 * ($(this).is('.plus') ? ((max_m-$(this).data('r'))/(max_m+max_p)) : 0) + '%',
                'text-align': $(this).is('.plus') ? 'left' : 'right'
            });
        });

        target.find('.rpg.statbar').each(function() {
            var grid;
            $(this).append(grid = $('<div />').addClass('scalegrid'));

            for (var i = 0; i < 2 * (max_m + max_p); i++)
                grid.append($('<div />').addClass(i%(max_m + max_p) < max_m ? 'st_pre' : (i%(max_m + max_p) >= max_m + 20 ? 'st_post' : '')).css('width', 100/(max_m + max_p) + '%'));
        });

        target.append(NF.row().append($($('<div />').addClass('cell rw-12 padded')).append(
            $('<div />').addClass('note')
                .text("Jeder deiner Kampfwerte reicht von 0 bis 20. Jede dar\u00fcber oder darunter liegende Ver\u00e4nderung wird ignoriert. Denke daran, dass Gegenst\u00e4nde im Kampf zerst\u00f6rt werden k\u00f6nnen, wodurch ihre Effekte sofort entfernt werden.")
                .append($('<br />')).append($('<br />'))
                .append("Das obrige Diagramm zeigt alle positiven (gr\u00fcn) und negativen (rot) Effekte auf deine Kampfwerte. Bereiche au\u00dferhalb der 0-20 - Skala sind grau unterlegt.")
        )));

    };

    var render_equipment = function(data, target) {
        var tmp = {};
        $.each(data, function(gid, group) {
            $.each(group.items, function(uid, item) {
                if (item.equipment) {
                    if (!tmp[item.equipment.name])
                        tmp[item.equipment.name] = {
                            primary: item.equipment.primary_cat,
                            items: []
                        };
                    tmp[item.equipment.name].items.push(item);
                }
            });
        });

        $.each(tmp, function(name, group) {
            var ul;
            target.append(
                NF.row()
                    .append(NF.n('b','',name))
                    .append(NF.row().append(ul = NF.cell(true)))
            );

            var eq; var rd;

            var r = NF.row().appendTo(ul);

            r.append(NF.cell(true, 6, 0, 'flatbox').append(NF.row()
                    .append(NF.n('b','sub',"Ausger\u00fcstet"))
                    .append(eq = NF.cell(true))

            ));

            r.append(NF.cell(true, 6, 0, 'flatbox').append(NF.row()
                    .append(NF.n('b','sub',"Im Inventar"))
                    .append(rd = NF.cell(true))

            ));

            $.each(group.items, function(k, v) {
                var container = core.snippets.item(false, v.name, v.icon, v.static <= 1 ? ((v.weapon && v.weapon.shots !== false) ? v.weapon.shots : v.count) : v.static, v.static > 1, false).addClass('hover');

                var equipped = $.inArray('equipped', $.objToArray(v.flags, true)) >= 0;
                var primary = group.primary && equipped && $.inArray('primary', $.objToArray(v.flags, true)) >= 0;

                if (primary) {
                    eq.prepend(container.addClass('equipped'));
                } else if (equipped) {
                    eq.append(container);
                } else {
                    rd.append(container);
                }

                container.click(function() {
                    core.command('act/inventory', {action: equipped ? 'unequip' : 'equip', items: [v.uin]});
                }).qtt('bottom', function() {
                    $(this)
                        .append(NF.n('b', 'header hold', v.name))
                        .append(render_effect_list(v.rpg))
                        .append(NF.separator());

                    if (group.primary && equipped && !primary)
                        $(this).append(
                            NF.row().append(NF.cell(true).append(NF.n('div', 'btn btn-zv', "Als Standart setzen").click(function() {
                                core.command('act/inventory', {action: 'equip_primary', items: [v.uin]});
                            })))
                        ).append(NF.separator());

                    if (!equipped)
                        $(this).append(NF.row().append(NF.cell().addClass('note').text("Klicke diesen Gegenstand an, um ihn anzulegen.")));
                    else
                        $(this).append(NF.row().append(NF.cell().addClass('note').text("Klicke diesen Gegenstand an, um ihn abzulegen.")));

                });

            });
        });
    };

    core.parts.rpg = function(data, inventory, target) {

        var stats, equip;
        $(target).empty().append(
            $('<div />').addClass('cell rw-4 rw-lg-6 padded').append(
                equip = $('<div />').addClass('flatbox inventory')
            )
        ).append(
            $('<div />').addClass('cell rw-8 rw-lg-6 padded').append(
                stats = $('<div />').addClass('flatbox')
            )
        );

        render_equipment(inventory, equip);
        render_stats(data.stats, stats);

    };
})();(function() {

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
            }, 'time_lock')
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
            }, 'time_lock')
        }
    };

    var fill_battleai = function(target, data) {
        var button;

        var sel_clone = NF.n('select')
            .append(NF.n('option', '', "Hohe Priorit\u00e4t").attr('value','+'))
            .append(NF.n('option', '', "Normale Priorit\u00e4t").attr('value','0'))
            .append(NF.n('option', '', "Geringe Priorit\u00e4t").attr('value','-'));

        target.empty()
            .append($('<h3 />').text("Kampfverhalten"))

            .append(NF.row().attr('title', "Steuert die Priorit\u00e4t, Zombies zu attackieren, die f\u00fcr dich selbst eine Bedrohung darstellen.").qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text("Selbstverteidigung"))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_self').find('option[value="' + data[0] + '"]').attr('selected','selected').end()))
            )

            .append(NF.row().attr('title', "Steuert die Priorit\u00e4t, Zombies zu attackieren, die f\u00fcr deine Kameraden eine Bedrohung darstellen.").qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text("Teamverteidigung"))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_team').find('option[value="' + data[1] + '"]').attr('selected','selected').end()))
            )

            .append(NF.row().attr('title', "Steuert die Priorit\u00e4t, im Kampf zu einer besseren Waffe zu wechseln.").qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text("Waffenauswahl"))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_wpn').find('option[value="' + data[2] + '"]').attr('selected','selected').end()))
            )

            .append(NF.row().attr('title', "Steuert die Priorit\u00e4t, die optimale Angriffsdistanz zu den Zombies f\u00fcr die aktuelle Waffe herzustellen.").qtip(game.render.html.qtip.ingame('top'))
                .append(NF.cell(true, 6, 0, 'right').text("Kampfdistanz"))
                .append(NF.cell(true, 6, 0, 'left').append(sel_clone.clone().attr('id', 'bhav_move').find('option[value="' + data[3] + '"]').attr('selected','selected').end()))
            )

            .append(NF.row().append(NF.cell(false, 6, 6).append(
                button = $('<div />').addClass('btn btn-icon disabled')
                    .append($('<span />').addClass('btn-icon-inner').append($('<i />').addClass('fa fa-check')))
                    .append($('<span />').text("Speichern"))
                    .click(function() {
                        core.command('player/ai', {
                            ai: $('#bhav_self').val() + $('#bhav_team').val() + $('#bhav_wpn').val() + $('#bhav_move').val()
                        }, true, function(ret) {
                            if (!ret.success) {
                                game.render.html.notify('error', "Beim Speichern der Einstellungen ist ein Fehler aufgetreten.");
                                fill_battleai(target, data);
                            } else button.addClass('disabled')
                        })
                    })
            ))).find('select').on('change', function() {button.removeClass('disabled');}).selectric();
    };

    core.parts.settings = function(data, target) {
        var time_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-5 rw-lg-6 rw-md-12 padded').appendTo(target));
        if (data.clock.time_mode == 0) fill_timesettings_stat(data.clock.time_settings, time_settings, data.clock.locked);
        if (data.clock.time_mode == 1) fill_timesettings_var(data.clock.time_settings, time_settings, data.clock.locked);

        var bai_settings = $('<div />').addClass('flatbox').appendTo($('<div />').addClass('cell rw-7 rw-lg-6 rw-md-12 padded').appendTo(target));
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

    core.snippets.countdown_pipe = {};

    core.snippets.countdown = function(initial, callback, pipe) {
        if (callback(core.snippets.timestr(initial), initial) && initial >= 0) {
            var tid = window.setTimeout(function() {core.snippets.countdown(initial - 1, callback, pipe)}, 1000);
            if (pipe) {
                if (core.snippets.countdown_pipe[pipe]) window.clearTimeout(core.snippets.countdown_pipe[pipe]);
                core.snippets.countdown_pipe[pipe] = tid;
            }
        }

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

            if (action.user != '0')
                ext.append(NF.cell(false, 12).html(game.i18n(":other wird diese Aktion durchf\u00fchren!", {':other': '<b>' + core.last.players.others[action.user]['name'] + '</b>'})));

            if (action.remaining != 0) {
                if ($.objToArray(action.requires).length) {

                    ext.append(
                        block = $('<div />').addClass('cell rw-'+g+' padded').append(
                            $('<i />').addClass('separator').text("Erfordert")
                        )
                    );
                    $.each(action.requires, function(k,v) {
                        block.append(
                            $('<div />').addClass('group default').append(
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
                .addClass('btn btn-zv ' + (action.skin ? 'btn-zv-skinned-' + action.skin : '') + (action.user != '0' ? ' btn-icon' : '') )
                .attr('data-target', action.user == '0' ? "Du" : core.last.players.others[action.user]['name'])
                .append(action.user != '0' ? NF.n('span','btn-icon-inner', NF.fa('external-link-square')) : '')
                .append(NF.n('span', '', action.description))
                .click(function (e,arg) {
                    // Hide all QTips
                    $('.qtip').qtip('hide');

                    if (action.popup) {
                        core.popup[action.popup]();
                        return;
                    }

                    if (call && call() === false) return;

                    if (action.escort) {
                        var popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        popup.append($('<h2 />').addClass('center').text(action.description));

                        popup.append(
                            NF.row().append($('<div />').addClass('cell rw-12 padded').text("Bitte w\u00e4hle einen Spieler aus, auf den du diese Aktion anwenden willst. Du kannst nur Spieler ausw\u00e4hlen, die sich am gleichen Ort befinden wie du und Befehle von dir entgegennehmen."))
                        );

                        if (core.last.players.others)
                            $.each(core.last.players.others, function(id, player) {
                                popup.append(NF.row().append($('<div />').addClass('cell rw-12 padded').append(
                                    $('<div />').addClass('btn btn-zv' + ((player.allow === true || player.allow[4]) ? '' : ' disabled')).text(player.name).click(function() {
                                        if (!(player.allow === true || player.allow[4]) || !confirm(game.i18n("Bist du sicher, dass du diese Aktion auf :name anwenden m\u00f6chtest?", {':name': player.name}))) return;

                                        popup.trigger('unpop');
                                        core.command('act/item', {action: action.action, item: action.target, co: player.id, coarg: arg});
                                    })
                                )))
                            });

                        popup.append(NF.row().append($('<div />').addClass('cell rw-12 padded').append(
                            $('<div />').addClass('btn').text("Abbrechen").click(function() {
                                popup.trigger('unpop');
                            }))
                        ));

                    } else core.command('act/item', {action: action.action, item: action.target, coarg: arg, player: action.user});
                });

            if (game.touch()) ext_mode = 'static';

            switch (ext_mode) {
                case 'none':
                    break;
                case 'static':
                    if (ext.children().size()) button.append(ext);
                    break;
                case 'tooltip':case 'nested':
                    if (!action.tooltip && action.remaining < 0 && !ext.children().size()) break;

                    var template = (ext_mode == 'nested') ? game.render.html.qtip.help : game.render.html.qtip.ingame;

                    button.attr('title','-').qtip(template((ext_mode == 'nested') ? {desktop: 'right', lg: 'top'} : 'bottom',{
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

        var active = false;

        button.attr('data-cats', '|' + $.objToArray(blueprint.categories, true).join('|') + '|');

        var rq_all_cache = {};
        var rq_all_check = function(bp) {
            if (rq_all_cache[bp.id] !== undefined)
                return rq_all_cache[bp.id];

            var gb_ok = true;
            $.each(bp.requires, function(k,v) {
                var ok = false;

                $.each(v, function(ki,vi) {if (lib[vi] && lib[vi].build)  ok = true;});
                if (!ok)
                    $.each(v, function(ki,vi) {if (lib[vi] && lib[vi].slot_open && rq_all_check(lib[vi])) ok = true;});

                if (!ok)
                    return gb_ok = false;
            });

            return rq_all_cache[bp.id] = gb_ok;
        };

        var all_rq_ok = rq_all_check(blueprint);

        if (blueprint.build)
            button.addClass('blue');
        else if (blueprint.build_possible && blueprint.slot_open) {
            button.addClass('green');
            active = true;
            $.each(blueprint.material_in, function(k,v) {
                active = active && (v.have >= v.count);
            });
            if (active) {
                button.addClass('active');
                if (typeof callback == "function")
                    button.click(function(e,force) {
                        if (!game.touch() || force)
                        callback();
                    });
            }
        }
        else if (!blueprint.slot_open || !all_rq_ok)
            button.addClass('red');
        else button.addClass('plain');

        if (blueprint.energy || $.objToArray(blueprint.material_in).length)
            mt_in.append($('<i/>').text("Erfordert"));
        if (blueprint.decay_speed || blueprint.repair || blueprint.defense || blueprint.deco || $.objToArray(blueprint.material_out).length)
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

        if (blueprint.deco)
            mt_out.append(
                $('<div />').addClass('group').append(
                    $('<img />').attr('src', 'media/icons/deco_' + (blueprint.deco > 0 ? 'positive' : 'negative') + '.gif')
                ).append(
                    $('<span />').append($('<span />').addClass(blueprint.deco > 0 ? 'green' : 'red').text((blueprint.deco > 0 ? '+' : '') + blueprint.deco))
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

                if (active && game.touch()) {
                    content.append($('<div />').addClass('btn green').text("Bauen").click(function() {
                        button.trigger('click',[true]);
                    }))
                }

                if (blueprint.build)
                    content.append($('<div />').addClass(blueprint.zombies ? 'point failure' : 'point success').text(blueprint.zombies ? "Diese Verteidigungsm\u00f6glichkeit wurde bereits eingesetzt." : "Dieses Projekt wurde bereits gebaut."));
                else if (!blueprint.slot_open)
                    content.append($('<div />').addClass('point failure').text("Du hast bereits ein \u00e4hnliches Projekt gebaut."));
                else if (!all_rq_ok)
                        content.append($('<div />').addClass('point failure').text("Mindestens eine Vorraussetzung f\u00fcr dieses Projekt kann nicht gebaut werden."));
                else {

                    if (blueprint.steps_max > 1)
                        content.append($('<span />').text(game.i18n(blueprint.zombies ? "Hiermit kannst du :num mal Zombies angreifen." : "Du kannst dieses Projekt :num mal bauen.",{':num': blueprint.steps_max}))).append('<span class="separator" />');
                    else if (blueprint.steps_max == 0 && !blueprint.zombies)
                        content.append($('<span />').text("Dieses Projekt kann unbegrenzt oft gebaut werden.")).append('<span class="separator" />');

                    content.append($('<span />').text("Vorraussetzungen"));

                    var chk_rq = false;
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
                        chk_rq = true;
                    });

                    if (!chk_rq) content.append($('<div />').addClass('point success').text("Keine besonderen Vorraussetzungen"));

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
        var sum_absolute_p = absolute_p.reduce(fsum,0), sum_absolute_n = absolute_n.reduce(fsum,0), sum_relative_p = Math.max(0,1+relative_p.reduce(fsum,0)), sum_relative_n = Math.max(0, 1+relative_n.reduce(fsum,0));
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
        var bar = $('<div />').addClass('cell rw-' + (inline ? 12 : 4)).append(
            $('<div />').addClass('bar').append(
                $('<img />').attr('src', 'media/icons/status_' + num_decode_str(type) + '.gif')
            ).append(
                $('<div />').addClass('background').append(
                    $('<div />').css({width: value + '%', background: num_decode_color(type)})
                ).append($('<div />').addClass('label').text(Math.round(value * 10)/10))
            )
        );

        if (!inline) bar.attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
                render: function (event, api) {
                    var content = $(this).find('.qtip-content').empty().append(
                        $('<b />').addClass('header').text(num_decode_title(type))
                    ).append(
                        $('<div />').addClass('info center').text(game.i18n("Aktueller Wert: :num", {':num': value}))
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
                $('<img />').addClass('status').attr('src','media/icons/status_' + num_decode_str(v) + '.gif').attr('title','-').qtip(game.render.html.qtip.ingame('bottom', {
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
                                ).append($('<div />').text(game.i18n("Verbleibende Dauer: :time", {':time': v.time})));
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
                countdown.html('<span class="hide-sm">' + "Weiter" + '</span><span class="hide-md hide-lg hide-desktop"><i class="fa fa-angle-double-right"><i></span>');
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
                    $('<div />').addClass(i ==  (scm.length -1) ? 'hide-md hide-sm' : '').text(scm[i] < 10 ? '0' + scm[i] : scm[i]).appendTo(countdown);

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
