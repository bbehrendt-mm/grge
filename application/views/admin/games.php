<?php
/**
 * @var array $games
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Spieleverwaltung</h1>

<div class="row-table padded row-table-borders row-table-striped row-table-interact" id="target_list">
    <div class="row">
        <div class="cell padded rw-1"><b>ID</b></div>
        <div class="cell padded rw-1"><b>Integrity</b></div>
        <div class="cell padded rw-3"><b>Name</b></div>
        <div class="cell padded rw-2"><b>Mode</b></div>
        <div class="cell padded rw-5"><b>Players</b></div>
    </div>
    <?php foreach ($games as $game) { ?>
        <div class="row">
            <div class="cell padded rw-1"><?=$game['id']?></div>
            <div id="error-hover-<?=$game['id']?>" class="cell padded rw-1"><?=$game['loadable'] ? 'OK' : 'ERROR'?></div>
            <?php if ($game['loadable']) { ?>
                <div id="name-hover-<?=$game['id']?>" class="cell padded rw-3"><?=$game['name'] ? $game['name'] : '[Unnamed]'?></div>
                <div class="cell padded rw-2"><?=__($game['mode']);?></div>
                <div class="cell padded rw-5"></div>
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
// ## JS COMPRESS END ## //
</script>