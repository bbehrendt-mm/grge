<?php
/**
 * @var number $id
 * @var string $name
 * @var string $mode
 * @var string $duration
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Game Manager (#<?=$id?>)</h1>

<div class="row">
    <div class="cell padded rw-12">
        <button class="btn small" id="toList">Back to game list</button>
    </div>
</div>

<div class="row">
    <div class="cell rw-6 padded">
        <div class="row-table padded row-table-borders row-table-striped">
            <div class="row">
                <div class="cell padded rw-12 center"><b>Game Information</b></div>
            </div>
            <div class="row">
                <div class="cell padded rw-6"><b>Game ID</b></div>
                <div class="cell padded rw-6"><?=$id?></div>
            </div>
            <div class="row">
                <div class="cell padded rw-6"><b>Game Name</b></div>
                <div class="cell padded rw-6"><?=$name?></div>
            </div>
            <div class="row">
                <div class="cell padded rw-6"><b>Mode</b></div>
                <div class="cell padded rw-6"><?=$mode?></div>
            </div>
            <div class="row">
                <div class="cell padded rw-6"><b>Game Duration</b></div>
                <div class="cell padded rw-6"><?=$duration?></div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#toList').click(function() {game.network.load('admin/games')});
// ## JS COMPRESS END ## //
</script>