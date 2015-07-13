<?php
/**
 * @var bool $multiplayer
 * @var string|null $name
 * @var string $mode
 * @var int $score_sp
 * @var int $score_ap
 * @var int $from
 * @var int $to
 * @var int $ticks
 * @var array $players
 * @var array $achievement_db
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Spieldetails');?></h1>

<div class="value-box">
    <div class="row center">
        <div class="cell rw-4 padded">
            <?php if ($multiplayer) { ?>
                <b><?=__('Name');?></b><br />
                <?=$name;?>
            <?php } else { ?>
                <b><?=__('Seelenpunkte');?></b><br />
                <?=$score_sp;?>
            <?php } ?>
        </div>
        <div class="cell rw-4 padded">
            <?php if ($multiplayer) { ?>
                <b><?=__('Ranking-Punkte');?></b><br />
                <?=$score_sp;?>
            <?php } else { ?>
                <b><?=__('Auszeichnungspunkte');?></b><br />
                <?=$score_ap;?>
            <?php } ?>
        </div>
        <div class="cell rw-4 padded">
            <b><?=__('Spielmodus');?></b><br />
            <?=__($mode);?>
        </div>
    </div>

    <div class="row center">
        <div class="cell rw-4 padded">
            <b><?=__('Spielstart');?></b><br />
            <?=date(__('G:i \U\h\r \a\m d.m.y'), $from);?>
        </div>
        <div class="cell rw-4 padded">
            <?php if ($multiplayer) { ?>
                &nbsp;
            <?php } else { ?>
                <b><?=__('Überlebte Zeit');?></b><br />
                <?=Tool_Numerics::duration_to_string($ticks)?>
            <?php } ?>
        </div>
        <div class="cell rw-4 padded">
            <b><?=__('Spielende');?></b><br />
            <?=date(__('G:i \U\h\r \a\m d.m.y'), $to);?>
        </div>
    </div>

    <?php if (!$multiplayer) { ?>
        <div class="row center">
            <div class="cell rw-4 padded">&nbsp;</div>
            <div class="cell rw-4 padded">
                <b><?=__('Spieler');?></b><br />
                <div class="inline-player"><?=$players[0]['name']?></div>
            </div>
            <div class="cell rw-4 padded">&nbsp;</div>
        </div>
    <?php } ?>
</div>

<br /><br />

<?php if ($multiplayer) { ?>
    <div class="row-table padded row-table-borders row-table-striped row-table-interact">
        <div class="row">
            <div class="cell rw-3 center padded"><?=__('Spieler');?></div>
            <div class="cell rw-3 center padded"><?=__('Punkte-Anteil');?></div>
            <div class="cell rw-2 center padded"><?=__('Lebenszeit');?></div>
            <div class="cell rw-2 center padded"><?=__('SP');?></div>
            <div class="cell rw-2 center padded"><?=__('AP');?></div>
        </div>
        <?php $p_accum = 0; ?>
        <?php foreach ($players as $p) { ?>
            <?php $p_accum += $p['ticks']; ?>
            <div class="row">
                <div class="cell rw-3 center padded"><div class="inline-player"><?=$p['name'];?></div></div>
                <div class="cell rw-3 center padded"><div class="soulpointbar" data-name="<?=$p['name'];?>" data-prc="<?=round(100*$p['ticks']/$score_sp)?>%"><div style="width: <?=100*$p['ticks']/$score_sp?>%"></div></div></div>
                <div class="cell rw-2 center padded"><?=Tool_Numerics::duration_to_string($p['ticks'])?></div>
                <div class="cell rw-2 center padded"><?=$p['score_sp'];?></div>
                <div class="cell rw-2 center padded"><?=$p['score_ap'];?></div>
                <div class="cell rw-12 center padded">
                    <?php foreach ($p['achievements'] as $achievement) { ?>
                        <div data-aid="<?=$achievement['id']?>" class="pointer achievement achievement-<?=$achievement['class']?>">
                            <img alt="?" src="media/icons/achievements/<?=$achievement['icon']?>" />
                            <span><?=$achievement['count']?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($p_accum < $score_sp) { ?>
            <div class="row">
                <div class="cell rw-3 center padded"><?=__('Sonstige Spieler');?></div>
                <div class="cell rw-3 center padded"><div class="soulpointbar" data-prc="<?=round(100*($score_sp-$p_accum)/$score_sp)?>%"><div style="width: <?=100*($score_sp-$p_accum)/$score_sp?>%"></div></div></div>
                <div class="cell rw-2 center padded">???</div>
                <div class="cell rw-2 center padded">0</div>
                <div class="cell rw-2 center padded">0</div>
                <div class="cell rw-12 center padded">
                    <div class="note">
                        <?=__('Unter diesem Punkt werden alle Spieler zusammengefasst, die in dieser Partie zwar dabei waren, jedoch durch einen frühen Tod keine Seelenpunkte erhalten haben und damit auf dieser Ergebnisseite nicht namentlich genannt werden.');?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="row">
        <?php foreach ($players[0]['achievements'] as $achievement) { ?>
            <div data-aid="<?=$achievement['id']?>" class="pointer achievement achievement-<?=$achievement['class']?>">
                <img alt="?" src="media/icons/achievements/<?=$achievement['icon']?>" />
                <span><?=$achievement['count']?></span>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    <?php foreach ($achievement_db as $achievement) { ?>
        $('[data-aid=<?=$achievement['id']?>]').attr('title', '-').qtip(game.render.html.qtip.ingame('top', {
            render: function(event,api) {
                $(this).find('.qtip-content')
                    .empty()
                    .append($('<b />').addClass('header').text(<?=__j($achievement['name'])?>))
                    .append($('<p />').html(<?=__j('Diese Auszeichnung ist ::i:: :num  Punkte::/i:: wert.', [':num' => $achievement['points']])?>))
            }
        })).click(function() {window.open('ranking/global/<?=$achievement['id']?>')});
    <?php } ?>

    $('.soulpointbar[data-prc]').attr('title','-').each(function() {
        var alias = $(this);
        if ($(this).data('name'))
            $(this).qtip(game.render.html.qtip.ingame('top', {
                render: function(event,api) {
                    $(this).find('.qtip-content')
                        .empty()
                        .append($('<b />').addClass('header').text(<?=__j('Punkte-Anteil')?>))
                        .append($('<p />').text(<?=__j('Dieser Balken zeigt an, wie sehr dieser Spieler zur Gesamtpunktzahl des Spiels beigetragen hat.')?>))
                        .append($('<p />').text(game.i18n(<?=__j(':name hat :prc der Punkte zu diesem Spiel beigesteuert.')?>, {':name': alias.data('name'), ':prc': alias.data('prc')})))
                }
            }));
        else
            $(this).qtip(game.render.html.qtip.ingame('top', {
                render: function(event,api) {
                    $(this).find('.qtip-content')
                        .empty()
                        .append($('<b />').addClass('header').text(<?=__j('Punkte-Anteil')?>))
                        .append($('<p />').text(<?=__j('Dieser Balken zeigt an, wie sehr die nicht namentlich aufgeführten Spieler zur Gesamtpunktzahl des Spiels beigetragen haben.')?>))
                        .append($('<p />').text(game.i18n(<?=__j('Sonstige Spieler haben :prc der Punkte zu diesem Spiel beigesteuert.')?>, {':prc': alias.data('prc')})))
                }
            }));
    });
// ## JS COMPRESS END ## //
</script>