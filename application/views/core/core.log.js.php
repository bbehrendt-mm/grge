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
