goog.provide('game.network');
goog.require('game');

goog.require('game.render.html');

game.network = {

    query: function(url,args,callback) {
        $.ajax(url, {
            cache: false,
            type: 'POST',
            data: args,
            headers: {
                'X-Request-Lang' : game.lang()
            },
            timeout: 45000
        }).done(callback)
        .fail(function(obj, status, server) {
            if (status == 'timeout')
                callback({error: 'GRGE-0001-0000', name: 'E_CLIENT_CONNECTION_TIMEOUT', message: 'Connection timed out.'});
            else callback({error: 'GRGE-0002-0000', name: 'E_SERVER_ERROR', message: 'Unexpected error while processing the request.'});
        });
    },

    load: function(url,args,silent) {
        game.clean();
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
                if (data.notifications)
                    $.each(data.notifications, function(k,v) {
                        game.render.html.notify(v['type'], v['content'], v['title']);
                    })
            }
        });
    }
};

