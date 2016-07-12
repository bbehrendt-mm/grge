<?php
/**
 * @var array $version_data Version Data
 */
?>

/**
 * @var {Core} core
 */
core = {
    parts: {},
    snippets: {},

    version: '<?="{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']}"?>',

    last: {},
    plugins: {},

    cache: {},

    sessiondata: {},


    /**
     * Retrieves an object from cache
     * @param id {string} Identifier
     * @param [initalization] {object|function} Default return value.
     * @returns {*}
     */
    cache_get: function(id, initalization) {
        if (typeof this.cache[id] !== 'undefined') return this.cache[id];
        else if (!initalization) return null;
        else if (typeof initalization === 'function') return initalization(id);
        else return initalization;
    },

    cache_put: function(id, data) {
        this.cache[id] = data;
    },

    session: function(key, data) {
        return (typeof data == 'undefined')
            ? core.sessiondata[key]
            : (core.sessiondata[key] = data);
    },

    renderLog: function(target) {
        this.command('game/logs',{}, true, function(data) {
            if (data.log)
                core.parts.log(data.log,$('<div />').addClass('row log_box').appendTo(target.empty()));
        }, true)
    },

    command: function(url, args, background, callback, no_clean, finished) {
        if (!url)
            url = 'japi/game/data';
        else url = 'japi/' + url;

        if (!background) game.render.html.modal.work();

        var scroll = $(document).scrollTop();

        game.network.query(url,args,function(data) {

            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                game.reset();
                return;
            }

            if (data.redirect) {
                game.clean();
                return game.network.load(data.redirect);
            }

            if (!background && !no_clean) game.clean(true);

            if (data.version && data.version != core.version) {
                game.reset(true);
                return;
            } if (callback)
                callback(data);
            else if (data) core.render(data, $('#content').empty());

            if (finished) finished(data);

            $(document).scrollTop(scroll)
        });
    },

    render: function(data, target) {
        <?php if ($version_data['stage'] < 3) { ?>
            console.log(data);
        <?php } ?>

        core.last = data;

        if (core.parts.admin) core.parts.admin.controls($('<div />').addClass('cell rw-12 padded').appendTo(NF.row().appendTo(target)));

        if (data.location) {
            var location_box = NF.row().appendTo(target);
            core.parts.location(data.location, location_box);
        }

        var action_box = $('<div />').addClass('row action_box ' + (data.location.meta.outside ? 'outside' : 'inside')).appendTo(target);

        if (data.location.meta.css) {
            location_box.addClass('custom custom-' + data.location.meta.css);
            action_box.addClass('custom custom-' + data.location.meta.css);
        }

        if (data.players && data.players.messages)
            action_box.append($('<div />').addClass('note control').text(<?=__j('Du hast neue Nachrichten!')?>));

        var auto_select = $('<select />').appendTo($('<div />').addClass('cell rw-12 padded hide-desktop control').appendTo(action_box))
            .append($('<option />').val('#inv_container').text(game.storage.get('settings','heroid_ui') == 'tab' ? <?=__j('Gegenstände')?> : <?=__j('Gegenstände & Heldentaten')?>))
            .append($('<option />').val('#rpg_container').text(<?=__j('Kampfausrüstung')?>))
            .append((game.storage.get('settings','heroid_ui') == 'tab') ? $('<option />').val('#inv_heroics').text(<?=__j('Heldentaten')?>) : null)
            .append($('<option />').val('#settings_container').text(<?=__j('Zeitfluss & Verhalten')?>))
            .append($('<option />').val('#game_info').text(<?=__j('Spieldetails')?>))
            .append(data.players ? $('<option />').val('#mp_container').text(data.players.multiplayer ? ((data.players.messages ? '[!!!] ' : '') + <?=__j('Spieler & NPCs')?>) : <?=__j('NPCs')?>) : false)
            .change(function() {
                $('[data-toggle="' + $(this).val() + '"]').click();
            })
            .selectric();

        var auto_tab = $('<ul />').addClass('tabline hide-mobile').appendTo(action_box)
            .append($('<li>').attr('data-toggle', '#inv_container').text(game.storage.get('settings','heroid_ui') == 'tab' ? <?=__j('Gegenstände')?> : <?=__j('Gegenstände & Heldentaten')?>))
            .append($('<li>').attr('data-toggle', '#rpg_container').text(<?=__j('Kampfausrüstung')?>))
            .append((game.storage.get('settings','heroid_ui') == 'tab') ? $('<li>').attr('data-toggle', '#inv_heroics').text(<?=__j('Heldentaten')?>) : null)
            .append($('<li>').attr('data-toggle', '#settings_container').text(<?=__j('Zeitfluss & Verhalten')?>))
            .append($('<li>').attr('data-toggle', '#game_info').text(<?=__j('Spieldetails')?>))
            .append(data.players ? $('<li>').attr('data-toggle', '#mp_container').text(data.players.multiplayer ? <?=__j('Spieler & NPCs')?> : <?=__j('NPCs')?>).prepend(data.players.multiplaye && data.players.messages ? $('<img />').attr('src','media/icons/new.png') : false) : false)
            .find('>li').click(function() {
                if ($(this).hasClass('active')) return;
                var t = $($(this).data('toggle'));
                auto_select.val($(this).data('toggle')).selectric();
                core.session('main.tabs.open', $(this).data('toggle'));
                $(this).addClass('active').siblings().removeClass('active');
                if (game.s.quality() > 1) {
                    action_box.children('div:not(.control)').stop().slideUp({queue: false, duration: 200}).css({opacity: 1}).animate({opacity: 0}, 200);
                    t.stop().insertAfter(t.siblings('ul')).slideDown({queue: false, duration: 200}).css('opacity',0).animate({opacity: 1}, 200);
                } else {
                    action_box.children('div:not(.control)').hide();
                    t.show();
                }

            }).first();

        if (data.inventory) {
            core.parts.inventory(data.inventory, $('<div />').attr('id', 'inv_container').addClass('row').appendTo(action_box), game.storage.get('settings', 'heroid_ui') != 'tab');
            if (game.storage.get('settings', 'heroid_ui') == 'tab')
                core.parts.heroics(data.inventory, $('<div />').attr('id', 'inv_heroics').addClass('row').appendTo(action_box));
        }

        if (data.rpg && data.inventory)
            core.parts.rpg(data.rpg, data.inventory.player, $('<div />').attr('id', 'rpg_container').addClass('row').appendTo(action_box));

        if (data.settings)
            core.parts.settings(data.settings, $('<div />').attr('id', 'settings_container').addClass('row').appendTo(action_box));

        if (data.game)
            core.parts.info(data.game, $('<div />').attr('id', 'game_info').addClass('row').appendTo(action_box));

        if (data.players)
            core.parts.mp_players(data.players, $('<div />').attr('id', 'mp_container').addClass('row').appendTo(action_box));

        if (data.status && data.clock)
            core.parts.status(data.status, data.clock, $('#persistent'));
        
        if (data.log)
            core.parts.log(data.log,$('<div />').addClass('row log_box').appendTo(target));

        var set_tab = auto_tab.parent().children().filter('[data-toggle="' + core.session('main.tabs.open') + '"]');
        if (set_tab.length == 1) set_tab.click();
        else auto_tab.click();
    }
};