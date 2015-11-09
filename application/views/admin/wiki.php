<?php
/**
 * @var array $index
 * @var array $names
 * @var array $items
 * @var array $zombies
 * @var array $radar
 * @var array $types
 * @var array $atlas
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Reflection Atlas</h1>

<div class="row">
    <div class="cell rw-12 padded">
        <div class="toolbox">
            <div class="row center">
                <div class="cell rw-12 padded">
                    <?php foreach ($atlas as $iclass => $item) { ?>
                        <img src="media/icons/<?=$item['icon']?>.gif" /><b><?=$item['name']?></b> (<?=$item['count']?>) <i class="small">[<?=$iclass?>]</i><br />
                        <?php if ($item['count']) foreach ($item['locations'] as $key => $entry) { ?>
                            <?php $str_name = (mb_strlen($names[$key]) > 16) ? (mb_substr($names[$key], 0, 15) . '…') : $names[$key] ?>
                            <div title="<?=htmlentities($names[$key])?>" style="opacity: <?=$entry ? 1 : 0.5?>" class="inline-player"><?=$str_name?> (<?=$entry?>)</div>
                        <?php } else { ?>Keine Fundorte!<?php } ?><br /><br />
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php foreach ($index as $lc_index) { ?>
        <div class="cell rw-12 padded">
            <div class="toolbox">
                <div class="row center">
                    <div class="cell rw-12 padded">
                        <b><?=$names[$lc_index]?></b><br />
                        <i class="small"><?=$lc_index?><?=$types[$lc_index] ? " [{$types[$lc_index]}]" : ''?></i>
                    </div>
                </div>

                <div class="row">
                    <div class="cell rw-6 padded">
                        <b>Items</b><br />
                        <?php if (isset($items[$lc_index])) { ?>
                            <table cellpadding="5">
                                <thead><tr><td colspan="2"><b>Item</b></td><td colspan="2"><b>Chance & Erw. Funde</b></td></tr></thead>
                                <?php foreach ($items[$lc_index] as $key => $item) { ?>
                                    <tr>
                                        <?php if ($key === '') { ?>
                                            <td></td>
                                            <td><b>GESAMT</b></td>
                                            <td></td>
                                            <td><?=$item?></td>
                                        <?php } else {?>
                                            <td><?php if ($item['icon']) {?><img src="media/icons/<?=$item['icon']?>.gif" /><?php } ?></td>
                                            <td><?=$item['name']?></td>
                                            <td><?=$item['chance']?>%</td>
                                            <td><?=$item['expect']?></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } else { ?>
                            <b class="red">Nicht konfiguriert!!!</b>
                        <?php } ?>
                    </div>
                    <div class="cell rw-6 padded">
                        <b>Zombies</b><br />
                        <?php if (isset($zombies[$lc_index])) { ?>
                            <table cellpadding="5">
                                <tr>
                                    <td><b>Max. Stärke</b></td>
                                    <td><?=$radar[$lc_index]['strength']?></td>
                                    <td><b>Max. Gruppen</b></td>
                                    <td><?=$radar[$lc_index]['groups']?></td>
                                </tr><tr>
                                    <td><b>Chance (Atk.)</b></td>
                                    <td><?=$radar[$lc_index]['c_attack']?>%</td>
                                    <td><b>Chance (Blk.)</b></td>
                                    <td><?=$radar[$lc_index]['c_block']?>%</td>
                                </tr>
                            </table>

                            <table cellpadding="5">
                                <thead><tr><td colspan="2"><b>Zombie</b></td><td colspan="2"><b>Chance & Max. #</b></td></tr></thead>

                                <?php foreach ($zombies[$lc_index] as $zombie) { ?>
                                    <tr>
                                        <td><?php if ($zombie['icon']) {?><img src="media/icons/<?=$zombie['icon']?>.gif" /><?php } ?></td>
                                        <td><?=$zombie['name']?></td>
                                        <td><?=$zombie['chance']?>%</td>
                                        <td><?=$zombie['expect']?></td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } else { ?>
                            <b class="red">Nicht konfiguriert!!!</b>
                        <?php } ?>
                    </div>
                </div>


            </div>
        </div>
    <?php } ?>

</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //

// ## JS COMPRESS END ## //
</script>