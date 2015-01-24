(function() {
    var renderers = {};

    renderers[<?=Model_Log_Message::MLM_PRERENDERED_STRING?>] =
        function(data) {
            return data.title
                ? $('<div />').data('expandable', true).append($('<div />').text(data.title)).append($('<div />').addClass('sub').text(data.body))
                : $('<div />').append($('<div />').text(data.body))

        };

    renderers[<?=Model_Log_Message::MLM_MOVEMENT_EVENT?>] =
        function(data) {
            var txt;
            switch (data['class']) {
                case <?=Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER?>:
                    txt = data.self ? <?=__j('Du hast diesen Ort betreten.')?> : game.i18n(<?=__j(':name hat diesen Ort betreten.')?>, {':name': data.name});
                    break;
                case <?=Model_Log_Types_Movement::MOVEMENT_TYPE_LEAVE?>:
                    txt = data.self ? <?=__j('Du hast diesen Ort verlassen.')?> : game.i18n(<?=__j(':name hat diesen Ort verlassen.')?>, {':name': data.name});
                    break;
                case <?=Model_Log_Types_Movement::MOVEMENT_TYPE_PASS?>:
                    txt = data.self ? <?=__j('Du hast diesen Ort auf deinem Weg passiert.')?> : game.i18n(<?=__j(':name hat diesen Ort auf seinem Weg passiert.')?>, {':name': data.name});
                    break;
            }

            return $('<div />').text(txt);
        };


    core.parts.log = function (data, target) {
        console.log(data);

        $.each(data, function(k,v) {
            var content;

            var rendered = renderers[v.type] ? renderers[v.type](v.data) : $('<div />').text('[RENDER ERROR] NO RENDERER PROVIDED FOR GIVEN MTYPE (' + v.type + ')!');
            var expandable = rendered.data('expandable');
            target.append(
                $('<div />').addClass('col rw-12 message' + (expandable ? ' pointer' : '')).append(
                    $('<div />').addClass(v.new ? 'timestamp new' : 'timestamp').text((new Date(v.time * 1000)).toLocaleTimeString())
                ).append(
                    content = $('<div />').addClass('content').append(rendered)
                ).click(function() {
                    $(this).find('.sub').slideToggle(200);
                })
            );
            content.find('.sub').hide();
        })
    };
})();
