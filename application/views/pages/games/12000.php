<?php
/**
 * @var string $mode_name Game mode name
 * @var array $linked_games
 * @var array $database
 * @var bool $midness
 * @var string $default_job
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><span class="hide-sm hide-md"><?=__('Spezial-Modus: :name', [':name' => __($mode_name)])?></span><span class="hide-lg hide-desktop"><?=__($mode_name);?></span></h1>

<div class="row" data-preselect="1">

    <div class="cell rw-6 rw-md-12 padded">

        <div class="help noclick">
            <h4><?=__($mode_name);?></h4>
            <?=__('In diesem Modus spielst du zusammen mit den anderen Bürgern deiner aktuellen DieVerdammten oder Die2Nite Stadt. Dein Beruf bei dieser Partie wird automatisch anhand deines Berufes in der Stadt festgelegt.');?><br />
            <?=__('Es kann nicht mehr als eine Partie pro Stadt gleichzeitig laufen. Wird die Partie jedoch beendet, kann mit derselben Stadt eine neue Partie gestartet werden.');?>
            <?=__('Stirbst du während der Partie in DV bzw. D2N, so hat dies keinen Einfluss auf die Partie.');?>
        </div>

    </div>

    <div class="cell rw-6 rw-md-12 padded">
        <b><?=__('Verfügbare Partien');?></b>

        <div class="row">
            <?php foreach ($linked_games as $service => $game) { ?>

                <div data-service="<?=$service?>" class="cell rw-12 padded">
                    <?php if ($game === null || is_numeric($game)) { ?>
                        <div class="hotbox disabled">
                            <b class="head"><?=$service?></b>
                            <i class="subtitle">
                                <?php if ($game === null) { ?>
                                    <?=__('Dein ZV-Profil ist nicht mit :service verknüpft, daher kann deine aktuelle Stadt nicht abgerufen werden.', [':service' => $service]);?>
                                <?php } elseif ($game === -1) { ?>
                                    <?=__('Du spielst bei :service gerade in keiner Stadt.', [':service' => $service]);?>
                                <?php } else { ?>
                                    <?=__('Beim Abrufen deiner aktuellen Stadt von :service ist ein Fehler aufgetreten. Bitte versuche es später erneut.', [':service' => $service]);?>
                                <?php } ?>
                            </i>
                        </div>
                    <?php } else { ?>
                        <div data-setservice="<?=$service?>" data-setsign="<?=$game['sign']?>" class="<?=$game['locked'] ? 'hotbox disabled' : 'hotbox special' ?>">
                            <b class="head"><?=$game['name']?></b>
                            <i class="subtitle"><?=$service?>, <?=__(($game['players'][0] === $game['players'][1]) ? ':num1 Spieler' : ':num1 Spieler, :num2 lebendig', [':num1' => $game['players'][1], ':num2' => $game['players'][0]]);?></i>
                        </div>
                    <?php } ?>
                </div>

            <?php } ?>
        </div>
    </div>
</div>

<div class="row" data-confirm="1">
    <h2><?=__('Dein Beruf');?></h2>
    <div class="cell rw-10 ro-1 rw-md-12 ro-md-0 padded">
        <div class="note"><?=__('Dein Beruf wird automatisch festgelegt.');?> <a href="#" data-showall="1"><?=__('Alle Berufe zeigen.');?></a></div>
        <?php foreach ($database['jobs'] as $jid => $data) { ?>
            <div class="row" data-sign="<?=$data['meta']['sign']?>">
                <div class="padded cell rw-2 rw-sm-8 left">
                    <b><?=__($data['meta']['name'])?></b><br />
                    <i><?=__(':num SP', [':num' => $data['points']])?></i>
                </div>
                <div class="padded cell rw-8 rw-sm-0 justify"><?=__($data['meta']['caption'])?></div>
                <div class="padded cell rw-2 rw-sm-4 center" data-job="<?=$jid?>">
                    <?php if ($data['locked']) { ?>
                        <img src="media/icons/lock.gif" alt="x" />
                    <?php } else if ($midness) { ?>
                        <img alt="" src="media/icons/midness.gif">
                    <?php } else if (count($data['levels']) === 0) { ?>
                        <img src="media/icons/silverstar.gif" alt="+" />
                    <?php } elseif (count($data['levels']) + 1 === $data['level']) { ?>
                        <img src="media/icons/superstar.gif" alt="++" />
                    <?php } else for ($i = 0; $i < $data['level']; $i++) { ?>
                        <img src="media/icons/star.gif" alt="*" />
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <div class="note" data-jobfail="1"><b><?=__('Entschuldige!');?></b> <?=__('Dein Stadtberuf konnte nicht ausgelesen werden. Du musst daher leider als Bürger spielen.');?></div>
    </div>
</div>
<div class="row" data-confirm="1">
    <h2><?=__('Bestätigen');?></h2>
    <?php
    $question = Tool_Gambling::select([
        'Die Zombies warten schon sehnsüchtig auf dich.', 'Es ist Fütterungszeit...', 'Du hängst doch nicht wirklich an deinem Leben, oder?',
        'Mario, die Prinzessin wurde entführt! .... Oh halt, falsches Spiel.', 'Ist das eine Machete in deiner Hose, oder freust du dich nur auf die Zombies?',
        'Der Weltuntergang wartet.'
    ]);
    $tease = Tool_Gambling::select([
        'Oder willst du lieber weiter mit deinen Barbiepuppen spielen?', 'Oder hast du doch zu viel Angst?', 'Oder bist du wirklich so eine Lusche wie alle sagen?',
        'Du willst die Zombies doch nicht enttäuschen, oder?', 'Oder traust du dich nur in Spiele, bei denen man sich Vorteile erkaufen kann?',
        'Oder willst du deine Zeit lieber mit etwas sinnvollem verbringen?'
    ]);
    ?>

    <div class="cell rw-12 padded">
        <i><?=__($question);?></i><br /><br />
        <b><?=__('Letzt liegt es an dir: Bist du bereit, in die furchterregende Welt von ZombVival einzutauchen?');?></b> <?=__($tease);?>
    </div>

    <div class="cell rw-4 rw-sm-12 padded">
        <div class="btn btn-icon" id="btn_cancel">
            <span class="btn-icon-inner"><i class="fa fa-times"></i></span>
            <?=__('Nichts wie weg!')?>
        </div>
    </div>
    <div class="cell rw-4 rw-sm-12 padded">
        <div class="btn btn-icon" id="btn_revert">
            <span class="btn-icon-inner"><i class="fa fa-undo"></i></span>
            <?=__('Andere Stadt wählen')?>
        </div>
    </div>
    <div class="cell rw-4 rw-sm-12 padded">
        <div class="btn btn-icon" id="btn_confirm">
            <span class="btn-icon-inner"><i class="fa fa-arrow-right"></i></span>
            <?=__('Auf geht\'s!')?>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
(function() {
    $('[data-confirm], [data-sign], [data-jobfail]').hide();
    $('[data-showall]').click(function() {
        $('[data-showall]').hide();
        $('[data-sign]').show();
    });
    $('[data-setservice]').click(function() {
        var sign = $(this).data('setsign');
        var service = $(this).data('setservice');
        if ($('[data-sign="' + $(this).data('setsign') +'"]').length == 0) {
            sign = '<?=$default_job?>';
            $('[data-jobfail]').show();
        } else $('[data-jobfail]').hide();
        
        var selected = $('[data-sign="' + sign +'"]');
        var unselected = $('[data-sign][data-sign!="' + sign +'"]');

        selected.css('opacity',1).show();
        unselected.css('opacity',0.5).hide();

        $('[data-confirm]').show();
        $('[data-preselect]').hide();
        $('[data-showall]').show();

        $('#btn_confirm').data('service', service);
    });

    $('#btn_cancel').click(function() {game.network.load('gamemaster/lobby');});
    $('#btn_revert').click(function() {
        $('[data-confirm]').hide();
        $('[data-preselect]').show();
    });
    $('#btn_confirm').click(function() {
        var service = $(this).data('service');
        if (!service) {
            game.render.html.notify('error',<?=__j('Ein Fehler ist aufgetreten.')?>);
            return;
        }

        var layer = $('<div />');

        game.render.html.modal.fade();
        setTimeout(function() {
            layer.css({
                position: 'fixed',
                width: $(document).width()/2,
                height: 413 * (($(document).width()/2)/1280),
                top: 100,
                left: $(document).width()/4,
                opacity: 0,
                transform: 'scale(0.75)',
                'z-index': $.topZIndex('*') + 1
            }).addClass('logo').appendTo('body');

            layer.animate({
                opacity: 1,
                top: 300,
                transform: 'scale(1)'
            }, 4000, 'swing');
        }, 500);

        setTimeout(function() {
            game.network.query('japi/gamemaster/start', {
                special: 12000,
                service: service
            }, function(data) {
                if (data.error) {
                    alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                } else
                    game.network.load(data.redirect, {}, null, function() {
                        game.render.html.modal.clear();
                        layer.animate({
                            opacity: 0,
                            transform: 'scale(2)',
                            top: 400
                        }, 300, 'swing', function() {
                            layer.remove();
                        });
                    });
            })}, 3000);
    });

    <?php foreach ($database['jobs'] as $jid => $job) { ?>
        $('[data-job="<?=$jid?>"]').each(function() {
            var alias = $(this);

            alias.attr('title','-').qtip(game.render.html.qtip.ingame('top', {
                render: function(event,api) {
                    var content = $(this).find('.qtip-content');

                    content
                        .empty()
                        .append($('<b />').addClass('header hold').text(<?=__j($job['meta']['name'])?>));

                    <?php if (!$job['locked']) { ?>
                        content
                            .append($('<div />').addClass('center').text(<?=__j('Level-Informationen')?>))
                            .append(NF.row()
                                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Aktuelles Level')?>))
                                .append($('<div />').addClass('cell rw-6 padded').text(<?=$job['level']?>))
                            ).append(NF.row()
                                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Seelenpunkte')?>))
                                .append($('<div />').addClass('cell rw-6 padded').text(<?=$job['points']?>))
                            );

                        <?php if (!$job['next_level']) { ?>
                            content.append($('<div />').addClass('b center').text(<?=__j('Maximales Level erreicht!')?>));
                        <?php } else { ?>
                            content.append(NF.row()
                                .append($('<div />').addClass('cell rw-6 padded right b').text(<?=__j('Nächstes Level')?>))
                                .append($('<div />').addClass('cell rw-6 padded').append($('<div />').addClass('soulpointbar').append($('<div />').css('width', (<?=100*$job['points']/$job['next_level']?>) + '%'))).append($('<div />').addClass('center').text(<?=$job['points']?> + ' / ' + <?=$job['next_level']?>)))
                            );
                        <?php } ?>
                    <?php } ?>
                }
            }));
        });
    <?php } ?>
})();
// ## JS COMPRESS END ## //
</script>

