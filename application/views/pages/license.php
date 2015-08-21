<?php
/**
 * @var array $data
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Bildmaterial')?></h1>

<div class="row">
    <div class="cell rw-12 padded">
        <?=__('Diese Seite dient der Nennung von Autoren und Lizenzen aller für das Design dieses Spiels verwendeten Grafiken, deren Lizenz eine solche Nennung verlangt. Nicht erfasst sind Grafiken von Motion Twin. Sind Sie der Autor einer solchen Grafik und mit der Nutzung nicht einverstanden, oder haben Sie eine Grafik gefunden die hier nicht aufgelistet ist, melden Sie sich bitte unter folgender E-Mail Adresse.')?>
        <a href="mailto: kontakt@ruine.dvspot.de">kontakt@ruine.dvspot.de</a><br />
    </div>
</div>

<?php foreach ($data as $license) { ?>
<div class="row">
    <div class="license">
        <div class="row">
            <div class="cell rw-3 padded">
                <?php if (!is_array($license['local'])) $license['local'] = [$license['local']]; ?>
                <?php foreach ($license['local'] as $local_url) { ?>
                    <img src="<?=$local_url?>" />
                <?php } ?>
            </div>
            <div class="cell rw-9 padded">
                <i class="small">This artwork is based on (and inherits its license from)</i><br />
                <b><?=$license['title']?></b>, by <a href="<?=$license['author']['url']?>" target="_blank"><?=$license['author']['name']?></a><br />
                <a href="<?=$license['url']?>" target="_blank"><?=$license['url']?></a><br /><br />
                License: <i><?=$license['license']['long']?> (<a target="_blank" href="<?=$license['license']['url']?>"><?=$license['license']['short']?></a>)</i><br /><br />

            </div>

        </div>
    </div>
</div>
<?php } ?>

