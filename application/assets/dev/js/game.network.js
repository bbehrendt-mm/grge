goog.provide('game.network');
goog.require('game');

goog.require('game.render.html');

game.network = {

    queries: {},
    ai: 0,

    query: function(url,args,callback) {
        var ajax_id = game.network.ai;
        game.network.ai++;
        game.network.queries[ajax_id] = $.ajax(url, {
            cache: false,
            type: 'POST',
            data: args,
            headers: {
                'X-Request-Lang' : game.lang()
            },
            timeout: 45000
        }).done(function(data) {
            if (data && data.error)
                switch (data.error.code) {
                    case "GRGE-0002-0001":
                        return game.reset();
                    default:
                        break;
                }

            if (data.notifications)
                $.each(data.notifications, function(k,v) {
                    game.render.html.notify(v['type'], v['content'], v['title']);
                });
            callback(data);
        }).fail(function(obj, status, server) {
            if (obj && obj.responseText && 0 < (e = obj.responseText.search('<!-- ### GRG CORE INLINE RENDERING EXCEPTION: ERROR PAGE BEYOND THIS LINE ### -->'))) {
                var d = $(obj.responseText.slice(e).replace(/<(\/{0,1})(html|head|body)(.*?)>/g, '<$1var$2$3>'));
                jQuery('head').html(d.find('varhead').html());
                jQuery('body').html(d.find('varbody').html());
                eval(d.find('varbody').attr('onload'));
                return;
            }

            if (status == 'abort')
                return;
            if (status == 'timeout')
                callback({error: 'GRGE-0001-0000', name: 'E_CLIENT_CONNECTION_TIMEOUT', message: 'Connection timed out.'});
            else callback({error: 'GRGE-0002-0000', name: 'E_SERVER_ERROR', message: 'Unexpected error while processing the request.'});
        }).always(function() {
            game.network.queries[ajax_id] = null;
        });
    },

    load: function(url,args,silent) {
        game.clean();

        $.each(game.network.queries, function(k,v) {
            if (v != null)
                v.abort();
        });
        game.network.queries = {};

        if (!silent) game.render.html.modal.work();
        game.network.query(url,args,function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                window.location.href = "index.php";
                game.clean();
            } else {
                if (data.content)
                    $.each(data.content, function(k,v) {
                        game.clean(true);
                        game.render.html.put(k,v);
                    });
            }
        });
    }
};

