(function() {

    var render_effect_list = function(f, mark) {
        var tmp = Ω.row('center');
        $.each(f, function(id, v) {
            switch (parseInt(id)) {
                case NaN: break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_INI?>: id = 'ini'; break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_ATK?>: id = 'atk'; break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_DEF?>: id = 'def'; break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_ACC?>: id = 'acc'; break;
            }

            tmp.append(Ω.cell(true, 3).append($('<div />').addClass('rpg stat').addClass(v > 0 ? 'plus' : (v == 0 ? 'null' : 'minus')).addClass(id).text(v).css('opacity', (!mark || mark == id) ? 1 : 0.75)));
        });
        return tmp;
    };

    var render_stats = function(data, target) {
        var max_p = 0;
        var max_m = 0;

        $.each(data, function(id, block) {
            var row = Ω.row().appendTo(target);

            var bar_p;
            var bar_m;
            row.append(Ω.cell(true, {desktop: 10, md: 8, sm: 6}).append($('<div />').addClass('rpg statbar').append(bar_p = $('<div />').addClass('rpg barcontainer plus')).append( bar_m = $('<div />').addClass('rpg barcontainer minus'))));

            var t = 'unk';

            switch (parseInt(id)) {
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_INI?>: t = 'ini'; break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_ATK?>: t = 'atk'; break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_DEF?>: t = 'def'; break;
                case <?=Model_Items_Abstract_Equipable::MIAE_STAT_ACC?>: t = 'acc'; break;
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
                                .append(Ω.n('b','header',<?=__j('Menschlichkeit')?>))
                                .append(Ω.n('p','',<?=__j('Als Mensch bist du den meisten Zombies körperlich zumindest ein wenig überlegen.')?>))
                                .append(Ω.separator())
                                .append(render_effect_list(elem.all, t))
                        });
                        break;
                    case <?=Model_Items_Abstract_Equipable::MIAE_ARMOR_BODY?>:
                    case <?=Model_Items_Abstract_Equipable::MIAE_ARMOR_HELMET?>:
                    case <?=Model_Items_Abstract_Equipable::MIAE_ARMOR_SHIELD?>:
                    case <?=Model_Items_Abstract_Equipable::MIAE_ARMOR_CAPE?>:
                        b.append($('<img />').attr('src','media/icons/' + elem.icon + '.gif')).qtt('top', function() {
                            $(this)
                                .append(Ω.n('b','header',elem.name))
                                .append(Ω.n('p','',<?=__j('Dieser Ausrüstungsgegenstand beeinflusst deine Kampfwerte. Lege ihn ab, um diese Effekte zu beenden.')?>))
                                .append(Ω.separator())
                                .append(render_effect_list(elem.all, t))
                        });
                        break;
                    case <?=Model_Items_Abstract_Equipable::MIAE_WEAPON?>:
                        b.append($('<img />').attr('src','media/icons/' + elem.icon + '.gif')).qtt('top', function() {
                            $(this)
                                .append(Ω.n('b','header',elem.name))
                                .append(Ω.n('p','',<?=__j('Diese Waffe beeinflusst deine Kampfwerte. Im Gegensatz zu Rüstungsgegenständen kommt dieser Einfluss jedoch nur zum Tragen, wenn die Waffe tatsächlich im Kampf verwendet wird.')?>))
                                .append(Ω.separator())
                                .append(render_effect_list(elem.all, t))
                        });
                        break;
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

        target.append(Ω.row().append($($('<div />').addClass('cell rw-12 padded')).append(
            $('<div />').addClass('note')
                .text(<?=__j('Jeder deiner Kampfwerte reicht von 0 bis 20. Jede darüber oder darunter liegende Veränderung wird ignoriert. Denke daran, dass Gegenstände im Kampf zerstört werden können, wodurch ihre Effekte sofort entfernt werden.')?>)
                .append($('<br />')).append($('<br />'))
                .append(<?=__j('Das obrige Diagramm zeigt alle positiven (grün) und negativen (rot) Effekte auf deine Kampfwerte. Bereiche außerhalb der 0-20 - Skala sind grau unterlegt.')?>)
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
                Ω.row()
                    .append(Ω.n('b','',name))
                    .append(Ω.row().append(ul = Ω.cell(true)))
            );

            var eq; var rd;

            var r = Ω.row().appendTo(ul);

            r.append(Ω.cell(true, 6, 0, 'flatbox').append(Ω.row()
                    .append(Ω.n('b','sub',<?=__j('Ausgerüstet')?>))
                    .append(eq = Ω.cell(true))

            ));

            r.append(Ω.cell(true, 6, 0, 'flatbox').append(Ω.row()
                    .append(Ω.n('b','sub',<?=__j('Im Inventar')?>))
                    .append(rd = Ω.cell(true))

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
                        .append(Ω.n('b', 'header', v.name))
                        .append(render_effect_list(v.rpg))
                        .append(Ω.separator());

                    if (group.primary && equipped && !primary)
                        $(this).append(
                            Ω.row().append(Ω.cell(true).append(Ω.n('div', 'btn btn-zv', <?=__j('Als Standart setzen')?>).click(function() {
                                core.command('act/inventory', {action: 'equip_primary', items: [v.uin]});
                            })))
                        ).append(Ω.separator());

                    if (!equipped)
                        $(this).append(Ω.row().append(Ω.cell().addClass('note').text(<?=__j('Klicke diesen Gegenstand an, um ihn anzulegen.')?>)));
                    else
                        $(this).append(Ω.row().append(Ω.cell().addClass('note').text(<?=__j('Klicke diesen Gegenstand an, um ihn abzulegen.')?>)));

                });

            });
        });

        console.log(tmp);
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
})();