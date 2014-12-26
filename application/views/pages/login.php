<?php
/**
 * @var array $services Available services as array
 */
if (!isset($services)) $services = array();
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Login')?></h1>

<div class="row">
    <div class="cell rw-6 padded">
        <div class="help noclick">
            <h4><?=__('Willkommen bei ZombVival')?></h4>
            <?=__('ZombVival ist ein an Die Verdammten von MotionTwin angelehntes, kostenloses Browserspiel. Entwickelt wird es von einem Die Verdammten-Spieler namens Brainbox. In ZombVival kämpfst du als Überlebender einer Zombie-Apokalypse gegen Horden von Untoten, während du die Überreste der Zivilisation nach Nahrung, Waffen und anderen nützlichen Gegenständen durchsuchst. Dein Tod ist gewiss - aber es liegt an dir, ihn so lange wie möglich herauszuzögern! Nebenbei kannst du versuchen, Auszeichnungen zu sammeln und die Spitze diverser Rankings zu erobern!');?>
        </div>
    </div>
    <div class="cell rw-6 padded">

        <h2><?=__('Logge dich über deinen ::i::Twinoid::/i::-Account ein!');?></h2>

            <div id="custom_login">
                <div class="row iconize" title="<?=__('Bitte wähle, welches ::i::Motion-Twin::/i::-Spiel du für den Login nutzen möchtest.');?>">
                    <div class="cell rw-1"><i class="fa fa-gamepad"></i></div>
                    <div class="cell rw-11">
                        <label for="service"></label><select class="form_input" id="service">
                            <?php foreach ($services as $name) { ?>
                                <option data-description="<?=__('Verwende deinen :name-Account', [':name' => $name]);?>" value="<?=$name?>"><?=$name?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div><br />

                <div class="row iconize" title="<?=__('Um deinen geheimen Schlüssel zu erhalten, musst du ZombVival über das ::b::Verzeichnis::/b:: von ::i::Die Verdammten::/i:: betreten.');?>">
                    <div class="cell rw-1"><i class="fa fa-user"></i></div>
                    <div class="cell rw-11"><input id="key" type="text" autocomplete="off" class="form_input" placeholder="<?=__('Geheimer Schlüssel');?>" /></div>
                </div><br />

                <div class="row">
                    <div class="cell rw-6"><label title="<?=__('Aktiviere diese Option, wenn du möchtest, dass deine Daten beim nächsten Besuch von ZombVival automatisch eingetragen werden. ::b::Aktiviere diese Option nicht, wenn du einen öffentlichen Computer verwendest!::/b::');?>"><input type="checkbox" class="form_input" id="remember"><?=__('Daten merken');?></label></div>
                    <div class="cell rw-6"></div>
                </div>

                <div class="row iconize">
                    <div class="cell rw-6 ro-6">
                        <div id="confirm" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-arrow-right"></i></span><span id="confirm-content"><?=__('Einloggen');?></span></div>
                    </div>
                </div>
            </div>

            <div id="profiles">

                <div class="row iconize">
                    <div class="cell rw-6">
                        <br />
                        <span id="delete" class="link small"><i class="fa fa-trash-o"></i> <?=__('Gespeicherte Daten löschen');?></span>
                    </div>
                    <div class="cell rw-6">
                        <div id="custom" class="btn"><?=__('Anderer Account');?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#persistent').empty();

    var login = function(key, service,remember, fail_callback) {
        game.network.query('japi/account/login', {key: key, service: service}, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);

                fail_callback();

            } else {
                game.network.load(data.redirect);
                game.render.html.notify('smile', '<?=__('Die Zombies freuen sich schon darauf, dich zu sehen...');?>', game.i18n('<?=__('Willkommen, :name!');?>', {':name': data.login.name}), 4000);

                if (remember) {
                    var profiles = game.storage.get('login','profiles',{});
                    profiles[data.login.user] = data.login;
                    game.storage.set('login','profiles',profiles);
                }
            }
        });
    };

    $('#delete').click(function() {
        if (!confirm('<?=__('Bist du sicher?');?>'))
            return;

        game.storage.set('login','profiles',{});
        $('#custom').click();

    });

    $('#content').find('.row.iconize, label').qtip(game.render.html.qtip.help('left'));

    $('#profiles').hide();

    $.each(game.storage.get('login','profiles',{}), function(k,v) {
        $('#profiles, #custom').show();
        $('#custom_login').hide();

        var mugshot = $('<div class="mugshot"><span class="mugshot-head" /><span class="mugshot-fill"><i class="fa fa-spin fa-circle-o-notch"></i></span><img alt="" /><span class="mugshot-append" /></div>');
        mugshot.find('.mugshot-head').text(v.host).end().find('img').attr('src', v.avatar || 'media/img/mugshot.png').end().find('.mugshot-append').text(v.name);
        mugshot.find('.mugshot-fill').hide();
        mugshot.find('img').error(function() {
            alert('!');
            $(this).attr('src', 'media/img/mugshot.png').off('error');
        });
        mugshot.click(function() {
            var alias = $(this);
            $('#content').find('.mugshot').addClass('disabled');
            $('#custom').addClass('btn-disabled');
            alias.find('.mugshot-fill').show();

            login(v.key, v.host, false, function() {
                $('#custom').addClass('btn-disabled');
                $('#content').find('.mugshot').removeClass('disabled');
                alias.find('.mugshot-fill').hide();
            });
        });

        $('#profiles').prepend(mugshot);
    });

    $('#custom').click(function() {
        $('#profiles').hide();
        $('#custom_login').show();
    });

    $('#confirm').click(function() {
        var key = $('#key').val();
        var service = $('#service').find('option:selected').val();

        if (!key) {
            alert('<?=__('Bitte gib deinen Geheimen Schlüssel ein.');?>');
            return;
        }

        var alias = $(this);
        alias.addClass('btn-disabled').find('.fa').attr('class','fa fa-spin fa-circle-o-notch');
        alias.find('#confirm-content').html('<?=__('Bitte warten...');?>');

        $('#content').find('.form_input').attr('disabled', 'disabled');

        login(key, service, $('#remember').is(':checked'), function() {
            var confirm = $('#confirm');
            confirm.removeClass('btn-disabled').find('.fa').attr('class','fa fa-arrow-right');
            confirm.find('#confirm-content').html('Einloggen');

            $('#content').find('.form_input').removeAttr('disabled');
        });
    });

    $('#service').selectric();
    $('#remember').customRadioCheck();
// ## JS COMPRESS END ## //
</script>

