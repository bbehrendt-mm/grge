<?php
if (!isset($fill_key)) $fill_key = null;

?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('QR-Code')?></h1>

<div class="row">
    <div class="cell rw-12 padded">
        <div class="note noclick">
            <?=__('Bitte trage den PIN ein, den du auf dem PC erhalten hast.');?>
        </div>
    </div>
</div>

<div class="row">
    <div class="cell rw-12 padded">

        <div class="row iconize">
            <div class="cell rw-1"><i class="fa fa-qrcode"></i></div>
            <div class="cell rw-11"><input id="key" type="text" autocomplete="off" class="form_input" placeholder="<?=__('PIN');?>" value="<?=$fill_key ? $fill_key : ''?>" /></div>
        </div><br />


        <div class="row">
            <div class="cell rw-12">
                <div id="confirm" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-arrow-right"></i></span><span id="confirm-content"><?=__('OK');?></span></div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    var login = function(key, fail_callback) {
        game.network.query('japi/account/qr', {key: key}, function(data) {
            if (data.error) {

                if (data.error.code == 'GRGE-0003-0003') {
                    game.render.html.notify('error',<?=__j('Der eingegebene PIN ist ungültig. Möglicherweise hast du den PIN bereits verwendet um ein anderes Gerät zu koppeln, oder die Gültigkeitsdauer des PINs ist abgelaufen. Bitte überprüfe deine Eingabe und erstelle ggf. einen neuen Code.')?>)
                } else alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);

                fail_callback();

            } else {
                game.network.load(data.redirect);
                game.render.html.notify('smile', '<?=__j('Deine Login-Daten wurden erfolgreich auf diesem Gerät hinterlegt. In Zukunft kannst du dich ohne QR-Codes, Pins oder Keys auf diesem gerät einloggen.');?>', game.i18n('<?=__j('Willkommen, :name!');?>', {':name': data.login.name}), 4000);

                var profiles = game.storage.get('login','profiles',{});
                profiles[data.login.user] = data.login;
                game.storage.set('login','profiles',profiles);
            }
        });
    };

    $('#confirm').click(function() {
        var key = $('#key').val();

        if (!key) {
            alert('<?=__j('Bitte gib deinen PIN ein.');?>');
            return;
        }

        var alias = $(this);
        alias.addClass('btn-disabled').find('.fa').attr('class','fa fa-spin fa-circle-o-notch');
        alias.find('#confirm-content').html('<?=__j('Bitte warten...');?>');

        $('#content').find('.form_input').attr('disabled', 'disabled');

        login(key, function() {
            var confirm = $('#confirm');
            confirm.removeClass('btn-disabled').find('.fa').attr('class','fa fa-arrow-right');
            confirm.find('#confirm-content').html('OK');

            $('#content').find('.form_input').removeAttr('disabled');
        });
    });
// ## JS COMPRESS END ## //
</script>

