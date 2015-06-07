<?php
/**
 * @var string $uri
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><span class="hide-sm"><?=__('Seite nicht gefunden')?></span><span class="hide-md hide-lg hide-desktop">404</span></h1>

<div class="row">
    <div class="cell rw-8 ro-2 rw-lg-10 ro-lg-1 rw-md-12 ro-md-0 padded">
        <h2><?=__('Diese Seite wurde von Zombies gefressen!');?></h2>
        <span><?=__('Offensichtlich ist das aber noch niemandem aufgefallen.');?></span><br /><br />

        <b><?=__('Hier sind ein paar Sachen, die du jetzt tun kannst:');?></b>
        <ul>
            <li><?=__('Falls du von einer fremden Seite hierher gelangt bist, informiere dessen Betreiber, dass der Link offenbar nicht mehr aktuell ist.');?></li>
            <li><?=__('Bist du über einen Link auf ZombVival hierher gekommen, melde das bitte im Forum!');?></li>
            <li><?=__('Bringe dem Internet ein Tieropfer dar und hoffe, dass dies die Seite zurückbringt, die du suchst.');?></li>
        </ul>

        <div class="row">
            <div class="cell rw-4 ro-8 rw-lg-6 ro-lg-6 rw-sm-12 ro-sm-0 padded">
                <div id="return_to_page" class="btn"><?=__('Zur Hauptseite');?></div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    // ## JS COMPRESS BEGIN ## //
    $('#return_to_page').click(function(){
        game.network.load('landing/redirect');
    });
    // ## JS COMPRESS END ## //
</script>
