<?php
/**
 * @var array $locations 
 */
?>
<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Item List</h1>

<div class="row">
    <div class="cell ro-1 rw-10 padded">
        <a href="#" data-hrefto=""><i class="fa fa-arrow-circle-right"></i> Reflection Atlas Main Page</a>
    </div>

    <div class="cell rw-12 padded">
        <div class="note">
            <b>Location List</b><br />
            This page lists all locations that have been registered to the game by inheriting it's location base class. Click on an entry to obtain more information.
        </div>

        <div class="row-table padded row-table-borders row-table-striped row-table-interact">
            <div class="row">
                <div class="cell padded rw-4">Location Name</div>
                <div class="cell padded rw-1">N.Dg.</div>
                <div class="cell padded rw-1">B/H</div>
                <div class="cell padded rw-6">Hierarchy</div>
            </div>
            <?php foreach ($locations as $location) { ?>
                <div class="row pointer" data-roll="1">
                    <div class="cell-small padded rw-1"><img src="media/icons/places/<?=$location['info']['icon']?>.gif" /></div>
                    <div class="cell-small padded rw-7"><?=$location['info']['name']?></div>
                    <div class="cell padded rw-1"><?=round($location['info']['danger']['ndg'],2)?></div>
                    <div class="cell padded rw-1"><?=round($location['info']['danger']['rte'],2)?></div>
                    <div class="cell padded rw-6">
                        <?php foreach ($location['lineage'] as $anc) { ?>
                            <i style="font-size: 14px; font-weight: bolder" class="fa fa-angle-left"></i> <span style="font-size: 10px;"><?=$anc?></span>
                        <?php } ?>
                    </div>
                    <div class="cell padded rw-11 ro-1">
                        <div class="row">
                            <div class="cell rw-12 padded">
                                <?php if ($location['info']['alias']) { ?>
                                    <b>Known aliases: </b>
                                    <?php foreach ($location['info']['alias'] as [
                                        $name, $icon]
                                    ) { ?>
                                        <div class="solid"><img src="media/icons/places/<?=$icon?>.gif" /> <?=$name?></div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <b>This location does not have aliases.</b>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="cell rw-12 padded">
                                <div class=row>
                                    <div class="cell rw-12"><b>Danger Configuration</b></div>
                                </div>
                                <div class=row>
                                    <div class="cell rw-2 right smallpad">Max. Strength</div>
                                    <div class="cell rw-2 left smallpad"><div class="solid" style="font-family: monospace"> <?=$location['info']['danger']['str']?> </div></div>

                                    <div class="cell rw-2 right smallpad">Chance</div>
                                    <div class="cell rw-2 left smallpad"><div class="solid" style="font-family: monospace"> <?=$location['info']['danger']['chn']?> </div></div>

                                    <div class="cell rw-2 right smallpad">Blocking</div>
                                    <div class="cell rw-2 left smallpad"><div class="solid" style="font-family: monospace"> <?=$location['info']['danger']['blk']?> </div></div>

                                    <div class="cell rw-2 right smallpad">Norm. Strength</div>
                                    <div class="cell rw-2 left smallpad"><div class="solid" style="font-family: monospace"> <?=$location['info']['danger']['ndg']?> </div></div>

                                    <div class="cell rw-2 right smallpad">Number</div>
                                    <div class="cell rw-2 left smallpad"><div class="solid" style="font-family: monospace"> <?=$location['info']['danger']['num'][0]?> - <?=$location['info']['danger']['num'][2]?> (<?=$location['info']['danger']['num'][1]?>) </div></div>

                                    <div class="cell rw-2 right smallpad">B-Rate</div>
                                    <div class="cell rw-2 left smallpad"><div class="solid" style="font-family: monospace"> <?=$location['info']['danger']['rte']?> / H </div></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="cell rw-12 padded">
                                <div class=row>
                                    <div class="cell rw-12"><b><?=!empty($location['items']) ? 'Item Configuration' : 'This location has no item drops' ?></b></div>
                                </div>
                                <div class=row>
                                    <?php foreach ($location['items'] as $entry) { ?>
                                        <div class="cell-small rw-3 left smallpad"><div class="solid" title="<?=htmlentities($entry['name'])?>">
                                            <img alt="?" src="media/icons/<?=$entry['icon']?>.gif" />
                                            <?=round(100*$entry['chance'], $entry['chance'] < 0.01 ? 2 : 0)?>%
                                        </div></div>
                                    <?php }?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="cell rw-12 padded">
                                <?php if ($location['code']) { ?>
                                    <b>Implemented Methods: </b>
                                    <?php foreach ($location['code'] as $entry) { ?>
                                        <div class="solid"><i class="fa fa-<?=$entry['custom'] ? 'caret-right' : 'long-arrow-up' ?>"></i> <?=$entry['name']?></div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <b>This location contains no program logic.</b>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell rw-12 padded">
                                <?php if ($location['properties']) { ?>
                                    <div class="row">
                                        <div class="cell rw-12">
                                            <div class=row>
                                                <div class="cell rw-12"><b>Static Properties</b></div>
                                            </div>
                                            <?php foreach ($location['properties'] as $pname => $pvalue) { ?>
                                                <div class=row>
                                                    <div class="cell rw-3 right smallpad"><?=$pname?></div>
                                                    <div class="cell rw-9 left smallpad"><div class="solid" style="font-family: monospace"><?=$pvalue?></div></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <b>This location has no static properties.</b>
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