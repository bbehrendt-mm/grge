(function() {

    var tid = -1;
    var active = false;
    var queued = [];
    var token = null;
    var last = 0;

    var cache = {u: {}, p: {}};
    var message_target;
    var userbox_target;
    var pinbox_target;

    var colorFromName = function(name) {
        var color = [0,0,0];
        var brightness = 0;
        for (var i = 0; i < name.length; i++)
            color[i%3] += name.charCodeAt(i);
        for (var j = 0; i < 3; j++)
            brightness += (color[j] = color[j]%256)/3;

        return ["#" + ((1 << 24) + (color[0] << 16) + (color[1] << 8) + color[2]).toString(16).slice(1), brightness < 127 ? '#FFFFFF' : '#000000'];
    };

    var draw_messages = function(messages) {
        $.each(messages, function(id,msg) {

            if (msg.type == <?=Controller_Chat::CC_PIN?>) {
                pinbox_target.empty()
                    .append(NF.n('div','b small center').text(<?=__j('Angepinnte Nachricht')?>))
                    .append(NF.n('span').text(msg.message));
                return;
            }

            var c;
            var e;
            var color = colorFromName(msg.sender);
            message_target.append(e = NF.cell(true,12,0,'message chat_t_' + msg.type)
                .append(NF.n('div','time',(new Date(msg.timestamp * 1000)).toLocaleTimeString()))
                .append(NF.n('div','sender').append(
                    NF.n('span').css({background: color[0], 'color': color[1]})
                        .on('mouseenter', function() {$(this).text(msg.sender);})
                        .on('mouseleave', function() {$(this).text(msg.sender.substring(0, 2));})
                        .trigger('mouseleave')
                ))
                .append(c = NF.n('div','message'))
            );

            switch (parseInt(msg.type)) {
                case <?=Controller_Chat::CC_MESSAGE?>:
                    c.text(msg.message);
                    break;
                case <?=Controller_Chat::CC_WHISPER?>:
                    if (msg.to) c.text(game.i18n(<?=__j('Geflüstert an :name: :message')?>, {':name': msg.to, ':message': msg.message}));
                    else c.text(game.i18n(<?=__j(':name flüstert: :message')?>, {':name': msg.sender, ':message': msg.message}));
                    break;
                case <?=Controller_Chat::CC_STATE?>:
                    c.text(msg.sender + ' ' + msg.message);
                    break;
                default: {
                    e.remove();
                    e = null;
                }
            }
            if (e) message_target.animate({ scrollTop: e.position().top}, 100);
        });
    };

    var render_messages = function(data) {

        $.each(data.p, function(id,msg) {
            last = Math.max(id,last);
            cache.p[id] = msg;

        });
        draw_messages(data.p);

        if (JSON.stringify(cache.u) != JSON.stringify(data.u)) {
            cache.u = data.u;
            userbox_target.empty();

            $.each(data.u, function(uid, ud) {
                userbox_target.append(NF.row()
                    .append(NF.cell(false, 9, 1, 'left').text(ud[0]))
                    .append(NF.cell(false, 2, 0, 'right').append(ud[1] ? NF.fa('wifi') : null))
                );
            });
        }


    };

    var transaction = function(from_timer, message, callback) {
        if (!$('.chat').length) return;

        if (active) {
            queued.push(message);
            return;
        }
        
        active = true;
        if (!from_timer) window.clearTimeout(tid);

        core.command('chat', {t: token, l: last, m: message ? message : queued.pop()}, true, function(data) {
            render_messages(data);
        }, false, null, function() {
            active = false;
            if (callback) callback();
            tid = window.setTimeout(function() {transaction(true);}, queued.length ? 500 : 3000);
        });
    };


    core.parts.chat = function(tkn, target) {

        $(target).empty();

        token = tkn;

        NF.row()
            .append(NF.cell(false, 9).addClass('rw-md-12').append(NF.n('div','b small center').text(<?=__j('ZombVival-Chat')?>)).append(message_target = NF.n('div','chatlog')))
            .append(NF.cell(false, 3).addClass('rw-md-12').append(NF.n('div','row chatlog')
                .append(userbox_target = NF.cell(true,12))
                .append(NF.cell(true,12).append(pinbox_target = NF.n('div','flatbox')))
            ))
            .appendTo(target);

        var controls = NF.row().appendTo(target);

        var textbox;
        var sendbutton;

        controls
            .append(NF.scell(true, 1, 0, 'center').append(NF.fa('question-circle')).attr('title','-').qtip(game.render.html.qtip.ingame('bottom',{
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty();

                    content
                        .append(NF.n('div').text(<?=__j('Du kannst eine Nachricht an den Chat anheften, indem du "/pin [DEINE NACHRICHT]" eingibst.')?>))
                        .append('<span class="separator" />')
                        .append(NF.n('div').text(<?=__j('Du kannst einen Spieler anflüstern, indem du "/whisper [SPIELER] [DEINE NACHRICHT]" eingibst; nur der angegebene Spieler wird die Nachicht lesen können.')?>))
                }}))
            )
            .append(NF.scell(true, 17).append(textbox = NF.input('text').attr('placeholder',<?=__j('Chat-Nachricht eingeben')?>).keydown(function(e) {if (e.keyCode == 13) sendbutton.click()})))
            .append(NF.cell(true, 3).append(sendbutton = NF.button(<?=__j('Senden')?>,false,'send').click(function() {
                var m = textbox.val();
                textbox.val('');
                controls.addClass('disabled');
                transaction(false, m, function() {
                    controls.removeClass('disabled');
                })
            })));

        draw_messages(cache.p);
        transaction();
    };
})();