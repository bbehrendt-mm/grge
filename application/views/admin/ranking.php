<?php /** @noinspection DisconnectedForeachInstructionInspection */
/**
 * @var int $season
 * @var string[] $modes
 * @var mixed[] $ranks
 * @var mixed[] $achievements
 * @var mixed[] $errors
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Ranking-Verwaltung</h1>

<div class="row">
    <h2>Überblick</h2>
    <div class="row">
        <div class="cell rw-10 ro-1">
            <div class="row-table padded row-table-borders row-table-striped">
                <div class="row">
                    <div class="cell padded rw-5"><b>Season</b></div>
                    <div class="cell padded rw-3"><b>Anz. Fehler</b></div>
                    <div class="cell padded rw-4"><b>Aktionen</b></div>
                </div>

                <?php foreach ($errors as $season => $count) { ?>
                    <div class="row <?=$count === 0 ? '' : 'bg-red'?>">
                        <div class="cell padded rw-5">Season <?=$season?></div>
                        <div class="cell padded rw-3"><?=$count?></div>
                        <div class="cell padded rw-4">
                            <?php if ($count > 0) { ?>
                                <div class="btn small" data-purpose="refresh" data-season="<?=$season?>">Aktualisieren</div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php for ($s = $season; $s >= 0; $s--) { ?>
        <?php if (empty($ranks[$s])) continue; ?>
        <h2>Season <?=$s;?></h2>

        <?php foreach ($modes as $m => $m_name) { ?>
            <?php if (empty($ranks[$s][$m])) continue; ?>
            <h3><?=$m_name;?> (<i><?=$s;?>/<?=$m;?></i>)</h3>

            <div class="row">
                <?php for ($f = 0; $f < 2; $f++) { ?>
                    <?php if (empty($ranks[$s][$m][$f])) continue; ?>

                        <div class="cell rw-4 padded">
                            <b><?=!$f?'Klassischer Zeitfluss':'Variabler Zeitfluss'?> Tabelle</b>
                            <div class="row-table padded row-table-borders row-table-striped">
                                <div class="row">
                                    <div class="cell-small padded rw-5"><b>ID</b></div>
                                    <div class="cell-small padded rw-19"><b>Spieler</b></div>
                                </div>

                                <?php foreach ($ranks[$s][$m][$f] as $rank) { ?>
                                    <div class="row">
                                        <div class="cell-small padded rw-5"><?=$rank['gameid']?></div>
                                        <div class="cell-small padded rw-19">
                                            <?php foreach ($rank['uid'] as $uid => $name) { ?>
                                                <span class="inline-player"><?=$name?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                <?php } ?>

                <div class="cell rw-4 padded">
                    <b>Punkte & AZ</b>
                    <div class="row-table padded row-table-borders row-table-striped">
                        <div class="row">
                            <div class="cell padded rw-9"><b>Spieler</b></div>
                            <div class="cell padded rw-3"><b>Punkte / AZ</b></div>
                        </div>

                        <?php foreach ($achievements[$s][$m] as $person) { ?>
                            <div class="row <?=$person['ok'] ? '' : 'bg-red'?>">
                                <div class="cell padded rw-9"><span class="inline-player"><?=$person['name']?></span></div>
                                <div class="cell padded rw-3"><?=$person['points']?> / <?=$person['achievements']?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>


<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('[data-purpose=refresh]').click(function() {
        $('[data-purpose]').addClass('disabled');
        game.network.query('admin/japi/ranking/fix', {season: $(this).data('season')}, function(data) {
            if (data.success) {
                game.render.html.notify('success', 'Ranking wurde neu berechnet.');
                game.network.load('admin/ranking');
            }
        }, function() {
            $('[data-purpose]').removeClass('disabled');
        })
    });

// ## JS COMPRESS END ## //
</script>