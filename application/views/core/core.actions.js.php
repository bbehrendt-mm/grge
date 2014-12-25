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
})();