(function() {
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
                            $('<i />').addClass('separator').text(<?=__j('Erfordert')?>)
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
                .addClass('btn btn-zv ' + (action.skin ? 'btn-zv-skinned-' + action.skin : '') )
                .append(
                    $('<span />').text(action.description)
                ).click(function () {
                    if (call && call() === false) return;
                    eval(action.javascript);
                });

            switch (ext_mode) {
                case 'static':
                    button.append(ext);
                    break;
                case 'tooltip':
                    if (!action.tooltip && action.remaining < 0 && !ext.children().size()) break;

                    button.attr('title','-').qtip(game.render.html.qtip.ingame('bottom',{
                        render: function(event,api) {
                            var content = $(this).find('.qtip-content').empty().append(
                                $('<b />').addClass('header').text(action.description)
                            );

                            if (action.tooltip)
                                content.append($('<span />').text(action.tooltip)).append('<span class="separator" />');
                            if (ext.children().size())
                                content.append(ext).append('<span class="separator" />');
                            if (action.remaining >= 0)
                                content.append($('<div />').addClass('note').text(game.i18n(<?=__j('Du kannst diese Aktion noch :num mal einsetzen.')?>, {':num': action.remaining})));
                        }})
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
        ).append($('<span />').text(<?=__j('Wird geladen ...');?>))
    };

    core.snippets.blueprint = function(blueprint, energy, lib, callback) {
        var button = $('<div />').addClass('blueprint').attr('title','-').attr('data-bid', blueprint.id);
        var ext = $('<div />').addClass('row details');

        var mt_in = $('<div />').addClass('cell rw-12').appendTo(ext);
        var mt_out = $('<div />').addClass('cell rw-12').appendTo(ext);

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
            mt_in.append($('<i/>').text(<?=__j('Erfordert')?>));
        if (blueprint.decay_speed || blueprint.repair || blueprint.defense || $.objToArray(blueprint.material_out).length)
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
            render: function(event,api) {
                var content = $(this).find('.qtip-content').empty().append(
                    $('<b />').addClass('header').text(blueprint.name)
                );

                if (blueprint.description)
                    content.append($('<span />').text(blueprint.description)).append('<span class="separator" />');

                if (blueprint.build)
                    content.append($('<div />').addClass('point success').text(<?=__j('Dieses Projekt wurde bereits gebaut.')?>));
                else if (!blueprint.slot_open) {
                    content.append($('<div />').addClass('point failure').text(<?=__j('Du hast bereits ein ähnliches Projekt gebaut.')?>));
                } else {

                    if (blueprint.steps_max > 1)
                        content.append($('<span />').text(game.i18n(<?=__j('Du kannst dieses Projekt :num mal bauen.')?>,{':num': blueprint.steps_max}))).append('<span class="separator" />');
                    else if (blueprint.steps_max == 0)
                        content.append($('<span />').text(<?=__j('Dieses Projekt kann unbegrenzt oft gebaut werden.')?>)).append('<span class="separator" />');

                    content.append($('<span />').text(<?=__j('Vorraussetzungen')?>));

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
                        else content.append($('<div />').addClass('point success').text(<?=__j('Keine besonderen Vorraussetzungen')?>));
                    });

                    content.append('<span class="separator" />');

                    cache = [];
                    $.each(lib,function(key,bp) {
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
                    $.each(blueprint.occupies, function(k,occ) {
                        $.each(lib, function(key, bp) {
                            if (bp.id != blueprint.id && !bp.hidden && $.inArray(bp.id, cache) < 0 && $.inArray(occ, $.objToArray(bp.occupies, true)) >= 0)
                                cache.push(bp.id)
                        });
                    });

                    if (cache.length) {
                        content.append($('<span />').text(<?=__j('Verhindert')?>));
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
})();