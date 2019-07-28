(function() {
    core.snippets.timestr = function(i) {
        var cache = [[<?=__j('Woche')?>,<?=__j('Wochen')?>],[<?=__j('Tag')?>,<?=__j('Tage')?>],[<?=__j('Stunde')?>,<?=__j('Stunden')?>],[<?=__j('Minute')?>, <?=__j('Minuten')?>],[<?=__j('Sekunde')?>, <?=__j('Sekunden')?>]];
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
            return split.join(', ') + " " + <?=__j('und')?> + " " + tmp[0];
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
    core.snippets.button = function(action, call, ext_mode, custom_popup_handler) {
        if (typeof action === "string")
            return $('<div />').addClass('btn').addClass(ext_mode === true ? 'btn-zv' : '').click(call).append(NF.n('span', '', action));
        else {

            ext_mode = ext_mode || 'extend';

            var button = $('<div />');
            var ext = $('<div />').addClass('row consequence');

            var block;

            var g = ext_mode == 'tooltip' ? 12 : 6;

            if (action.user != '0')
                ext.append(NF.cell(false, 12).html(game.i18n(<?=__j(':other wird diese Aktion durchführen!')?>, {':other': '<b>' + core.last.players.others[action.user]['name'] + '</b>'})));

            if (action.remaining != 0) {
                if ($.objToArray(action.requires).length) {

                    ext.append(
                        block = $('<div />').addClass('cell rw-'+g+' padded').append(
                            $('<i />').addClass('separator').text(<?=__j('Erfordert')?>)
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
                            $('<i />').addClass('separator').text(<?=__j('Effekte')?>)
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
                            $('<i />').addClass('separator').text(<?=__j('Effekte auf ausgewählten Spieler')?>)
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
                .attr('data-target', action.user == '0' ? <?=__j('Du')?> : core.last.players.others[action.user]['name'])
                .append(action.user != '0' ? NF.n('span','btn-icon-inner', NF.fa('external-link-square')) : '')
                .append(NF.n('span', '', action.description))
                .click(function (e,arg) {
                    // Hide all QTips
                    $('.qtip').qtip('hide');

                    if (action.popup) {
                        if (custom_popup_handler)
                            custom_popup_handler(action.popup);
                        else core.popup[action.popup]();
                        return;
                    }

                    if (call && call() === false) return;

                    if (action.escort) {
                        var popup = core.popup.spawn({desktop: 400, sm: '100%'});

                        popup.append($('<h2 />').addClass('center').text(action.description));

                        popup.append(
                            NF.row().append($('<div />').addClass('cell rw-12 padded').text(<?=__j('Bitte wähle einen Spieler aus, auf den du diese Aktion anwenden willst. Du kannst nur Spieler auswählen, die sich am gleichen Ort befinden wie du und Befehle von dir entgegennehmen.')?>))
                        );

                        if (core.last.players.others)
                            $.each(core.last.players.others, function(id, player) {
                                popup.append(NF.row().append($('<div />').addClass('cell rw-12 padded').append(
                                    $('<div />').addClass('btn btn-zv' + (player.local && (player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE?>]) ? '' : ' disabled')).text(player.name).click(function() {
                                        if (!(player.allow === true || player.allow[<?=Interface_Plentity::IC_ALLOW_ITEMS_SIDEUSE?>]) || !confirm(game.i18n(<?=__j('Bist du sicher, dass du diese Aktion auf :name anwenden möchtest?')?>, {':name': player.name}))) return;

                                        popup.trigger('unpop');
                                        core.command('act/item', {action: action.action, item: action.target, co: player.id, coarg: arg});
                                    })
                                )))
                            });

                        popup.append(NF.row().append($('<div />').addClass('cell rw-12 padded').append(
                            $('<div />').addClass('btn').text(<?=__j('Abbrechen')?>).click(function() {
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
                    if (ext.children().length) button.append(ext);
                    break;
                case 'tooltip':case 'nested':
                    if (!action.tooltip && action.remaining < 0 && !ext.children().length) break;

                    var template = (ext_mode == 'nested') ? game.render.html.qtip.help : game.render.html.qtip.ingame;

                    button.attr('title','-').qtip(template((ext_mode == 'nested') ? {desktop: 'right', lg: 'top'} : 'bottom',{
                        render: function(event,api) {
                            $(this).css('width',$(this).css('max-width'));

                            if (action.skin && action.skin.search('action-drunk') >= 0) $(this).css('filter','blur(3px)');

                            var content = $(this).find('.qtip-content').empty().append(
                                (ext_mode == 'nested') ? null : $('<b />').addClass('header').text(action.description)
                            );

                            if (action.tooltip)
                                content.append($('<span />').text(action.tooltip)).append('<span class="separator" />');
                            if (ext.children().length)
                                content.append(ext).append('<span class="separator" />');
                            if (action.remaining >= 0)
                                content.append($('<div />').addClass('note').text(game.i18n(<?=__j('Du kannst diese Aktion noch :num mal einsetzen.')?>, {':num': action.remaining})));
                        }},true)
                    );
                    break;
                case 'extend':default:
                    button.append(ext.hide()).mouseenter(function() {
                        if (ext.children().length) ext.stop().slideDown('fast');
                    }).mouseleave(function() {
                        if (ext.children().length) ext.stop().slideUp('slow');
                    });
                    break;
            }

            return button;
        }
    };

    core.snippets.wait = function() {
        return $('<div />').addClass('center').append(
            $('<i/>').addClass('fa fa-circle-o-notch fa-spin')
        ).append($('<span />').text(<?=__j('Wird geladen ...');?>))
    };

    /**
     *
     * @param {Blueprint} blueprint
     * @param {int} energy
     * @param {int} zombies
     * @param {Blueprint[]} lib
     * @param {Function} callback
     * @param {Object} viewport
     * @returns {*}
     */
    core.snippets.blueprint = function(blueprint, energy, zombies, lib, callback, viewport) {
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
            //if (bp.hidden && !bp.build) return false;

            var gb_ok = true;
            $.each(bp.requires, function(k,v) {
                var ok = false;

                $.each(v, function(ki,vi) {if (lib[vi] && lib[vi].build)  ok = true;});
                if (!ok)
                    $.each(v, function(ki,vi) {if (lib[vi] && lib[vi].slot_open && rq_all_check(lib[vi])) ok = true;});

                if (!ok)
                    return gb_ok = false;
            });

            if (gb_ok) $.each(bp.requires_local, function(k,v) {
                var ok = false;

                $.each(v, function(ki,vi) {if (lib[vi] && lib[vi].build_local) ok = true;});
                if (!ok)
                    $.each(v, function(ki,vi) {if (lib[vi] && lib[vi].slot_open && rq_all_check(lib[vi])) ok = true;});

                if (!ok)
                    return gb_ok = false;
            });

            if (gb_ok) $.each(bp.requires_room, function(k,v) {
                var ok = v;
                if (!ok)
                    ok = (lib[k] && lib[k].slot_open && rq_all_check(lib[k]))

                if (!ok) return gb_ok = false;
            });

            return rq_all_cache[bp.id] = gb_ok;
        };

        var all_rq_ok = rq_all_check(blueprint);
        var all_tg_ok = true;
        $.each(blueprint.requires_tag, function(k,v) {
            if (!v.b) all_tg_ok = false;
        })

        if (blueprint.build && blueprint.build_local)
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
        else if (!blueprint.slot_open || !all_rq_ok || !all_tg_ok)
            button.addClass('red');
        else button.addClass('plain');

        if (blueprint.energy || $.objToArray(blueprint.material_in).length)
            mt_in.append($('<i/>').text(<?=__j('Erfordert')?>));
        if (blueprint.decay_speed || blueprint.repair || blueprint.defense || blueprint.deco || $.objToArray(blueprint.material_out).length)
            mt_out.append($('<i/>').text(<?=__j('Produziert')?>));

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
            mt_zmb.append($('<i/>').text(<?=__j('Tötet')?>)).append(
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

        if (mt_in.children('.group').length == 1 && mt_out.children('.group').length == 1) {
            mt_in.removeClass('rw-12').addClass('rw-6');
            mt_out.removeClass('rw-12').addClass('rw-6');
        }

        button.qtip(game.render.html.qtip.ingame('top',{
            render: function() {
                var content = $(this).find('.qtip-content').empty().append(
                    $('<b />').addClass('header').text(blueprint.name)
                );

                if (blueprint.description)
                    content.append($('<span />').text(blueprint.description)).append('<span class="separator" />');

                if (active && game.touch()) {
                    content.append($('<div />').addClass('btn green').text(<?=__j('Bauen')?>).click(function() {
                        button.trigger('click',[true]);
                    }))
                }

                if (!blueprint.build_local && blueprint.build && !blueprint.globally_blocked)
                    content
                        .append($('<div />').addClass('info').text(<?=__j('Du hast dieses Projekt bereits in einem anderen Raum gebaut.')?>))
                        .append('<span class="separator" />');

                if (blueprint.build_local || (blueprint.build && blueprint.globally_blocked))
                    content.append($('<div />').addClass(blueprint.zombies ? 'point failure' : 'point success').text(blueprint.zombies ? <?=__j('Diese Verteidigungsmöglichkeit wurde bereits eingesetzt.')?> : <?=__j('Dieses Projekt wurde bereits gebaut.')?>));
                else if (!blueprint.slot_open)
                    content.append($('<div />').addClass('point failure').text(<?=__j('Du hast bereits ein ähnliches Projekt gebaut.')?>));
                else if (!all_rq_ok)
                        content.append($('<div />').addClass('point failure').text(<?=__j('Mindestens eine Vorraussetzung für dieses Projekt kann nicht gebaut werden.')?>));
                else {

                    if (blueprint.steps_max > 1)
                        content.append($('<span />').text(game.i18n(blueprint.zombies ? <?=__j('Hiermit kannst du :num mal Zombies angreifen.')?> : <?=__j('Du kannst dieses Projekt :num mal bauen.')?>,{':num': blueprint.steps_max}))).append('<span class="separator" />');
                    else if (blueprint.steps_max == 0 && !blueprint.zombies)
                        content.append($('<span />').text(<?=__j('Dieses Projekt kann unbegrenzt oft gebaut werden.')?>)).append('<span class="separator" />');

                    content.append($('<span />').text(<?=__j('Raum')?>));

                    if (blueprint.space > 0) {
                        content.append($('<div />').addClass('point').addClass(blueprint.space_open ? 'success' : 'failure').text(<?=__j('Freier Platz')?> + ': ' + blueprint.space + 'm²'));
                        chk_rm = true;
                    }

                    $.each(blueprint.requires_tag, function(k,v) {
                        content.append($('<div />').addClass('point').addClass(v.b ? 'success' : 'failure').text(game.i18n(<?=__j('Raumtyp: :type')?>, {':type': v.name})));
                        chk_rm = true;
                    });

                    var chk_rm = false;
                    $.each(blueprint.requires_room, function(k,v) {
                        content.append($('<div />').addClass('point').addClass(v ? 'success' : 'failure').text(lib[k].name));
                        chk_rm = true;
                    });

                    $.each(blueprint.occupies_room, function(k,v) {
                        if (k != blueprint.id) {
                            content.append($('<div />').addClass('point').addClass(v ? 'success' : 'failure').text(game.i18n(<?=__j('Kein Upgrade auf :name')?>, {':name': lib[k].name})));
                            chk_rm = true;
                        }
                    });

                    if (!chk_rm) content.append($('<div />').addClass('point success').text(<?=__j('Keine besonderen Vorraussetzungen')?>));

                    content.append($('<span />').text(<?=__j('Vorraussetzungen')?>));

                    var rq_sum = [];
                    $.each(blueprint.requires, function(k,v) {rq_sum.push([false,v])});
                    $.each(blueprint.requires_local, function(k,v) {rq_sum.push([true,v])});

                    var chk_rq = false;
                    $.each(rq_sum, function(k,v_pre) {
                        var local = v_pre[0];
                        var v = v_pre[1];

                        var cache = [];
                        var ok = false;
                        $.each(v, function(ki,vi) {
                            if (lib[vi]) {
                                cache.push(lib[vi].name);
                                if ((lib[vi].build && !local) || (lib[vi].build_local && local)) ok = true;
                            }
                        });
                        if (cache.length)
                            content.append($('<div />').addClass('point').addClass(ok ? 'success' : 'failure').text(cache.join(', ') + (local ? (' (' + <?=__j('in diesem Raum')?> + ')') : '')));

                        chk_rq = true;
                    });

                    if (!chk_rq) content.append($('<div />').addClass('point success').text(<?=__j('Keine besonderen Vorraussetzungen')?>));

                    content.append('<span class="separator" />');

                    var cache = [];
                    if (blueprint.is_room)
                        $.each(lib,function(key,bp) {
                            $.each(bp.requires_room,function(vi,v) {
                                if (vi == blueprint.id)
                                    cache.push(bp.name);
                            });
                        });
                    else
                        $.each(lib,function(key,bp) {
                            $.each(bp.requires_local,function(k,req) {
                                $.each(req,function(k,vi) {
                                    if (vi == blueprint.id)
                                        cache.push(bp.name + " (" + <?=__j('in diesem Raum')?> + ")");
                                });
                            });
                            $.each(bp.requires,function(k,req) {
                                $.each(req,function(k,vi) {
                                    if (vi == blueprint.id)
                                        cache.push(bp.name);
                                });
                            });
                        });

                    if (cache.length) {
                        content.append($('<span />').text(<?=__j('Ermöglicht')?>));
                        $.each(cache, function(k,v) {
                            content.append($('<div />').addClass('point').text(v));
                        });
                        content.append('<span class="separator" />');
                    }

                    cache = [];
                    if (blueprint.is_room)
                        $.each(blueprint.occupies_room, function(occ,v) {
                            $.each(lib, function(key, bp) {
                                if (bp.is_room && bp.id != blueprint.id && !bp.hidden && $.inArray(bp.id, cache) < 0 && $.inArray(occ, $.objToArray(bp.occupies_room, false)) >= 0)
                                    cache.push(bp.name + " (" + <?=__j('in diesem Raum')?> + ")");
                            });
                        });
                    else
                        $.each(blueprint.occupies, function(k,occ) {
                            $.each(lib, function(key, bp) {
                                if (bp.id != blueprint.id && !bp.hidden && $.inArray(bp.id, cache) < 0 && $.inArray(occ, $.objToArray(bp.occupies, true)) >= 0)
                                    cache.push(bp.name + (bp.globally_blocked ? '' : (" (" + <?=__j('in diesem Raum')?> + ")")))
                            });
                        });

                    if (cache.length || blueprint.globally_blocked) {
                        content.append($('<span />').text(<?=__j('Verhindert')?>));
                        if (blueprint.globally_blocked)
                            content.append($('<div />').addClass('point').text(blueprint.name + " (" + <?=__j('in anderen Räumen')?> + ")"));
                        $.each(cache, function(k,v) {
                            content.append($('<div />').addClass('point').text(v + (!blueprint.globally_blocked ? '' : (" (" + <?=__j('an diesem Ort')?> + ")"))));
                        })
                    }
                }
            }}, viewport)
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
})();