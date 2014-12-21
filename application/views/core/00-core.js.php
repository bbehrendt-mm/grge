<?php
/**
 * @var array $version_data Version Data
 */
?>
core = {
    parts: {},
    snippets: {},

    version: '<?="{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']}"?>',

    command: function(url, args, background, callback) {
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

            game.clean(true);

            if (data.version && data.version != core.version) {
                game.reset();
                return;
            } if (callback)
                callback(data.callback || {});
            core.render(data, $('#content').empty());
        });
    },

    render: function(data, target) {
        console.log(data);
        if (data.location) {
            var location_box = $('<div />').addClass('row').appendTo(target);
            core.parts.location(data.location, location_box);
        }

        var active_box = $('<div />').addClass('row').appendTo(target);
        if (data.inventory) {
            var inventory_box = $('<div />').addClass('cell rw-8 row').appendTo(active_box);
            core.parts.inventory(data.inventory, inventory_box)
        }
    }
};