<?php if (Kohana::$environment === Kohana::DEVELOPMENT) { ?>
(function() {
    core.parts.admin = {};

    core.parts.admin.loader = function(target, path, callback) {
        target.empty().append(core.snippets.wait());
        game.network.query(path, {}, function(data) {
            if (data.error) {
                core.parts.admin.controls(target.empty());
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            }
            else
                callback(target.empty().append($('<div />').addClass('btn small').text('Menü').click(function() {
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

        $('<div />').addClass('btn small').text('Skip ahead...').click(function() {
            var in_w, in_d, in_h, in_m;

            core.popup.spawn(300, 'auto')
                .append($('<div />').addClass('row')
                    .append($('<div />').addClass('cell rw-6 padded').text('Weeks (W)'))
                    .append($('<div />').addClass('cell rw-6 padded').append(in_w = $('<input />').addClass('form_input').attr('type','text').val('0')))
                ).append($('<div />').addClass('row')
                    .append($('<div />').addClass('cell rw-6 padded').text('Days (D)'))
                    .append($('<div />').addClass('cell rw-6 padded').append(in_d = $('<input />').addClass('form_input').attr('type','text').val('0')))
                ).append($('<div />').addClass('row')
                    .append($('<div />').addClass('cell rw-6 padded').text('Hours (H)'))
                    .append($('<div />').addClass('cell rw-6 padded').append(in_h = $('<input />').addClass('form_input').attr('type','text').val('0')))
                ).append($('<div />').addClass('row')
                    .append($('<div />').addClass('cell rw-6 padded').text('Minutes (M)'))
                    .append($('<div />').addClass('cell rw-6 padded').append(in_m = $('<input />').addClass('form_input').attr('type','text').val('5')))
                .append($('<div />').addClass('row')
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
<?php } else { ?>
(function() {
    core.parts.admin = false;
})();
<?php } ?>