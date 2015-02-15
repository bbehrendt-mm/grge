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