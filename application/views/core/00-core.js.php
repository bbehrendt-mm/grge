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

    version: '<?="{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']}"?>',

    command: function(url, args, background, callback, no_clean) {
        var c;

        if (!url)
            url = 'japi/game/data';
        else url = 'japi/' + url;

        if (!background) game.render.html.modal.work();
        game.network.query(url,args,function(data) {

            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                game.reset();
                return;
            }

            if (!background && !no_clean) game.clean(true);

            if (data.version && data.version != core.version) {
                game.reset();
                return;
            } if (callback)
                callback(data);
            else core.render(data, $('#content').empty());
        });
    },

    render: function(data, target) {
        console.log(data);
        if (data.location) {
            var location_box = $('<div />').addClass('row').appendTo(target);
            core.parts.location(data.location, location_box);
        }

        var action_box = $('<div />').addClass('row action_box ' + (data.location.meta.outside ? 'outside' : 'inside')).appendTo(target);
        if (data.inventory)
            core.parts.inventory(data.inventory, action_box);

        if (data.status && data.clock)
            core.parts.status(data.status, data.clock, $('#persistent'));
        
        if (data.log)
            core.parts.log(data.log,$('<div />').addClass('row log_box').appendTo(target))

    }
};