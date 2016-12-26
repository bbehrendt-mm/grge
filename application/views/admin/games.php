<?php
/**
 * @var array $games
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Spieleverwaltung</h1>

<div class="row">
    <div class="cell ro-1 rw-10">
        <div class="flatbox">
            <button class="btn small" data-gid="*" data-action="game_update">Update all</button>
            <button class="btn small" data-gid="*" data-action="process_tick">Process next tick for all games</button>
        </div>
    </div>
</div>

<div class="row-table padded row-table-borders row-table-striped row-table-interact" id="target_list">
    <div class="row">
        <div class="cell padded rw-1"><b>ID</b></div>
        <div class="cell padded rw-1"><b>Integrity</b></div>
        <div class="cell padded rw-3"><b>Name</b></div>
        <div class="cell padded rw-2"><b>Mode</b></div>
        <div class="cell padded rw-5"><b>Players</b></div>
    </div>
    <?php foreach ($games as $game) { ?>
        <div class="row"  data-roll="1">
            <div class="cell padded rw-1"><?=$game['id']?></div>
            <div id="error-hover-<?=$game['id']?>" class="cell padded rw-1"><?=$game['loadable'] ? 'OK' : 'ERROR'?></div>
            <?php if ($game['loadable']) { ?>
                <div id="name-hover-<?=$game['id']?>" class="cell padded rw-3"><?=$game['name'] ? (is_array($game['name']) ? "{$game['name'][0]} ({$game['name'][1]})" : $game['name']) : '[Unnamed]'?></div>
                <div class="cell padded rw-2"><?=__($game['mode']);?></div>
                <div class="cell padded rw-5">
                    <?php foreach ($game['players'] as $pid => $p) { ?>
                        <div class="solid"><?=$p['name']?> (<?=$pid?>)</div>
                    <?php } ?>
                </div>
                <div class="cell padded rw-11 ro-1">
                    <b>Actions</b><br />
                    <div class="row">
                        <div class="cell rw-12">
                            <button class="btn small <?=$game['players'] ? '' : 'disabled'?>" data-gid="<?=$game['id']?>" data-action="game_update">Update</button>
                            <button class="btn small <?=$game['players'] ? '' : 'disabled'?>" data-gid="<?=$game['id']?>" data-action="process_tick">Process next tick</button>
                            <button class="btn small <?=$game['players'] ? '' : 'disabled'?>" data-confirm="Are you sure you want to end game #<?=$game['id']?>?" data-auto="0" data-gid="<?=$game['id']?>" data-action="game_retire">End Gracefully</button>
                            <button class="btn small <?=$game['players'] ? '' : 'disabled'?>" data-confirm="Are you sure you want to end game #<?=$game['id']?>?" data-auto="1" data-gid="<?=$game['id']?>" data-action="game_retire">Remove Gracefully</button>
                            <button class="btn small" data-gid="<?=$game['id']?>" data-confirm="Are you sure you want to end game #<?=$game['id']?>?" data-action="game_delete">Delete</button>
                        </div>
                    </div>


                    <?php if ($game['players']) { ?>
                        <b>Players</b><br />
                        <div class="row-table padded row-table-borders row-table-striped row-table-interact">
                            <div class="row">
                                <div class="cell padded rw-1"><b>ID</b></div>
                                <div class="cell padded rw-3"><b>Name</b></div>
                                <div class="cell padded rw-3"><b>Job</b></div>
                                <div class="cell padded rw-1"><b>Alive</b></div>
                                <div class="cell padded rw-4"><b>Actions</b></div>
                            </div>
                            <?php foreach ($game['players'] as $pid => $p) { ?>
                                <div class="row">
                                    <div class="cell padded rw-1"><?=$pid?></div>
                                    <div class="cell padded rw-3"><?=$p['name']?></div>
                                    <div class="cell padded rw-3"><?=$p['job']?> (<?=$p['level']?>)</div>
                                    <div class="cell padded rw-1"><?=$p['alive'] ? 'YES' : ($p['confirmed'] ? 'NO' : 'NO (pend.)')?></div>
                                    <div class="cell padded rw-4">
                                        <button class="btn small <?=$p['alive'] ? '' : 'disabled'?>" data-gid="<?=$game['id']?>" data-pid="<?=$pid?>" data-action="kill">Kill</button>
                                        <button class="btn small <?=(!$p['alive'] && !$p['confirmed']) ? '' : 'disabled'?>" data-gid="<?=$game['id']?>" data-pid="<?=$pid?>" data-confirm="Are you sure you want to kill player <?=$p['name']?> (<?=$game['id']?>/<?=$pid?>)?" data-action="retire">Confirm Death</button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <?php if ($game['npcs']) { ?>
                        <b>NPCs</b><br />
                        <div class="row-table padded row-table-borders row-table-striped row-table-interact">
                            <div class="row">
                                <div class="cell padded rw-1"><b>ID</b></div>
                                <div class="cell padded rw-3"><b>Name</b></div>
                                <div class="cell padded rw-3"><b>Class</b></div>
                                <div class="cell padded rw-1"><b>Alive</b></div>
                                <div class="cell padded rw-4"><b>Actions</b></div>
                            </div>
                            <?php foreach ($game['npcs'] as $nid => $n) { ?>
                                <div class="row">
                                    <div class="cell padded rw-1"><?=$nid?></div>
                                    <div class="cell padded rw-3"><?=$n['name']?></div>
                                    <div class="cell padded rw-3"><?=$n['species']?> (<?=$n['cls']?>)</div>
                                    <div class="cell padded rw-1"><?=$n['alive'] ? 'YES' : 'NO'?></div>
                                    <div class="cell padded rw-4">
                                        <button class="btn small <?=$n['alive'] ? '' : 'disabled'?>" data-gid="<?=$game['id']?>" data-pid="<?=$nid?>" data-confirm="Are you sure you want to kill NPC <?=$p['name']?> (<?=$game['id']?>/<?=$nid?>)?" data-action="kill">Kill</button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                </div>
            <?php } else { ?>
                <div class="cell padded rw-11 ro-1">
                    <?=$game['error'];?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    <?php foreach ($games as $game) { ?>
        <?php if ($game['error']) { ?>
            $('#error-hover-<?=$game['id']?>').attr('title',<?=json_encode($game['error'])?>).qtip(game.render.html.qtip.player('top'));
        <?php } ?>

    <?php } ?>

    $('[data-roll]').each(function() {
        $(this).children(':last-child').hide();
    }).click(function() {
        $(this).children(':last-child').slideToggle();
    });

    $('button[data-action]').click(function(e) {
        e.stopPropagation();

        var trns_data = {};
        $.each($(this).data(), function(k,v) {
            if ($.inArray(k,['action','confirm']) < 0)
                trns_data[k] = v;
        });

        if ($(this).data('confirm') && !confirm('This action requires explicit confirmation: ' + $(this).data('confirm')))
            return;

        game.network.query('admin/japi/games/' + $(this).data('action'), trns_data, function(data) {
            if (data.success == "0") {
                game.render.html.notify('success','Operation successfull');
                game.network.load('admin/games');
            }
            else game.render.html.notify('error','Operation failed!','Error Code ' + data.success);
        });
    });
// ## JS COMPRESS END ## //
</script>