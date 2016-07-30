(function() {

    core.parts.chat = function(token, target) {

        $(target).empty();

        core.command('chat/w', {t: token}, true, function(data) {
            console.log(data);
        });

    };
})();