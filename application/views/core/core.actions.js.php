(function() {
    core.snippets.button = function(action, call) {

        if (typeof action === "string")
            return $('<div />').addClass('btn').text(action).click(call);
        else {
            var button = $('<div />');
            var ext = $('<div />').addClass('row').hide();

            var block;
            if ($.objToArray(action.requires).length) {

                ext.append(
                    block = $('<div />').addClass('cell rw-6 padded').append(
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
                    block = $('<div />').addClass('cell rw-6 padded').append(
                        $('<i />').addClass('separator').text(<?=__j('Effekte')?>)
                    )
                );
                $.each(action.effects, function(k,v) {
                    block.append(
                        $('<div />').addClass('group').addClass(v.color).append(
                            v.icon ? $('<img />').attr('src', 'media/icons/' + v.icon + '.gif') : $('<img />').addClass('fake').attr('src', 'media/icons/fake_h.gif')
                        ).append(
                            $('<span />').text(v.value)
                        )
                    );
                })
            }

            if ($.objToArray(action.sides).length) {
                ext.append(
                    block = $('<div />').addClass('cell rw-6 padded').append(
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

            return button
                .addClass('btn btn-zv')
                .append(
                    $('<span />').text(action.description)
                ).append(ext).click(function () {
                    eval(action.javascript);
                    if (call) call();
                }).mouseenter(function() {
                    if (ext.children().size()) ext.stop().slideDown('fast');
                }).mouseleave(function() {
                    if (ext.children().size()) ext.stop().slideUp('slow');
                });
        }
    };
})();