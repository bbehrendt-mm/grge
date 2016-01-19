<?php
/**
 * @var string $username
 * @var int $pid
 * @var bool $allow
 * @var string $key
 * @var string $service
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Spieleraccounts verschmelzen')?></h1>

<div class="row">
    <div class="cell rw-8 ro-2 padded" id="merge_allow">
        <p><?=__('Möchtest du deinen :service-Account verwenden können, um dich in Zukunft bei ZombVival einzuloggen? Andere Login-Methoden in deinen ZV-Account bleiben weiterhin gültig.', [':service' => $service]);?></p>
        <div class="btn" id="confirm">
            <?=__('::b::Ja::/b::, ich möchte mich in Zukunft auch über :service einloggen können!', [':service' => $service]);?>
        </div>
    </div>
    <div class="cell rw-8 ro-2 padded" id="merge_deny">
        <p><?=__('Du kannst diesen :service-Account leider nicht automatisch mit deinem ZombVival-Account verknüpfen, da er bereits mit einem anderen ZV-Account verknüpft ist.', [':service' => $service]);?></p>
    </div>
    <div class="row">
        <div class="cell rw-8 ro-2 padded">
            <span id="cancel" class="link small"><?=__('Abbrechen');?></span>
        </div>

    </div>
</div>
<script type="application/javascript">
    // ## JS COMPRESS BEGIN ## //
    <?php if ($allow) { ?>
        $('#merge_deny').hide();
    <?php } else { ?>
        $('#merge_allow').hide();
    <?php } ?>

    $('#cancel').click(function() {
        game.network.load('lobby/main')
    });

    $('#confirm').click(function() {
        $(this).addClass('disabled');
        game.network.query('japi/account/merge', {key: '<?=$key?>', service: '<?=$service?>'}, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                $('#confirm').removeClass('disabled');
            } else {
                $('#confirm').removeClass('disabled');

                if (data.success) {
                    game.network.load('lobby/main');
                    game.render.html.notify('smile', game.i18n('<?=__j('Herzlichen Glückwunsch! Du kannst dich jetzt auch über :service in deinen ZombVival-Account einloggen.');?>', {':service': '<?=$service?>'}), '<?=__j('Verknüpfung erfolgreich!');?>', 4000);
                } else
                    game.render.html.notify('smile', game.i18n('<?=__j('Die Verknüpfung deines ZombVival-Accounts mit :service ist fehlgeschlagen...');?>', {':service': '<?=$service?>'}), '<?=__j('Fehlgeschlagen');?>', 4000);
            }
        });
    });
    // ## JS COMPRESS END ## //
</script>
