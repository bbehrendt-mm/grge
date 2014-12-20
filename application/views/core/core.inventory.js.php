(function() {

    var render_box = function(data, target) {
        $(target).empty();
        $.each(data, function(k,v) {
            var container;
            var flags = $.map(v.flags, function(m) {return m;});
            $(target).append(
                container = $('<li />').append(
                    $('<img />').attr('src', 'media/icons/items/' + v.icon)
                )
            );

            var notes = [];
            $.each(flags, function(k,v) {
                switch (v) {
                    case 'equipped':
                        container.addClass('equipped');
                        break;
                    case 'armor':
                        notes.push(<?=__j('Dies ist eine Rüstung. Sie wendet während eines Kampfes Schaden von dir ab.')?>);
                        break;
                    case 'weapon':
                        notes.push(<?=__j('Dies ist eine Waffe. Sie wird automatisch eingesetzt wenn du gegen Zombies kämpfst.')?>);
                        break;
                    case 'escape':
                        notes.push(<?=__j('Dieser Gegenstand hilft dir dabei, vor Zombies zu fliehen die dich Belagern. Er wird automatisch bei Bedarf eingesetzt.')?>);
                        break;
                    case 'temp':
                        container.addClass('temp');
                        notes.push(<?=__j('Dieser Gegenstand verschwindet, wenn du ihn zurücklässt.')?>);
                        break;
                    case 'event':
                        container.addClass('event');
                        notes.push(<?=__j('Dies ist ein Event-Gegenstand. Er verschwindet, wenn du das Event-Gebiet verlässt oder das Event endet.')?>);
                        break;
                    case 'carrier':
                        notes.push(<?=__j('Du kannst diesen Gegenstand mitführen, ohne dass dein Rucksack belastet wird.')?>);
                        break;
                }
            });

            if (v.static > 1)
                container.append($('<div />').addClass('staticCount').text(v.static));
            else if (v.count != null)
                container.append($('<div />').addClass('instanceCount').text(v.count));

            container.attr('title','-').qtip(game.render.html.qtip.ingame('top',{
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty();

                    content.append(
                        $('<b />').addClass('header').text(v.name)
                    ).append(v.description);

                    if (notes.length) content.append('<span class="separator" />');
                    $.each(notes, function(k,v) {
                        content.append(
                            $('<div />').addClass('note').text(v)
                        )
                    });
                }
            }));

        })
    };

    var render_block = function(data, target, headline) {
        $(target).empty().append(
            $('<h3 />').text(headline)
        );
        $.each(data, function(k,v) {
            var item_target;
            $(target).append(
                $('<div />').addClass('row').append(
                    $('<b />').text(v.name)
                ).append(
                    item_target = $('<ul />')
                )
            );
            render_box(v.items, item_target);
        });
    };

    core.parts.inventory = function(data, target) {
        var iv_a, iv_b;
        $(target).empty().append(
            $('<div />').addClass('cell rw-6 padded').append(
                iv_a = $('<div />').addClass('row inventory inventory_location')
            )
        ).append(
            $('<div />').addClass('cell rw-6 padded').append(
                iv_b = $('<div />').addClass('row inventory inventory_player')
            )
        );

        render_block(data.player, iv_a, <?=__j('Dein Rucksack')?>);
        render_block(data.location, iv_b, data.home ? <?=__j('Deine Truhe')?> : <?=__j('Items am Boden')?>);
    };
})();