(function() {
    var renderers = {};

    renderers[<?=Model_Log_Message::MLM_PRERENDERED_STRING?>] =
        function(data) {
            return data.title
                ? $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<div />').addClass('sub').text(data.body))
                : $('<div />').append($('<div />').text(data.body))

        };

    renderers[<?=Model_Log_Message::MLM_MOVEMENT_EVENT?>] =
        function(data) {
            var txt;
            switch (data['class']) {
                case <?=Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER?>:
                    txt = data.self ? <?=__j('Du hast diesen Ort betreten.')?> : game.i18n(<?=__j(':name hat diesen Ort betreten.')?>, {':name': data.name});
                    break;
                case <?=Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE?>:
                    txt = data.self ? <?=__j('Du hast diesen Ort verlassen.')?> : game.i18n(<?=__j(':name hat diesen Ort verlassen.')?>, {':name': data.name});
                    break;
                case <?=Model_Log_Types_Movement::MOVEMENT_TYPE_PASS?>:
                    txt = data.self ? <?=__j('Du hast diesen Ort auf deinem Weg passiert.')?> : game.i18n(<?=__j(':name hat diesen Ort auf seinem Weg passiert.')?>, {':name': data.name});
                    break;
            }

            return $('<div />').text(txt);
        };

    renderers[<?=Model_Log_Message::MLM_ITEM_LOG?>] =
        function(data) {
            var header;
            switch (data['class']) {
                case <?=Model_Log_Types_Item::MLTI_DIGUP?>:
                    header = <?=__j(':itemdef gefunden!')?>;
                    break;
                case <?=Model_Log_Types_Item::MLTI_EAGLE?>:
                    header = <?=__j(':itemdef entdeckt!')?>;
                    break;
                case <?=Model_Log_Types_Item::MLTI_DEATH?>:
                    header = <?=__j(':name ist von uns gegangen...')?>;
                    break;
                case <?=Model_Log_Types_Item::MLTI_ZOMBIFY?>:
                    header = <?=__j(':name ist gestorben und hat sich in einen Zombie verwandelt!')?>;
                    break;
                case <?=Model_Log_Types_Item::MLTI_GHULKILL?>:
                    header = <?=__j(':name hat nun endlich seinen ewigen Frieden gefunden...')?>;
                    break;
                case <?=Model_Log_Types_Item::MLTI_SOUL?>:
                    header = <?=__j(':itemdef angelockt!')?>;
                    break;
                case <?=Model_Log_Types_Item::MLTI_VENDING?>:
                    header = <?=__j(':itemdef erworben!')?>;
                    break;
                default:
                    header = <?=__j(':itemdef erhalten!')?>;
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
                    case <?=Model_Log_Types_Item::MLTI_DEATH?>:
                        sub.append($('<p />').text(game.i18n(data.self ? <?=__j('Du hast soeben deinen letzten Atemzug getan und deiner Gemeinschaft das wenige, was du hattest, hinterlassen. Das wars dann wohl...')?> : <?=__j('Heute ist ein trauriger Tag für eure kleine Gemeinschaft, denn sie ist soeben wieder etwas geschrumpft. Nur einige sterbliche Überreste sind noch zurück geblieben...')?>,{':name': data.primary})));
                        break;
                    case <?=Model_Log_Types_Item::MLTI_ZOMBIFY?>:
                        sub.append($('<p />').text(game.i18n(data.self ? <?=__j('Du hast dich soeben in einen Zombie verwandelt!')?> : <?=__j('Heute ist ein trauriger Tag für eure kleine Gemeinschaft, denn sie ist soeben wieder etwas geschrumpft. Die Zombiehorden hingegen haben Zuwachs zu verzeichnen...')?>,{':name': data.primary})));
                        break;
                    case <?=Model_Log_Types_Item::MLTI_GHULKILL?>:
                        sub.append($('<p />').text(game.i18n(data.self ? <?=__j('Deine Freunde haben dir endlich den ewigen Frieden geschenkt.')?> : <?=__j('Es ist immer schwer, jemandem den man gekannt hat den Gnadenstoß zu geben. Nur einige sterbliche Überreste sind noch zurück geblieben...')?>,{':name': data.primary})));
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

    renderers[<?=Model_Log_Message::MLM_CHEM_EXPERIMENT?>] =
        function(data) {
            var txt;
            if (data.results)
                txt = data.self ? <?=__j('Du hast :item mit :chem kombiniert, und dabei :list erhalten.')?> : <?=__j(':name hat :item mit :chem kombiniert, und dabei :list erhalten.')?>;
            else
                txt = data.self ? <?=__j('Du hast erfolglos :item mit :chem kombiniert...')?> : <?=__j(':name hat erfolglos :item mit :chem kombiniert...')?>;

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

    renderers[<?=Model_Log_Message::MLM_LOCATION_LOG?>] =
        function(data) {
            var txt = <?=__j(':building aufgedeckt!')?>;

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

    renderers[<?=Model_Log_Message::MLM_BATTLE_CONTAINER?>] =
        function(data) {
            var details = $('<div />').addClass('sub');
            var even = false;

            var summary = {};
            $.each(data.battle, function(round, obj) {
                if (obj.type == <?=Model_Log_Message::MLM_BATTLE_ENTER?> && !obj.zombie) {
                    summary[obj.name] = {
                        killed: false, damage_received: 0, damage_dealt: 0, kills: 0, items_lost: {}, energy_lost: 0, injuries: {}
                    }; return;
                }

                if (obj.type == <?=Model_Log_Message::MLM_BATTLE_DEATH?> && !obj.zombie) {
                    summary[obj.name].killed = true;
                    return;
                }

                if (obj.type == <?=Model_Log_Message::MLM_BATTLE_INJURY?>) {
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

                if (obj.type == <?=Model_Log_Message::MLM_BATTLE_ATTACK?>) {
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
                            add_lost_item(obj.attacker.name, <?=__j('Munition')?>,icon);
                        });
                    }
                }
            });

            $.each(summary, function(player, data) {
                var row = $('<div />').addClass('row log-battle-summary').appendTo(details);
                $('<div />').appendTo(row).addClass('cell rw-3 player').addClass(data.killed ? 'killed' : '').text(player);
                var stuff = $('<div />').appendTo(row).addClass('cell rw-3 stuff');
                var injuries = $('<div />').appendTo(row).addClass('cell rw-2 injuries');
                var items = $('<div />').appendTo(row).addClass('cell rw-4 items_lost');


                $('<span />').appendTo(stuff).addClass('damage_received').text(data.damage_received);
                $('<span />').appendTo(stuff).addClass('energy_lost').text(data.energy_lost);
                $('<span />').appendTo(stuff).addClass('damage_dealt').text(data.damage_dealt);
                $('<span />').appendTo(stuff).addClass('zombies_killed').text(data.kills);

                $.each(data.items_lost, function(k,item) {
                    items.append(core.snippets.item(<?=__j('Dieser Gegenstand wurde während des Kampfes zerstört.')?>,item.name,item.icon,item.count,true,false))
                });

                $.each(data.injuries, function(k,item) {
                    var s = $('<span />').appendTo(injuries).attr('title', item.name).qtip(game.render.html.qtip.ingame('top'));
                    if (item.count > 1) s.append($('<span />').text(item.count + 'x'));
                    s.append($('<img />').attr('src', 'media/icons/' + item.icon + '.gif'));
                });
            });
            $.each(data.battle, function(round, obj) {
                var row = $('<div />').addClass('row').appendTo(details);

                var txt;
                if (obj.type == <?=Model_Log_Message::MLM_BATTLE_ENTER?>) {
                    if (!obj.zombie) txt = game.i18n(<?=__j(':name tritt dem Kampfgeschehen bei!');?>, {':name': obj.name});
                    else {
                        var d;
                        if		(obj.distance < 5)	d = <?=__j('in einer dunklen Ecke');?>;
                        else if	(obj.distance < 10)	d = <?=__j('in unmittelbarer Nähe');?>;
                        else if	(obj.distance < 25)	d = <?=__j('in der Umgebung');?>;
                        else if	(obj.distance < 50)	d = <?=__j('in einiger Entfernung');?>;
                        else if	(obj.distance < 75)	d = <?=__j('weit entfernt');?>;
                        else						d = <?=__j('am Horizont');?>;
                        txt = game.i18n(obj.ren ? <?=__j(':zombies erscheint :distance!');?> : <?=__j(':zombies tauchen :distance auf.');?>, {':zombies': obj.ren ? obj.name : (obj.count + ' ' + obj.name), ':distance': d});
                    }

                    row.addClass('log-battle-enter').addClass(obj.zombie ? 'log-battle-enter-zombie' : 'log-battle-enter-citizen');
                    row.text(txt);
                }

                else if (obj.type == <?=Model_Log_Message::MLM_BATTLE_ESCAPE?>) {
                    row.addClass('log-battle-escape');
                    if (obj.v == -1) row.text(<?=__j('Es gibt kein Entkommen!')?>).addClass('log-battle-escape-impossible');
                    if (obj.v ==  0) row.text(<?=__j('Eine Flucht scheint aussichtslos...')?>).addClass('log-battle-escape-futile');
                    if (obj.v ==  1) row.text(<?=__j('Gerade noch so entkommen! Das war knapp...')?>).addClass('log-battle-escape-success');
                }

                else if (obj.type == <?=Model_Log_Message::MLM_BATTLE_DEATH?>) {
                    if (!obj.zombie) txt = game.i18n(<?=__j(':name hat es hinter sich...');?>, {':name': obj.name});
                    else  txt = game.i18n(obj.ren ? <?=__j(':zombies wurde besiegt!');?> : <?=__j('Die Meute :zombies wurde zerschlagen!');?>, {':zombies': obj.name});

                    row.text(txt).addClass('log-battle-death').addClass(obj.zombie ? 'log-battle-death-zombie' : 'log-battle-death-citizen');
                }

                else if (obj.type == <?=Model_Log_Message::MLM_BATTLE_ROUND?>) {
                    row.prev().addClass('round-close');
                    even = false;
                    row.text(game.i18n(<?=__j('Runde :round');?>, {':round': obj.round})).addClass('log-battle-round');
                }

                else if (obj.type == <?=Model_Log_Message::MLM_BATTLE_INJURY?>) {
                    even = !even;
                    row.addClass('log-battle-injury')
                        .append($('<span />').text(game.i18n(<?=__j(':name hat sich eine Verletzung zugezogen: ')?>, {':name': obj.player})))
                        .append($('<img />').attr('src', 'media/icons/' + obj.icon + '.gif'))
                        .append($('<span />').text(obj.name));
                }

                else if (obj.type == <?=Model_Log_Message::MLM_BATTLE_ATTACK?>) {
                    row.addClass('log-battle-attack').addClass(even ? 'log-battle-attack-even' : 'log-battle-attack-odd');
                    even = !even;

                    var msg_destroyed = <?=__j('Wurde beim Angriff zerstört!');?>;

                    var desc = $('<div />').addClass('cell rw-6').appendTo(row);
                    var damage = $('<div />').addClass('row').appendTo($('<div />').addClass('cell rw-6').appendTo(row));
                    var items = $('<div />').addClass('cell-small rw-9').appendTo(damage);
                    $('<div />').addClass('cell-small rw-1').append($('<i />').addClass('fa fa-chevron-right')).appendTo(damage);
                    var calc = $('<div />').addClass('cell-small rw-6').appendTo(damage);
                    $('<div />').addClass('cell-small rw-1').append($('<i />').addClass('fa fa-chevron-right')).appendTo(damage);
                    var result = $('<div />').addClass('cell-small rw-7').appendTo(damage);

                    if (obj.attacker.is_zombie) desc
                        .append($('<span />').addClass('zombie').text(obj.attacker.count + ' ' + obj.attacker.name))
                        .append($('<span />').text(obj.attacker.count == 1 ? <?=__j('stürzt sich auf')?> : <?=__j('stürzen sich auf')?>))
                        .append($('<span />').addClass('player').text(obj.defender.name));
                    else desc
                        .append($('<span />').addClass('player').text(obj.attacker.name))
                        .append($('<span />').text(<?=__j('attackiert')?>))
                        .append($('<span />').addClass('zombie').text(obj.defender.count + ' ' + obj.defender.name));

                    items.append(core.snippets.item(true,obj.weapon.name,obj.weapon.icon,1,false,false).addClass(obj.weapon.destroyed ? 'destroyed' : ''));
                    if (obj.weapon.energy)
                        items.append($('<span />').addClass('energy').text(obj.weapon.energy));
                    $.each(obj.weapon.ammo, function(k,icon) {
                        items.append(core.snippets.item(false,'',icon,1,false,false));
                    });

                    if (obj.protection.value) {
                        items.append($('<i />').addClass('fa fa-caret-right'));
                        $.each(obj.protection.covers,function(k,item) {
                            items.append(core.snippets.item(!item.stable ? msg_destroyed : true,item.name,item.icon,1,false,false).addClass(!item.stable ? 'destroyed' : ''));
                        });
                        $.each(obj.protection.armor,function(k,item) {
                            items.append(core.snippets.item(!item.stable ? msg_destroyed : true,item.name,item.icon,1,false,false).addClass(!item.stable ? 'destroyed' : ''));
                        });
                    }

                    if (obj.missed)
                        calc.append($('<span />').addClass('fa fa-ban')).append($('<span />').text(<?=__j('Verfehlt!')?>));
                    else {
                        calc.append($('<span />').addClass('calculation damage').append($('<span />').text(obj.damage)).append($('<img />').attr('src','media/icons/atk1.gif')));
                        if (obj.protection.value)
                            calc.append($('<i />').addClass('fa fa-caret-right')).append($('<span />').addClass('calculation protection').append($('<span />').text(obj.protection.value)).append($('<img />').attr('src','media/icons/atk2.gif')));
                    }

                    result.append($('<span />').addClass('final damage').append($('<span />').text(obj.damage - obj.protection.value)).append($('<img />').attr('src','media/icons/damage.gif')));
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
