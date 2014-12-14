core = {
    parts: {},
    command: function(url, args, background, callback) {
        if (!url)
            url = 'japi/game/data';
        else url = 'japi/' + url;

        if (!background) game.render.html.modal.work();
        game.network.query(url,args,function(data) {
            game.clean(true);
            if (callback)
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
    }
};