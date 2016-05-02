<?php
/**
 * @var array $items 
 */
?>
<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Item List</h1>

<div class="row">
    <div class="cell ro-1 rw-10 padded">
        <a href="#" data-hrefto=""><i class="fa fa-arrow-circle-right"></i> Reflection Atlas Main Page</a>
    </div>

    <div class="cell rw-12 padded">
        <div class="note">
            <b>Item List</b><br />
            This page lists all items that have been registered to the game by inheriting it's item base class. Click on an item to obtain more information.
        </div>
        
        <div class="row-table padded row-table-borders row-table-striped row-table-interact">
            <div class="row">
                <div class="cell padded rw-4">Item Name</div>
                <div class="cell padded rw-8">Hierarchy</div>
            </div>
            <?php foreach ($items as $item) { ?>
                <div class="row pointer" data-roll="1">
                    <div class="cell-small padded rw-1"><img src="media/icons/<?=$item['info']['icon']?>.gif" /></div>
                    <div class="cell-small padded rw-7"><?=$item['info']['name']?></div>
                    <div class="cell padded rw-8">
                        <?php foreach ($item['lineage'] as $anc) { ?>
                            <i style="font-size: 14px; font-weight: bolder" class="fa fa-angle-left"></i> <span style="font-size: 10px;"><?=$anc?></span>
                        <?php } ?>
                    </div>
                    <div class="cell padded rw-11 ro-1">
                        <div class="row">
                            <div class="cell rw-12 padded">
                                <?php if ($item['info']['alias']) { ?>
                                    <b>Known aliases: </b>
                                    <?php foreach ($item['info']['alias'] as list($name, $icon)) { ?>
                                        <div class="solid"><img src="media/icons/<?=$icon?>.gif" /> <?=$name?></div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <b>This item does not have aliases.</b>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="cell rw-12 padded">
                                <?php if ($item['code']) { ?>
                                    <b>Implemented Methods: </b>
                                    <?php foreach ($item['code'] as $entry) { ?>
                                        <div class="solid"><i class="fa fa-<?=$entry['custom'] ? 'caret-right' : 'long-arrow-up' ?>"></i> <?=$entry['name']?></div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <b>This item contains no program logic.</b>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell rw-12 padded">
                                <?php if ($item['properties']) { ?>
                                    <div class="row">
                                        <div class="cell ro-1 rw-12">
                                            <div class=row>
                                                <div class="cell rw-12"><b>Static Properties</b></div>
                                            </div>
                                            <?php foreach ($item['properties'] as $pname => $pvalue) { ?>
                                                <div class=row>
                                                    <div class="cell rw-3 right smallpad"><?=$pname?></div>
                                                    <div class="cell rw-9 left smallpad"><div class="solid" style="font-family: monospace"><?=$pvalue?></div></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <b>This item has no static properties.</b>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>            
            <?php } ?>
        </div>
    </div>
</div>

<script type="application/javascript">
    // ## JS COMPRESS BEGIN ## //
    $('[data-hrefto]').click(function() {
        game.network.load($(this).data('hrefto') ? ('admin/wiki/' + $(this).data('hrefto')) : 'admin/wiki');
    });

    $('[data-roll]').each(function() {
        $(this).children(':last-child').hide();
    }).click(function() {
        $(this).children(':last-child').slideToggle();
    });

    // ## JS COMPRESS END ## //
</script>