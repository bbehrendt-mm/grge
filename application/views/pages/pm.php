<?php
/**
 * @var string[] $players
 * @var mixed[] $messages
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Post')?></h1>

<div class="row">
    <div class="cell rw-10 rw-lg-9 rw-md-8 rw-sm-6 padded">
        <div class="btn" id="new_msg"><?=__('Nachricht verfassen')?></div>
    </div>
    <div class="cell rw-2 rw-lg-3 rw-md-4 rw-sm-6 padded">
        <div class="btn" data-return="1"><?=__('Zurück')?></div>
    </div>
    <div id="new_msg_area" class="cell rw-12 padded">
        <div class="row">
            <div class="cell rw-1 right padded"><?=__('An:')?></div>
            <div class="cell rw-3 padded">
                <select class="form_input" id="new_msg_address">
                    <?php foreach ($players as $pid => $player) { ?>
                        <option value="<?=$pid?>"><?=$player?></option>
                    <?php } ?>
                    <option value="-1"><?=__('Alle')?></option>
                </select>
            </div>
            <div class="cell rw-8 padded">
                <input type="text" class="form_input" id="new_msg_title" placeholder="<?=__('Titel')?>" />
            </div>
            <div class="cell rw-12 padded">
                <textarea placeholder="<?=__('Nachrichtentext');?>" class="form_input" style="height: 120px;" id="new_msg_text"></textarea>
            </div>
            <div class="cell rw-4 ro-8 padded">
                <div class="btn" id="new_msg_confirm"><?=__('Senden');?></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (!$messages) { ?>
        <div class="center"><b><?=__('Dein Posteingang ist leer...');?></b></div>
    <?php } else { ?>

        <?php foreach ($messages as $message) { ?>
            <div class="article">
                <div>
                    <b><?=htmlentities($message['title'])?></b>
                    <span><b><?= $players[$message['uid']] ?? '???' ?></b>, <?=date(__('G:i \U\h\r \a\m d.m.'), $message['timestamp'])?></span>
                </div>
                <div><?=nl2br(htmlentities($message['message']))?></div>
                <div>
                    <span class="pointer" data-mid="<?=$message['mid']?>" data-for="delete"><i class="fa fa-trash"></i> <?=__('Löschen');?></span>
                    <span class="pointer" data-uid="<?=$message['uid']?>" data-for="answer"><i class="fa fa-envelope"></i> <?=__('Antworten');?></span>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
</div>

<div class="row">
    <div class="cell rw-2 ro-10 rw-lg-3 ro-lg-9 rw-md-4 ro-md-8 rw-sm-6 ro-sm-6 padded">
        <div class="btn" data-return="1"><?=__('Zurück')?></div>
    </div>
</div>

<script type="application/javascript">
    // ## JS COMPRESS BEGIN ## //
    (function() {
        $('#new_msg').click(function() {
            $(this).hide();
            $('#new_msg_area').show();
        });
        $('#new_msg_area').hide().find('select').val(-1).selectric();

        $('[data-return]').click(function() {
            game.network.load('game/redirect');
        });

        var mbox = function(obj, callback) {
            $('#new_msg_confirm').addClass('disabled');
            $('[data-mid]').addClass('disabled');
            $('#new_msg').addClass('disabled');

            game.network.query('japi/player/message', obj, callback, function() {
                $('#new_msg_confirm').removeClass('disabled');
                $('[data-for]').removeClass('disabled');
                $('#new_msg').removeClass('disabled');
            })
        };

        $('#new_msg_confirm').click(function() {
            if (!confirm(<?=__j('Möchtest du diese Nachricht wirklich senden?')?>)) return;

            var alias = $(this);
            alias.addClass('disabled');

            var msg_title = $('#new_msg_title').val();
            var msg_body = $('#new_msg_text').val();

            if (msg_title.length < 2 || msg_body.length < 5) {
                alert(<?=__j('Nachrichtentext oder Titel sind zu kurz. Bitte verwende mindestens 2 Zeichen im Titel und 5 Zeichen im Text.')?>);
                alias.removeClass('disabled');
                return;
            }

            if (msg_title.length > 64 || msg_body.length > 2048) {
                alert(<?=__j('Nachrichtentext oder Titel sind zu lang. Bitte verwende nicht mehr als 64 Zeichen im Titel und 2048 Zeichen im Text.')?>);
                alias.removeClass('disabled');
                return;
            }

            mbox({
                action: 'new',
                title: msg_title,
                to: $('#new_msg_address').val(),
                body: msg_body
            }, function(data) {
                if (data.success) {
                    game.render.html.notify('success', <?=__j('Deine Nachricht wurde erfolgreich versandt.')?>);
                    $('#new_msg_title').val('');
                    $('#new_msg_address').val('-1');
                    $('#new_msg_text').val('');
                    $('#new_msg').show();
                    $('#new_msg_area').hide();
                } else {
                    game.render.html.notify('error', <?=__j('Beim Senden der Nachricht ist ein Fehler aufgetreten.')?>);
                }
            })
        });

        $('[data-for=delete]').click(function() {
            if (!confirm(<?=__j('Bist du sicher, dass du diese Nachricht löschen möchtest?')?>)) return;

            var alias = $(this);

            mbox({
                action: 'delete',
                mid: $(this).attr('data-mid')
            }, function(data) {
                if (data.success) {
                    game.render.html.notify('success', <?=__j('Die ausgewählte Nachricht wurde gelöscht.')?>);
                    alias.parents('.article').slideUp(200, function() {
                        $(this).remove();
                    });
                } else {
                    game.render.html.notify('error', <?=__j('Beim Löschen der Nachricht ist ein Fehler aufgetreten.')?>);
                }
            })
        });

        $('[data-for=answer]').click(function() {
            $('#new_msg').click();

            $('#new_msg_title').val('RE: ' + $(this).parents('.article').find('>div:first-child>b').text());
            $('#new_msg_address').val($(this).attr('data-uid')).selectric();
            window.scrollTo(0,0);
        });
    })();


    // ## JS COMPRESS END ## //
</script>
