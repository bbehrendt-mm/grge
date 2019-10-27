<?php
/**
 * @var $url
 * @var $avatar
 * @var $user
 * @var $user_c
 * @var $mentor
 * @var $fake
 * @var $id
 * @var $dv_id
 * @var $d2n_id
 * @var $token
 * @var $mail
 * @var $pw_stage
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><span class="hide-sm"><?=__('Profileinstellungen')?></span><span class="hide-md hide-lg hide-desktop"><?=__('Einstellungen')?></span></h1>

<div class="row"><div class="cell rw-12 padded"><div class="note"><?=__('Hier kannst du einige Einstellungen für dein ZombVival-Profil vornehmen.');?></div></div></div>
<br />



<ul class="tabline">
    <li data-controls="settings_avatar" class="active"><?=__('Avatar & Name')?></li>
    <li data-controls="settings_login" class=""><?=__('Login')?></li>
    <li data-controls="settings_ui" class=""><?=__('Benutzeroberfläche')?></li>
</ul>

<div class="row settings-tab" id="settings_avatar">
    <div class="cell rw-12">
        <div class="row">
            <div class="padded cell ro-3 rw-6 ro-lg-2 rw-lg-8 ro-md-1 rw-md-10 ro-sm-0 rw-sm-12">
                <div class="flatbox zv-license">
                    <div class="row">
                        <div class="cell rw-12">
                            <h3><?=__('ZombVival SurvivorID');?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell rw-4 padded left">
                            <div class="zv-license-pic">
                                <img id="av_img" class="avatar" src="<?=$avatar ?: 'media/img/mugshot.png'?>" alt="<?=$user?>">
                            </div>
                        </div>
                        <div class="cell rw-8 padded left">
                            <div class="row">
                                <div class="cell rw-12 padded">
                                    <span id="av_name" class="zv-license-main"><?=$user?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell rw-12 padded">
                                    <span id="av_mentor" class="zv-license-info">
                                        <b><?=__('Bürgerlicher Name');?>:</b><br />
                                        <?=$fake?><br/>
                                        &lt;&lt;<?=$mentor?><?=str_pad(str_pad($id,3,"0",STR_PAD_LEFT),8,"<",STR_PAD_LEFT)?>&lt;&lt;O8L5
                                        <?php if ($user !== $user_c) { ?><br/><?=$user_c?><?php } ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <br /><br />
    <div class="cell rw-12">
        <div class="row">
            <div class="cell rw-12">
                <h2><?=__('Aktionen')?></h2>
            </div>
            <?php if ($dv_id >= 0 || $d2n_id >= 0) { ?>
                <div class="padded cell rw-3 rw-lg-4 rw-md-6 rw-sm-12">
                    <div id="profile_sync_button" class="btn"><?=__('Mit MotionTwin abgleichen');?></div>
                </div>
            <?php } ?>

            <div class="padded cell rw-3 rw-lg-4 rw-md-6 rw-sm-12">
                <div id="profile_addav_button" class="btn"><?=__('Profilbild ändern');?></div>
            </div>
        </div>
    </div>
</div>

<div class="row settings-tab" id="settings_login">
    <div class="cell rw-12">
        <div class="row">
            <div class="cell rw-6 rw-md-12 padded">
                <div class="row">
                    <div class="cell rw-12"><b><?=__('Verknüpfte Accounts')?></b></div>
                    <?php if ($dv_id >= 0) {?>
                        <div class="cell ro-1 ro-md-0 rw-11 rw-md-12 padded">Die Verdammten <span class="small">(<?=__('ID-Nr.')?> <?=$dv_id?>)</span></div>
                    <?php } ?>
                    <?php if ($d2n_id >= 0) {?>
                        <div class="cell ro-1 ro-md-0 rw-11 rw-md-12 padded">Die2Nite <span class="small">(<?=__('ID-Nr.')?> <?=$d2n_id?> )</span></div>
                    <?php } ?>
                    <div class="cell rw-12 padded">
                        <div id="profile_qr" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-mobile"></i></span><span id="confirm-content"><?=__('Smartphone oder Tablet verknüpfen');?></span></div>
                    </div>
                </div>
            </div>
            <div class="cell rw-6 rw-md-12 padded">
                <?php if ($token) {?>
                <div class="note">
                    <?=__('Hierdurch werden Login-Daten zu deinem Profil auf diesem sowie allen anderen PCs, auf denen sie gespeichert sind, unbrauchbar gemacht. Um dich nach Anwenden dieser Option wieder einzuloggen, musst du deinen DV oder D2N Schlüssel verwenden.');?>
                </div>
                <div id="profile_reset_token" class="btn"><?=__('Gespeicherte Logins zurücksetzen');?></div>
                <?php } ?>
            </div>

        </div>

        <div class="row">
            <div class="cell rw-12"><b><?=__('Passwort-Login')?></b></div>
            <div class="cell rw-6">

                <div id="pass_email_form">
                    <div class="row">
                        <div class="cell padded rw-6 left">
                            <b><?=__('Deine E-Mail Adresse: ');?></b><br />
                            <i class="fa fa-envelope-o"></i>
                            <span id="email_content"><?=$mail?></span>
                        </div>
                        <div class="cell padded rw-6 right">
                            <i class="fa fa-trash-o"></i> <a id="profile_cancel_pw" href="#"><?=__('Löschen');?></a>
                        </div>
                    </div>

                </div>

                <div id="pass_create_form">
                    <div class="row iconize" title="<?=__('Bitte gib deine E-Mail Adresse hier ein. Deine Adresse wird ausschließlich für den Login verwendet.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-envelope"></i></div>
                        <div class="padded cell rw-11"><input id="email" type="email" autocomplete="on" class="form_input" placeholder="<?=__('Deine E-Mail Adresse');?>" /></div>
                    </div>
                    <div class="row iconize" title="<?=__('Gib dein Passwort ein.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-key"></i></div>
                        <div class="padded cell rw-11"><input id="pass1" type="password" autocomplete="off" class="form_input" placeholder="<?=__('Passwort');?>" /></div>
                    </div>
                    <div class="row iconize" title="<?=__('Gib dein Passwort erneut ein.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-key"></i></div>
                        <div class="padded cell rw-11"><input id="pass2" type="password" autocomplete="off" class="form_input" placeholder="<?=__('Passwort wiederholen');?>" /></div>
                    </div>
                    <div class="row">
                        <div class="padded cell rw-6 ro-6 rw-lg-8 ro-lg-4 rw-md-10 ro-md-2 rw-sm-12 ro-sm-0">
                            <div id="profile_add_pw" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-check"></i></span><span><?=__('OK');?></span></div>
                        </div>
                    </div>
                </div>

                <div id="pass_activate_form">
                    <div class="row iconize" title="<?=__('Bitte gib den Aktivierungsschlüssel ein, den du per E-Mail erhalten hast.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-key"></i></div>
                        <div class="padded cell rw-11"><input id="atoken" type="text" autocomplete="off" class="form_input" placeholder="<?=__('Aktivierungsschlüssel');?>" /></div>
                    </div>
                    <div class="row">
                        <div class="padded cell rw-6 rw-lg-4 rw-md-12">
                            <a id="profile_resend_atoken" href="#"><?=__('Aktivierungsschlüssel erneut senden');?></a>
                        </div>
                        <div class="padded cell rw-6 rw-lg-8 rw-md-12">
                            <div id="profile_activate_pw" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-check"></i></span><span><?=__('OK');?></span></div>
                        </div>
                    </div>
                </div>

                <div id="pass_alter_form">
                    <div class="row iconize" title="<?=__('Gib dein altes Passwort ein.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-key"></i></div>
                        <div class="padded cell rw-11"><input id="pass_old" type="password" autocomplete="off" class="form_input" placeholder="<?=__('Altes Passwort');?>" /></div>
                    </div>
                    <div class="row iconize" title="<?=__('Gib ein neues Passwort ein.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-key"></i></div>
                        <div class="padded cell rw-11"><input id="pass_new1" type="password" autocomplete="off" class="form_input" placeholder="<?=__('Neues Passwort');?>" /></div>
                    </div>
                    <div class="row iconize" title="<?=__('Gib dein neues Passwort erneut ein.');?>">
                        <div class="padded cell rw-1"><i class="fa fa-key"></i></div>
                        <div class="padded cell rw-11"><input id="pass_new2" type="password" autocomplete="off" class="form_input" placeholder="<?=__('Neues Passwort wiederholen');?>" /></div>
                    </div>
                    <div class="row">
                        <div class="padded cell rw-8 ro-4 rw-lg-10 ro-lg-2 rw-md-12 ro-md-0">
                            <div id="profile_alter_pw" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-check"></i></span><span><?=__('Passwort ändern');?></span></div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="cell rw-6 padded">
                <div class="note">
                    <?=__('Wenn du dich in Zukunft gerne mit deiner E-Mail Adresse und einem Passwort anmelden möchtest, kannst du diese Daten hier eintragen. Der Login über DV/D2N wird danach dennoch weiterhin möglich sein.')?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row settings-tab" id="settings_ui">

    <div class="cell rw-10 ro-1 rw-lg-12 ro-lg-0">
        <div class="row">
            <div class="cell rw-4 rw-md-12 padded">

                <label for="quality"><b><?=__('Darstellungsqualität');?></b></label><br />
                <select id="quality" data-associated-setting data-handler="quality" data-default="3">
                    <option value="3"><?=__('Hoch');?></option>
                    <option value="2"><?=__('Mittel');?></option>
                    <option value="1"><?=__('Niedrig');?></option>
                </select>
                <div data-help-for="quality">
                    <?=__('Durch die Verringerung der Darstellungsqualität werden bestimmte grafische Effekte deaktiviert, und die Leistung auf Geräten mit schwächerer Hardware zu verbessern.');?>
                </div>
            </div>

            <div class="cell rw-4 rw-md-12 padded">

                <label for="input"><b><?=__('Eingabegerät');?></b></label><br />
                <select id="input" data-associated-setting="input-device"  data-default="auto">
                    <option value="auto"><?=__('Automatisch');?></option>
                    <option value="mouse"><?=__('Maus');?></option>
                    <option value="touch"><?=__('Touchscreen');?></option>
                </select>
                <div data-help-for="input">
                    <?=__('Die Benutzeroberfläche erkennt normalerweise automatisch, ob du mit Maus oder Touchscreen spielst, und optimiert die Eingabefunkionen dementsprechend. Sollte das bei dir nicht funktionieren (z.B. weil du ein Gerät nutzt, das sowohl über eine Maus, als auch einen Touchscreen verfügt), kannst du die automatische Erkennung außer Kraft setzen.');?>
                </div>
            </div>

            <div class="cell rw-4 rw-md-12 padded">
                <label for="news"><b><?=__('Nach dem Login');?></b></label><br />
                <select id="news" data-associated-setting="show-news"  data-default="news">
                    <option value="news"><?=__('Zur Neuigkeiten-Seite');?></option>
                    <option value="game"><?=__('Direkt zum Spiel');?></option>
                </select>
                <div data-help-for="news">
                    <?=__('Hier kannst du einstellen, welche Seite nach dem Login aufgerufen werden soll. Auch wenn du die Option "Direkt zum Spiel" aktiviert hast, wirst du einmal pro Tag auf die Neuigkeiten-Seite geleitet.');?>
                </div>
            </div>
        </div>

        <br /><br />

        <div class="row">
            <div class="cell rw-4 rw-md-12 padded">
                <label for="heroic_ui"><b><?=__('Anzeige der Heldentaten');?></b></label><br />
                <select id="heroic_ui" data-associated-setting="heroid_ui"  data-default="inline">
                    <option value="inline"><?=__('Im Inventar-Tab');?></option>
                    <option value="tab"><?=__('In eigenem Tab');?></option>
                </select>
            </div>

            <div class="cell rw-4 rw-md-12 padded">
                <label for="travel_confirm"><b><?=__('Bestätigung beim Reisen');?></b></label><br />
                <select id="travel_confirm" data-associated-setting="travel_confirm"  data-default="always">
                    <option value="always"><?=__('Immer bestätigen');?></option>
                    <option value="auto"><?=__('Nur bei Gefahr bestätigen');?></option>
                </select>
            </div>

            <div class="cell rw-4 rw-md-12 padded">
                <label for="log_time_mode"><b><?=__('Zeitangabe in Log-Einträgen');?></b></label><br />
                <select id="log_time_mode" data-associated-setting="log_time_mode"  data-default="rt">
                    <option value="rt"><?=__('Echte Uhrzeit');?></option>
                    <option value="gt"><?=__('Spielzeit');?></option>
                </select>
            </div>

            <div class="cell rw-4 rw-md-12 padded">
                <label for="show_room_actions"><b><?=__('Aktionsanzeige für Räume');?></b></label><br />
                <select id="show_room_actions" data-associated-setting="show_room_actions"  data-default="main">
                    <option value="main"><?=__('Auf Hauptseite & Raumübersicht');?></option>
                    <option value="popup"><?=__('Nur in Raumübersicht');?></option>
                </select>
            </div>
        </div>
    </div>


</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //

    (function() {
        $('#content').find('.row.iconize, label').qtip(game.render.html.qtip.help({desktop: 'right', lg: 'top'}));

        if ( !'<?=$mail?>' ) $('#pass_email_form').hide();
        $('#pass_create_form, #pass_activate_form, #pass_alter_form').hide();
        switch (<?=$pw_stage?>) {
            case 0:
                $('#pass_create_form').show();
                break;
            case 1:
                $('#pass_activate_form').show();
                break;
            case 2:
                $('#pass_alter_form').show();
                break;
            default: break;
        }

        console.log(<?=$pw_stage?>);

        var tabline = $('.tabline');
        var tabs = $('.settings-tab');

        tabline.children('li').on('click',function () {
            if ($(this).hasClass('active')) return;

            var target = $(this).data('controls');
            tabs.not('#' + target).slideUp();
            tabs.filter('#' + target).slideDown();
            $('.tabline').children('li').removeClass('active');
            $(this).addClass('active');
        });
        tabs.hide();
        tabs.filter( '#' + tabline.children('li:first-child').data('controls') ).show();
    })();

    $('#profile_addav_button').on('click', function() {
        var popup = core.popup.spawn({desktop: 700, lg:'100%'});

        var file_selector, file_name_box, file_name, file_info_box, file_type, file_res, file_size, file_type_icon, file_size_icon, file_res_icon;

        var preview_box, preview_image;
        var btn_confirm, btn_cancel;

        var file_content, file_mime;

        popup.append($('<form/>')
            .append(
                NF.row()
                    .append( NF.cell(true,12).append( NF.info()
                            .append(NF.n('p','',<?=__j('Hier kannst du ein eigenes Profilbild hochladen. Dein Bild sollte quadratisch sein und mindestens eine Auflösung 100x100 Pixeln haben.')?>))
                            .append(NF.n('p','',<?=__j('Unterstützt werden Bilder in den Formaten GIF, JPEG, PNG, BMP und WebP mit einer Dateigröße von maximal :size.', [':size' => '3 MiB'])?>))
                            .append(NF.n('p','',<?=__j('Nachdem du ein Bild ausgewählt hast, wird dir eine Vorschau des Bildes angezeigt. Je nach Dateityp und Qualität des Bildes unterscheidet sich diese Vorschau möglicherweise geringfügig von dem finalen Bild, welches der Server nach dem Hochladen berechnet und abspeichert.')?>))
                    ) )
                    .append( NF.cell(true,12)
                        .append(NF.row()
                            .append(NF.cell(true,{desktop:6,md:12},{desktop:3,md:0}).append(
                                $('<label for="av_file_sel" />').append(NF.button(<?=__j('Bild auswählen')?>, false, 'image'))
                            ))
                            .append(file_name_box = NF.cell(false,12).append(NF.n('div','flatbox small center').append(
                                NF.row().append(NF.cell(true,12).append(file_name = NF.n('div').css({'word-wrap': 'break-word'})))
                            )))
                        )
                        .append(NF.row().append(NF.cell(false,12).append(file_selector = NF.input('file',null,{id: "av_file_sel", name: "av_file_sel", accept: ".gif,.jpg,.jpeg,.jif,.jfif,.png,.webp,.bmp"}))))
                        .append(file_info_box = NF.n('div','flatbox').append(NF.row()
                            .append(NF.cell(true,5).append(NF.n('span','b',<?=__j('Dateityp')?>)))
                            .append(NF.cell(true,5).append(file_type = NF.n('div')))
                            .append(NF.cell(true,2).append(file_type_icon = NF.n('div','right')))

                            .append(NF.cell(true,5).append(NF.n('span','b',<?=__j('Größe')?>)))
                            .append(NF.cell(true,5).append(file_size = NF.n('div')))
                            .append(NF.cell(true,2).append(file_size_icon = NF.n('div','right')))

                            .append(NF.cell(true,5).append(NF.n('span','b',<?=__j('Auflösung')?>)))
                            .append(NF.cell(true,5).append(file_res = NF.n('div')))
                            .append(NF.cell(true,2).append(file_res_icon = NF.n('div','right')))
                        ))
                    )

            ).append(
                preview_box = NF.row()
                    .append(NF.cell(true,6)
                        .append(NF.row()
                            .append(NF.cell(true,12,0,'center')
                                .append(NF.n('div','framed main inline-block').append(preview_image = NF.n('img','avatar')))
                            )
                        )
                    ).append(NF.cell(true,6)
                        .append(btn_confirm = NF.button(<?=__j('Bild hochladen')?>, false, 'upload'))
                        .append(btn_cancel = NF.button(<?=__j('Abbrechen' )?>, false, 'times'))
                    )
            )
        );

        btn_confirm.addClass('disabled');
        btn_cancel.on('click', function() {popup.trigger('unpop');});

        btn_confirm.on('click', function() {
            popup.addClass('disabled');
            game.network.query('japi/account/upload_avatar', {data: btoa(file_content), mime: file_mime}, function(data) {
                popup.removeClass('disabled');

                if (!data.success) {

                    if (data.error)
                        alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                    else
                        game.render.html.notify('error', <?=__j('Beim Aktualisieren der Daten ist ein Fehler aufgetreten.')?>);
                } else {
                    game.render.html.notify('success', <?=__j('Dein Profil wurde aktualisiert.')?>);
                    $('#av_img')
                        .attr('src', '')
                        .attr('src', 'cdn/avatar/' + data.access + '?' + new Date().getTime());
                    popup.trigger('unpop');
                }

            });
        });

        preview_image.on('load', function() {
            var w = preview_image[0].naturalWidth;
            var h = preview_image[0].naturalHeight;

            file_res.text( w + ' x ' + h );

            var unlock = true;
            var perfect = false;

            if (h === w && h >= 100)
                perfect = true;
            else if ( h < 16 || w < 16 )
                unlock = false;
            else if ( h / w < 0.1 || w / h < 0.1 )
                unlock = false;

            if (unlock) {
                preview_box.show();
                btn_confirm.removeClass('disabled');
            }

            file_res_icon.empty().append(NF.fa(unlock ? ( perfect ? 'check-circle' : 'minus-circle' ) : 'times-circle'));
        });
        preview_image.on('error', function() {
            file_content = file_mime = null;
            file_type.text(<?=__j('Unbekannt')?>);
            file_type_icon.empty().append(NF.fa('times-circle'));
        });

        preview_box.hide();
        file_info_box.hide();
        file_name_box.hide();
        file_selector.hide();
        file_selector.on('change', function(e) {

            file_content = file_mime = null;

            preview_box.hide();
            btn_confirm.addClass('disabled');

            var files = e.target.files;
            if (files.length !== 1) return;
            var file = files[0];

            file_name.text(file.name);
            file_name_box.show();
            file_info_box.show();

            var valid_filetype = true;
            var type_info = file.type.split('/',2);
            if (type_info.length < 2 || type_info[0] !== 'image') {
                valid_filetype = false;
                type_info = ['unknown','unknown'];
            }

            var size_info;
            if      (file.size >= 1073741824) size_info = [Math.round(file.size/107374182.4)/10,'GiB'];
            else if (file.size >=    1048576) size_info = [Math.round(file.size/   104857.6)/10,'MiB'];
            else if (file.size >=       1024) size_info = [Math.round(file.size/       1024)   ,'KiB'];
            else                              size_info = [           file.size                ,'B'  ];

            var valid_filesize = file.size > 0 && file.size <= 3145728; //3MB

            file_type.text( type_info[1] );
            file_type_icon.empty().append(NF.fa(valid_filetype ? 'check-circle' : 'times-circle'));
            file_size.text( size_info[0] + ' ' + size_info[1]);
            file_size_icon.empty().append(NF.fa(valid_filesize ? 'check-circle' : 'times-circle'));
            file_res.text( '???' );
            file_res_icon.empty().append(NF.fa('times-circle'));

            if (valid_filesize && valid_filetype) {

                var reader = new FileReader();
                reader.onload = function(e) {
                    file_content = e.target.result;
                    file_mime = type_info[1];
                    preview_image.attr('src', 'data:image/' + type_info[1] + ';base64,' + btoa(e.target.result));
                };
                reader.readAsBinaryString(file);
            }
        });
    });

    $('#profile_sync_button').on('click', function() {
        var popup = core.popup.spawn(500);
        var loader = core.snippets.wait();
        popup.append( loader );

        game.network.query('japi/account/sync_mt', {}, function(data) {

            if (!data.success) {
                popup.trigger('unpop');
                game.render.html.notify('error', <?=__j('Beim Abrufen der Daten von MotionTwin ist ein Fehler aufgetreten.')?>);
                return;
            }

            if (data.avatar.old === data.avatar.new && data.name.old === data.name.new) {
                popup.trigger('unpop');
                game.render.html.notify('success', <?=__j('Deine Profil ist aktuell. Es ist keine Synchronisierung nötig.')?>);
                return;
            }

            var pp_close, pp_accept;

            popup
                .append(
                    NF.row().append( NF.cell(true, 12).append( NF.n('span', 'b', <?=__j('Möchtest du deine aktuellen Profilinformationen ersetzen?')?> ) ))
                ).append(
                    NF.row().append( NF.cell(true, 12).append( NF.info(<?=__j('Wenn du dich derzeit in einem Spiel befindest und deinen Namen änderst, wirst du in deinem aktuellen Spiel weiterhin mit deinem alten Namen angezeigt werden.')?> ) ))
                ).append(
                    NF.row()
                        .append( NF.cell(true, 4, 0, 'right').append(NF.n('div', 'framed main inline-block').append($('<img class="avatar" src="' + data.avatar.old + '" alt="old_avatar" />'))) )
                        .append( NF.cell(true, 8, 0, 'left')
                            .append(NF.n('div', 'b', data.name.old))
                            .append(NF.n('div', 'i', <?=__j('Dies ist dein aktuelles, von ZombVival gespeichertes Profil.')?>))
                        )
                ).append(
                    NF.row().append( NF.cell(true, 12, 0, 'center').append( NF.fa('arrow-down') ))
                ).append(
                    NF.row()
                        .append( NF.cell(true, 4, 0, 'right').append(NF.n('div', 'framed main inline-block').append($('<img src="' + data.avatar.new + '" alt="old_avatar" />'))) )
                        .append( NF.cell(true, 8, 0, 'left')
                            .append(NF.n('div', 'b', data.name.new))
                            .append(NF.n('div', 'i', <?=__j('Dies sind deine aktuellen Profildaten, die bei MotionTwin gespeichert sind.')?>))
                        )
                ).append(
                    NF.row()
                        .append( NF.cell(true, 5, 0).append( pp_close = NF.button(<?=__j('Abbrechen')?>, false, 'fa-times') ) )
                        .append( NF.cell(true, 5, 2).append( pp_accept = NF.button(<?=__j('Ersetzen')?>, false, 'fa-check') ) )
                );

            pp_close.on('click', function() { popup.trigger('unpop'); });
            pp_accept.on('click', function() {
                pp_close.addClass('disabled');
                pp_accept.addClass('disabled');

                game.network.query('japi/account/sync_mt', {control: data.control}, function(data) {
                    pp_close.removeClass('disabled');
                    pp_accept.removeClass('disabled');
                    if (data.success) {
                        game.render.html.notify('success', <?=__j('Dein Profil wurde aktualisiert.')?>);
                        popup.trigger('unpop');

                        $('#av_img').attr('src', data.avatar.new ? data.avatar.new : 'media/img/mugshot.png');
                        $('#av_name').html( data.name.new );

                        var profiles = game.storage.get('login','profiles',{});
                        if (profiles && profiles[data.id]) {
                            profiles[data.id].name   = data.name.new;
                            profiles[data.id].avatar = data.avatar.new;
                        }
                        game.storage.set('login','profiles',profiles);

                    } else game.render.html.notify('error', <?=__j('Beim Aktualisieren der Daten ist ein Fehler aufgetreten.')?>);
                });
            });

        }, function() {
            loader.hide();
        })

    });

    $('#profile_add_pw').on('click', function() {
        var mail = $('#email').val();
        var pass1 = $('#pass1').val();
        var pass2 = $('#pass2').val();

        if (!mail || !pass1 || !pass2) {
            game.render.html.notify('error', <?=__j('Bitte fülle alle Felder aus.')?>);
            return;
        }

        if (pass1 !== pass2) {
            game.render.html.notify('error', <?=__j('Die eingegebenen Passwörter stimmen nicht überein.')?>);
            return;
        }

        if (pass1.length < 5) {
            game.render.html.notify('error', <?=__j('Dein Passwort muss aus mindestens 5 Zeichen bestehen.')?>);
            return;
        }

        $('#content').addClass('disabled');
        game.network.query('japi/account/make_pw', {email: mail, pass: pass1}, function(data) {
            $('#content').removeClass('disabled');

            if (!data.success) {
                if (data.error && data.error === 'email') {
                    game.render.html.notify('error', <?=__j('Die eingegebene E-Mail Adresse ist ungültig.')?>);
                    return;
                }

                game.render.html.notify('error', <?=__j('Beim Verarbeiten der Daten ist ein Fehler aufgetreten.')?>);
                return;
            }

            game.render.html.notify('info', <?=__j('Wir haben einen Aktivierungscode an deine E-Mail Adresse geschickt. Bitte gib diesen Code ein, um deine Zugangsdaten freizuschalten.')?>, <?=__j('Sie haben Post!')?>);
            $('#email_content').text( mail );

            $('#pass_create_form').slideUp(100);
            $('#pass_email_form').slideDown(100);
            $('#pass_activate_form').slideDown(100);

        }, function() {
            $('#profile_add_pw').removeClass('disabled');
        })
    });

    $('#profile_activate_pw').on('click', function() {
        var token = $('#atoken').val();

        if (!token) {
            game.render.html.notify('error', <?=__j('Bitte fülle alle Felder aus.')?>);
            return;
        }

        $('#content').addClass('disabled');
        game.network.query('japi/account/activate_pw', {t: token}, function(data) {
            $('#content').removeClass('disabled');

            if (!data.success) {
                game.render.html.notify('error', <?=__j('Beim Verarbeiten der Daten ist ein Fehler aufgetreten.')?>);
                return;
            }

            game.render.html.notify('success', <?=__j('Deine Zugangsdaten wurden freigeschaltet.')?>);

            $('#pass_activate_form').slideUp(100);
            $('#pass_alter_form').slideDown(100);

        }, function() {
            $('#content').removeClass('disabled');
        })
    });

    $('#profile_cancel_pw').on('click', function() {
        if (!confirm(<?=__j('Bist du sicher, dass du die Verknüpfung mit deiner E-Mail Adresse aufheben möchtest? Du kannst dich dann nicht länger über deine E-Mail Adresse anmelden.')?>))
            return;

        $('#content').addClass('disabled');
        game.network.query('japi/account/cancel_pw', {}, function(data) {
            $('#content').removeClass('disabled');

            if (!data.success) {
                game.render.html.notify('error', <?=__j('Beim Verarbeiten der Daten ist ein Fehler aufgetreten.')?>);
                return;
            }

            game.render.html.notify('info', <?=__j('Deine E-Mail Adresse und Passwort wurden aus der Datenbank entfernt.')?>);

            $('#pass_activate_form').slideUp(100);
            $('#pass_alter_form').slideUp(100);
            $('#pass_email_form').slideUp(100);
            $('#pass_create_form').slideDown(100);

        }, function() {
            $('#content').removeClass('disabled');
        })
    });

    $('#profile_reset_token').on('click',function() {
        if (!confirm(<?=__j('Bist du sicher?')?>)) return;

        $('#content').addClass('disabled');
        game.network.query('japi/account/remove_tokens', {}, function(data) {
            $('#content').removeClass('disabled');
            if (data.redirect) {
                game.render.html.notify('success', <?=__j('Alle gespeicherten Login-Informationen wurden entwertet. Du kannst dich weiterhin über DV/D2N in dein ZV-Profil einloggen.')?>);
                game.network.load(data.redirect);
            }
        }, function() {
            $('#content').removeClass('disabled');
        })
    });

    $('#profile_qr').on('click', function() {
        var popup = core.popup.spawn({desktop: 424, sm: '100%'});
        var content, qr_area;
        popup.append(
            NF.row().append(
                content = $('<div />').addClass('cell rw-12 padded')
            )
        );

        content
            .append($('<h2 />').addClass('center').text(<?=__j('Login via QR')?>))
            .append($('<div />').text(<?=__j('Scanne den folgenden QR-Code oder gib die darunter stehende URL auf deinem mobilen Gerät ein. Deine Login-Daten werden dadurch auf dem Gerät gespeichert und du kannst dich zukünftig ohne die Hilfe deines PCs einloggen.')?>))
            .append(NF.row().append(qr_area = $('<div />').addClass('cell rw-12 padded')))
            .append($('<div />').addClass('note').text(<?=__j('Der QR-Code kann nur einmalig verwendet werden und ist für 5 Minuten gültig. Möchtest du mehrere Geräte verbinden, schließe dieses Popup und öffne es erneut, um einen neuen Code zu generieren.')?>))
            .append(NF.row().append($('<div />').addClass('cell rw-6 ro-6 rw-sm-12 ro-sm-0 padded').append($('<div />').addClass('btn').text(<?=__j('Schließen')?>).click(function() {popup.trigger('unpop')}))))
        ;

        qr_area.append($('<p />').addClass('center').append($('<i />').addClass('fa fa-spin fa-circle-o-notch')));

        game.network.query('japi/account/mkqr',{},function(data) {
            if (data.error || !data.pin) {
                qr_area.empty().append($('<p />').css('color','red').text(<?=__j('Abrufen des QR-Codes ist fehlgeschlagen!')?>))
            } else {
                var url = '<?=$url?>m/' + data.pin;

                qr_area.empty().append(
                    $('<img />').on('load', function() {
                        qr_area.append(NF.row()
                                .append($('<div />').addClass('cell rw-12 right padded').css({'font-size': 20})
                                    .append($('<span />').text('<?=$url?>m/'))
                                    .append($('<span />').text(data.pin).css({'font-size': 25, 'font-weight': 'bold'}))
                                )
                        );
                    }).css('width','100%').attr('src','https://chart.googleapis.com/chart?cht=qr&chs=400x400&chl=' + encodeURIComponent(url) + '&chld=M|1')
                )
            }
        });
    });


    $('[data-associated-setting]').each(function() {
        var setting = $(this).data('associated-setting');
        var id = $(this).attr('id');
        $(this).val(($(this).data('handler') && game.s[$(this).data('handler')]) ? game.s[$(this).data('handler')]() : game.storage.get('settings',setting,$(this).data('default'))).change(function() {
            if ($(this).data('handler') && game.w[$(this).data('handler')])
                game.w[$(this).data('handler')]($(this).val());
            else game.storage.set('settings',setting,$(this).val());
            game.render.html.notify('success', <?=__j('Die Änderungen wurden gespeichert.')?>)
        });

        var label = $(this).siblings('label[for="' + id + '"]');
        var help = $(this).siblings('div[data-help-for="' + id + '"]');

        if (label.length && help.length) {
            label.prepend(NF.fa('info-circle').attr('title','-').qtip(game.render.html.qtip.help('bottom', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content').empty();

                    content
                        .append($('<b />').text(label.text()))
                        .append($('<div />').text(help.text()));
                }
            })));
            help.remove();
        }

    });

    $('#content').find('select').selectric();
// ## JS COMPRESS END ## //
</script>