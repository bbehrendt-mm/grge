<?php
/**
 * @var $url
 * @var $avatar
 * @var $user
 * @var $id
 * @var $dv_id
 * @var $d2n_id
 * @var $token
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
            <div class="cell rw-2 rw-sm-12 padded left">
                <div class="framed main inline-block">
                    <img class="avatar" src="<?=$avatar ?: 'media/img/mugshot.png'?>" alt="<?=$user?>">
                </div>
            </div>
            <div class="cell rw-10 rw-sm-12 left">
                <div class="row">
                    <div class="cell rw-12 padded">
                        <span style="font-size: 20px; font-weight: bold"><?=$user?></span>
                    </div>
                </div>
                <div class="row">
                    <?php if ($dv_id >= 0 || $d2n_id >= 0) { ?>
                        <div class="cell rw-12 padded">
                            <div id="profile_sync_button" class="btn small"><?=__('Mit MotionTwin abgleichen');?></div>
                        </div>
                    <?php } ?>
                    <?php if ($avatar) { ?>
                        <div class="cell rw-12 padded">
                            <div id="profile_sync_button" class="btn small"><?=__('Profilbild entfernen');?></div>
                        </div>
                    <?php } ?>
                </div>
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
                    <div id="profile_reset_token" class="btn"><?=__('Gespeicherte Logins zurücksetzen');?></div>
                </div>
                <?php } ?>
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
        </div>
    </div>

</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //

    (function() {
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

    $('#profile_sync_button').on('click', function() {
        var popup = core.popup.spawn(500);
        var loader = core.snippets.wait();
        popup.append( loader );

        game.network.query('japi/account/sync_mt', {}, function(data) {

            if (!data['success']) {
                popup.trigger('unpop');
                game.render.html.notify('error', <?=__j('Beim Abrufen der Daten von MotionTwin ist ein Fehler aufgetreten.')?>);
                return;
            }

            popup
                .append(
                    NF.row().append( NF.cell(true, 12).append( NF.n('span', '', <?=__j('Möchtest du deine aktuellen Profilinformationen ersetzen?')?> ) ))
                ).append(
                    NF.row()
                        .append( NF.cell(true, 4, 0, 'right').append(NF.n('div', 'framed main inline-block').append($('<img src="' + data.avatar.old + '" alt="old_avatar" />'))) )
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
                )

        }, function() {
            loader.hide();
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