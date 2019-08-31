<?php
/**
 * @var array $services Available services as array
 */
if (!isset($services)) $services = array();
if (!isset($preset_legacy_key)) $preset_legacy_key = '';
if (!isset($preset_legacy_service)) $preset_legacy_service = '';
if (!isset($preset_zvid)) $preset_zvid = -1;

?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Login')?></h1>

<div class="row">
    <div class="cell rw-6 padded rw-md-0 nopad-md">
        <div class="help noclick">
            <h4><?=__('Willkommen bei ZombVival')?></h4>
            <?=__('ZombVival ist ein an Die Verdammten von MotionTwin angelehntes, kostenloses Browserspiel. Entwickelt wird es von einem Die Verdammten-Spieler namens Brainbox. In ZombVival kämpfst du als Überlebender einer Zombie-Apokalypse gegen Horden von Untoten, während du die Überreste der Zivilisation nach Nahrung, Waffen und anderen nützlichen Gegenständen durchsuchst. Dein Tod ist gewiss - aber es liegt an dir, ihn so lange wie möglich herauszuzögern! Nebenbei kannst du versuchen, Auszeichnungen zu sammeln und die Spitze diverser Rankings zu erobern!');?>
        </div>
    </div>

    <div class="cell rw-6 rw-md-12 padded login_form" id="login_legacy_preset">
        <h2><?=__('Gleich kanns losgehen!');?></h2>

        <div id="preset_select">
            <div class="note"><?=__('Es scheint, als ob du von ::i:: :service ::/i:: hierher gelangt bist. Du kannst ::b::ZombVival::/b:: daher ohne zusätzliche Registrierung mit deinem :service-Account spielen!', [':service' => $preset_legacy_service]);?></div>
            <p><?=__('Möchtest du, dass deine Account-Daten auf diesem Rechner gespeichert werden? Dadurch kannst du dich in Zukunft direkt auf dieser Webseite anmelden, ohne den Umweg über :service gehen zu müssen. Wenn du ZombVival gerade von einem öffentlichen Computer oder dem Computer eines Freundes besuchst, solltest du "Nein" wählen.', [':service' => $preset_legacy_service]);?></p>

            <div class="row iconize">
                <div class="cell rw-4 rw-lg-5">
                    <div id="preset_forget" class="btn"><?=__('Nein');?></div>
                </div>
                <div class="cell rw-4 ro-4 rw-lg-5 ro-lg-2">
                    <div id="preset_remember" class="btn"><?=__('Ja');?></div>
                </div>
            </div>
        </div>

        <div id="preset_auto">
            <p class="center">
                <b><?=__('Automatischer Login');?></b><br />
                <i class="fa fa-spin fa-circle-o-notch"></i>
            </p>
        </div>

    </div>

    <div class="cell rw-6 rw-md-12 padded login_form" id="login_legacy">

        <div id="custom_login">
            <div id="legacy_login">
                <h2><?=__('Logge dich über deinen ::i::Twinoid::/i::-Account ein!');?></h2>

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
                </div>

                <div class="row">
                    <div class="cell padded">
                        <br />
                        <i class="fa fa-arrow-right"></i> <a id="switch_dv_to_pw" href="#"><span><?=__('Login mit E-Mail Adresse und Passwort');?></span></a>
                    </div>
                </div>

            </div>
            <div id="password_login">

                <h2><?=__('Logge dich mit deinem Passwort ein!');?></h2>

                <div class="row iconize" title="<?=__('Bitte gib deine E-Mail Adresse ein.');?>">
                    <div class="cell rw-1"><i class="fa fa-envelope"></i></div>
                    <div class="cell rw-11"><input id="login_email" type="email" autocomplete="off" class="form_input" placeholder="<?=__('E-Mail');?>" /></div>
                </div><br />

                <div class="row iconize" title="<?=__('Bitte gib dein Passwort ein.');?>">
                    <div class="cell rw-1"><i class="fa fa-key"></i></div>
                    <div class="cell rw-11"><input id="login_pass" type="password" autocomplete="off" class="form_input" placeholder="<?=__('Passwort');?>" /></div>
                </div>

                <div class="row">
                    <div class="cell padded">
                        <br />
                        <i class="fa fa-arrow-right"></i> <a id="switch_pw_to_dv" href="#"><span><?=__('Login mit Twinoid-Account');?></span></a>
                    </div>
                </div>

            </div>
            <br />

            <div class="row">
                <div class="cell rw-6 rw-md-12"><label title="<?=__('Aktiviere diese Option, wenn du möchtest, dass deine Daten beim nächsten Besuch von ZombVival automatisch eingetragen werden. ::b::Aktiviere diese Option nicht, wenn du einen öffentlichen Computer verwendest!::/b::');?>"><input type="checkbox" class="form_input" id="remember"><?=__('Daten merken');?></label></div>
                <div class="cell rw-6 rw-md-12">
                    <div id="confirm" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-arrow-right"></i></span><span id="confirm-content"><?=__('Einloggen');?></span></div>
                </div>
                <div class="cell hide-desktop rw-12 padded">
                    <div class="note noclick">
                        <?=__('Du kannst dich auch auf einem PC einloggen und deine Login-Informationen von dort bequem auf dein mobiles Gerät übertragen lassen!');?>
                    </div>
                </div>
            </div>
        </div>

        <div id="profiles">

            <h2><?=__('Bitte wähle dein Profil');?></h2>

            <div class="row iconize">
                <div class="cell rw-6 rw-sm-12">
                    <br />
                    <span id="delete" class="link small"><i class="fa fa-trash-o"></i> <?=__('Gespeicherte Daten löschen');?></span>
                </div>
                <div class="cell rw-6 rw-sm-12">
                    <div id="custom" class="btn"><?=__('Anderer Account');?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row hide-mobile">
    <div class="cell rw-12 padded">
        <div class="note noclick">
            <?=__('Du kannst ZombVival auch auf mobilen Geräten wie Smartphones und Tablets mit einer angepassten Benutzeroberfläche spielen! Nach dem Login kannst du dir unter "Seelen -> Profileeinstellungen" einen QR-Code generieren lassen, der deine Login-Daten automatisch auf dein mobiles Gerät kopiert.');?>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#persistent').empty();

    var login_mode_pw = false;
    $('#password_login').hide();

    $('#switch_dv_to_pw').on('click', function() {
        login_mode_pw = true;
        $('#legacy_login').slideUp(150);
        $('#password_login').slideDown(150);
    });

    $('#switch_pw_to_dv').on('click', function() {
        login_mode_pw = false;
        $('#password_login').slideUp(150);
        $('#legacy_login').slideDown(150);
    });

    $('.login_form').hide();
    $('<?=($preset_legacy_key && $preset_legacy_service) ? '#login_legacy_preset' : '#login_legacy'?>').show();

    var login = function(user, key, service,remember, fail_callback) {
        game.network.query('japi/account/login', {user: user, key: key, service: service, skip: (game.storage.get('settings','show-news') == 'game' && game.storage.get('news','last-seen') > (Date.now() - 86400000)) ? 1 : 0}, function(data) {
            if (data.error) {
                if (fail_callback && !fail_callback(data.error.code))
                    alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            } else {
                game.network.load(data.redirect);
                game.render.html.notify('smile', <?=__j('Die Zombies freuen sich schon darauf, dich zu sehen...');?>, game.i18n(<?=__j('Willkommen, :name!');?>, {':name': data.login.name}), 4000);

                if (remember) {
                    var profiles = game.storage.get('login','profiles',{});
                    profiles[data.login.user] = data.login;
                    game.storage.set('login','profiles',profiles);
                }
            }
        });
    };

    <?php if ($preset_legacy_key && $preset_legacy_service) {?>
        var ocp = game.storage.get('login','profiles',{});
        $('#preset_auto, #preset_select').hide();

        if (ocp[<?=$preset_zvid?>]) {
            $('#preset_auto').show();

            var ret = function() {
                $('#preset_auto').hide();
                $('#preset_select').show();
            };

            if (ocp[<?=$preset_zvid?>].key && ocp[<?=$preset_zvid?>].host)
                login(null,ocp[<?=$preset_zvid?>].key, ocp[<?=$preset_zvid?>].host, true, ret);
            else if (ocp[<?=$preset_zvid?>].token)
                login(null,ocp[<?=$preset_zvid?>].token, 'Token', true, ret);
            else ret();
        } else $('#preset_select').show();
    <?php } ?>

    $('#preset_forget, #preset_remember').click(function() {
        $(this).empty().append('<i class="fa fa-circle-o-notch fa-spin"></i><span>&nbsp;</span>');
        $('#preset_forget, #preset_remember').addClass('disabled');
        login(null,'<?=$preset_legacy_key?>', '<?=$preset_legacy_service?>', $(this).attr('id') == 'preset_remember', function() {
            $('#login_legacy_preset').hide();
            $('#login_legacy').show();
        });
    });

    $('#delete').click(function() {
        if (!confirm(<?=__j('Bist du sicher?');?>))
            return;

        game.storage.set('login','profiles',{});
        $('#custom').click();

    });

    $('#content').find('.row.iconize, label').qtip(game.render.html.qtip.help({desktop: 'left', lg: 'top'}));

    $('#profiles').hide();

    $.each(game.storage.get('login','profiles',{}), function(k,v) {
        $('#profiles, #custom').show();
        $('#custom_login').hide();

        var mugshot = $('<div class="mugshot"><span class="mugshot-fill"><i class="fa fa-spin fa-circle-o-notch"></i></span><img alt="" /><span class="mugshot-append" /></div>');
        mugshot.find('img').attr('src', v.avatar || 'media/img/mugshot.png').end().find('.mugshot-append').text(v.name);
        mugshot.find('.mugshot-fill').hide();
        mugshot.find('img').on('error',function() {
            $(this).attr('src', 'media/img/mugshot.png').off('error');
        });
        mugshot.click(function() {
            var alias = $(this);
            $('#content').find('.mugshot').addClass('disabled');
            $('#custom').addClass('btn-disabled');
            alias.find('.mugshot-fill').show();

            var ret = function(code) {
                $('#custom').removeClass('btn-disabled');
                $('#content').find('.mugshot').removeClass('disabled');
                alias.find('.mugshot-fill').hide();

                if (code == 'GRGE-0003-0003') {
                    game.render.html.notify('error',<?=__j('Die gespeicherten Login-Daten dieses Profils sind ungültig, daher wird das Icon aus dem Schnell-Login entfernt. Bitte versuche, dich über DV oder D2N einzuloggen.')?>,<?=__j('Ungültige Login-Daten')?>);
                    alias.remove();

                    var d = game.storage.get('login','profiles',{});
                    delete d[k];
                    game.storage.set('login','profiles',d);

                    if (!$.objToArray(d).length) $('#custom').click();
                    return true;
                }
            };

            if (v.key && v.host)
                login(null,v.key, v.host, true, ret);
            else if (v.token)
                login(null,v.token, 'Token', true, ret);
        });

        mugshot.insertAfter( $('#profiles h2') );
    });

    $('#custom').click(function() {
        $('#profiles').hide();
        $('#custom_login').show();
    });

    $('#confirm').click(function() {
        var key = null, user = null, service = null;

        if (login_mode_pw) {
            user = $('#login_email').val();
            key = $('#login_pass').val();
            service = 'grge';

            if (!user) {
                alert(<?=__j('Bitte gib deine E-Mail Adresse ein.');?>);
                return;
            }
            if (!key) {
                alert(<?=__j('Bitte gib dein Passwort ein.');?>);
                return;
            }

        } else {
            key = $('#key').val();
            service = $('#service').find('option:selected').val();

            if (!key) {
                alert(<?=__j('Bitte gib deinen Geheimen Schlüssel ein.');?>);
                return;
            }
        }

        var alias = $(this);
        alias.addClass('btn-disabled').find('.fa').attr('class','fa fa-spin fa-circle-o-notch');
        alias.find('#confirm-content').html(<?=__j('Bitte warten...');?>);

        $('#content').find('.form_input').attr('disabled', 'disabled');

        login(user, key, service, $('#remember').is(':checked'), function() {
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

