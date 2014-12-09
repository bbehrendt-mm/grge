<?php
/**
 * @var string $user Username
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Backend-Login')?></h1>

<div class="row">
    <div class="cell rw-6 padded">
        <div class="help noclick">
            <h4><?=__('ZombVival Backend')?></h4>
            <?=__('Hier gehts für dich vermutlich erstmal nicht weiter. Das ZombVival-Backend ist ausschließlich für Moderatoren und Administratoren gedacht. ::b::Bitte versuche nicht, dich hier einzuloggen, wenn du nicht genau weist was du tust!::/b::');?>
        </div>
    </div>

    <div class="cell rw-6 padded">

        <div class="row iconize">
            <div class="cell rw-1"><i class="fa fa-user"></i></div>
            <div class="cell rw-11"><input class="form_input" type="text" disabled="disabled" value="<?=$user;?>" /></div>
        </div><br />

        <div class="row iconize">
            <div class="cell rw-1"><i class="fa fa-lock"></i></div>
            <div class="cell rw-11"><input  class="form_input" id="password" type="password" placeholder="<?=__('Backend-Passwort');?>" /></div>
        </div><br />

        <div class="row iconize">
            <div class="cell rw-6 ro-6">
                <div id="confirm" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-unlock"></i></span><span id="confirm-content"><?=__('Einloggen');?></span></div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    var login = function(password, fail_callback) {
        game.network.query('admin/japi/account/login', {password: password}, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);

                fail_callback();
            } else {
                game.network.load(data.redirect);
                game.render.html.notify('smile', game.i18n(<?=__j('Dein Zugangstoken läuft in :sec Sekunde/n (ca. :min Minute/n) ab.');?>, {':sec': data.duration, ':min': Math.round(data.duration/60)}), game.i18n(<?=__j('Willkommen im Administrationsbereich, :name!');?>, {':name': '<?=$user;?>'}), 4000);
            }
        });
    };

    $('#confirm').click(function() {
        var pw = $('#password').val();

        if (!pw) {
            alert('<?=__('Bitte gib dein Passwort ein.');?>');
            return;
        }

        var alias = $(this);
        alias.addClass('btn-disabled').find('.fa').attr('class','fa fa-spin fa-circle-o-notch');
        alias.find('#confirm-content').html('<?=__('Bitte warten...');?>');

        $('#content').find('.form_input').attr('disabled', 'disabled');

        login(pw, function() {
            var confirm = $('#confirm');
            confirm.removeClass('btn-disabled').find('.fa').attr('class','fa fa-arrow-right');
            confirm.find('#confirm-content').html('Einloggen');

            $('#content').find('.form_input').removeAttr('disabled');
        });
    });
</script>

