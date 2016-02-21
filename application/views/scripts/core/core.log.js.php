(function() {
    var renderers = {};

    renderers[<?=Model_Log_Message::MLM_PRERENDERED_STRING?>] =
        function(data) {
            return data.title
                ? $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<div />').addClass('sub').text(data.body))
                : $('<div />').append($('<div />').text(data.body))

        };

    renderers[<?=Model_Log_Message::MLM_RAW_DATA?>] =
        function(data) {
            return $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<pre />').addClass('sub').text(data.body));
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

    renderers[<?=Model_Log_Message::MLM_COMBAT?>] =
        function(data) {
            var header;

            var title = $('<div />');

            title.append($('<span />').text(data.msg)).data('expandable', true).append(
                sub = $('<div />').addClass('sub')
            );

            var videobtn = $('<div />').addClass('btn btn-icon')
                .append($('<span />').addClass('btn-icon-inner').append($('<i />').addClass('fa fa-video-camera')))
                .append($('<span />').text(<?=__j('Kampf ansehen')?>))
                .click(function(e) {
                    e.stopPropagation();
                    var popup = core.popup.spawn(644);

                    var fav = (data.gallery || videobtn.data('nogallery')) ? $('<div />').addClass('b').text(<?=__j('Dieses Video befindet sich bereits in deiner Gallerie.')?>) : $('<div />').addClass('btn').text(<?=__j('In meine Kampfgallerie aufnehmen')?>)
                        .click(function() {
                            var label = prompt(<?=__j('Bitte gib deinem Kampf einen Titel, unter dem er in deiner Gallerie erscheinen soll.')?>, game.i18n(<?=__j('Kampf #:id')?>, {':id': data.bid}))

                            if (label) {
                                fav.addClass('disabled');

                                core.command('player/favbattle', {v: data.bid, l: label}, true, function(data) {
                                    if (data.success) {
                                        game.render.html.notify('success', <?=__j('Deine Videogallerie wurde aktualisiert!')?>);
                                        fav.replaceWith($('<div />').addClass('b').text(<?=__j('Dieses Video befindet sich bereits in deiner Gallerie.')?>));
                                        videobtn.data('nogallery', true);
                                    } else {
                                        game.render.html.notify('error', <?=__j('Das Video konnte nicht in deine Gallerie kopiert werden ...')?>);
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
                                    .text(<?=__j('Hast du einen besonders beeindruckenden Kampf erlebt, kannst du ihn in deine Kampfgallerie kopieren. Von dort aus kannst du ihn jederzeit auch nach Beendigung des Spiels ansehen, deinen Freunden präsentieren und sogar in andere Webseiten einbinden.')?>)
                                    .append(fav)
                            ))
                        )
                });

            var current_row;
            sub
                .append(NF.row().append(NF.cell(true, 12).text(data.bdy)))
                .append(current_row = NF.row());

            current_row.append($('<div />').addClass('cell rw-4 rw-md-6 rw-sm-12 padded').append(
                $('<div />').addClass('note').text(<?=__j('Keine Lust auf langweilige Kampfstatistiken? Dann schau dir doch einfach ein Video des Kampfes an!')?>).append(videobtn)
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
                case <?=Model_Log_Types_Item::MLTI_RAVEN?>:
                    header = <?=__j('Der Rabe hat :itemdef gebracht!')?>;
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

    renderers[<?=Model_Log_Message::MLM_TRANSACTION_LOG?>] =
        function(data) {
            var header;
            switch (data['class']) {
                case <?=Model_Log_Types_Transaction::MLTT_UP?>:
                    header = data.self ? <?=__j('Du hast :itemdef aufgehoben.')?> : <?=__j(':name hat :itemdef aufgehoben.')?>;
                    break;
                case <?=Model_Log_Types_Transaction::MLTT_DOWN?>:
                    header = data.self ? <?=__j('Du hast :itemdef abgelegt.')?> : <?=__j(':name hat :itemdef abgelegt.')?>;
                    break;
                case <?=Model_Log_Types_Transaction::MLTT_USE?>:
                    header = data.self ? <?=__j('Du hast :itemdef verwendet (:action).')?> : <?=__j(':name hat :itemdef verwendet (:action).')?>;
                    break;
            }


            var title = $('<div />');
            header = game.i18n(header, {':name': data.player, ':action': data.action});
            var pos = header.search(':itemdef');
            if (pos >= 0) {
                title.append($('<span />').text(header.slice(0,pos)));
                $.each(data.items, function(k, item) {
                    title.append(core.snippets.item(true,item.name,item.icon,data['class'] == <?=Model_Log_Types_Transaction::MLTT_USE?> ? 0 : item.count,false,false));
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
