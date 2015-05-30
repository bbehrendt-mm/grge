<?php
/**
 * @var $url
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><span class="hide-sm"><?=__('Profileinstellungen')?></span><span class="hide-md hide-lg hide-desktop"><?=__('Einstellungen')?></span></h1>

<div class="row"><div class="cell rw-12 padded"><div class="note"><?=__('Hier kannst du einige Einstellungen für dein ZombVival-Profil vornehmen.');?></div></div></div>
<br />
<div class="row">

    <div class="cell rw-12 center">
        <h2><?=__('Login')?></h2>

    </div>
    <div class="cell rw-12">
        <div class="row">
            <div class="cell rw-6 rw-md-12 padded">
                <div id="profile_qr" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-mobile"></i></span><span id="confirm-content"><?=__('Smartphone oder Tablet verknüpfen');?></span></div>
            </div>
            <div class="cell rw-6 rw-md-12 padded">
                <div class="note">
                    <?=__('Hierdurch werden Login-Daten zu deinem Profil auf diesem sowie allen anderen PCs, auf denen sie gespeichert sind, unbrauchbar gemacht. Um dich nach Anwenden dieser Option wieder einzuloggen, musst du deinen DV oder D2N Schlüssel verwenden.');?>
                    <div id="profile_reset_token" class="btn"><?=__('Gespeicherte Logins zurücksetzen');?></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#profile_reset_token').click(function() {
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

    $('#profile_qr').click(function() {
        var popup = core.popup.spawn({desktop: 424, sm: '100%'});
        var content, qr_area;
        popup.append(
            $('<div />').addClass('row').append(
                content = $('<div />').addClass('cell rw-12 padded')
            )
        );

        content
            .append($('<h2 />').addClass('center').text(<?=__j('Login via QR')?>))
            .append($('<div />').text(<?=__j('Scanne den folgenden QR-Code oder gib die darunter stehende URL auf deinem mobilen Gerät ein. Deine Login-Daten werden dadurch auf dem Gerät gespeichert und du kannst dich zukünftig ohne die Hilfe deines PCs einloggen.')?>))
            .append($('<div />').addClass('row').append(qr_area = $('<div />').addClass('cell rw-12 padded')))
            .append($('<div />').addClass('note').text(<?=__j('Der QR-Code kann nur einmalig verwendet werden und ist für 5 Minuten gültig. Möchtest du mehrere Geräte verbinden, schließe dieses Popup und öffne es erneut, um einen neuen Code zu generieren.')?>))
            .append($('<div />').addClass('row').append($('<div />').addClass('cell rw-6 ro-6 rw-sm-12 ro-sm-0 padded').append($('<div />').addClass('btn').text(<?=__j('Schließen')?>).click(function() {popup.trigger('unpop')}))))
        ;

        qr_area.append($('<p />').addClass('center').append($('<i />').addClass('fa fa-spin fa-circle-o-notch')));

        game.network.query('japi/account/mkqr',{},function(data) {
            if (data.error || !data.pin) {
                qr_area.empty().append($('<p />').css('color','red').text(<?=__j('Abrufen des QR-Codes ist fehlgeschlagen!')?>))
            } else {
                var url = '<?=$url?>m/' + data.pin;

                qr_area.empty().append(
                    $('<img />').on('load', function() {
                        qr_area.append($('<div />').addClass('row')
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
// ## JS COMPRESS END ## //
</script>