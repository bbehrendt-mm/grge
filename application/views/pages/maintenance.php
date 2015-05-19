<?php
/**
 * @var array $slot
 */

    $mnt_begin = strtotime("+{$slot[0]} minutes", strtotime('today'));
    $mnt_end = strtotime("+{$slot[1]} minutes", strtotime('today'));
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Tägliche Wartung')?></h1>

<div class="row">
    <div class="cell rw-10 ro-1 padded">
        <h2><?=__('Im Moment wird die tägliche Wartung duchgeführt.');?></h2>
        <span><?=__('Für die Dauer der Wartung ist der Spielserver nicht erreichbar!');?></span><br /><br />

        <b><?=__('Hier ist alles, was du über die tägliche Wartung wissen musst:');?></b>
        <ul>
            <li><?=__('Sie findet täglich zwischen :utcbegin und :utcend Uhr deutscher Zeit (:localbegin und :localend Uhr in deiner Zeitzone) statt.',
                    [
                        ':utcbegin' => date('G:i', $mnt_begin),
                        ':utcend' => date('G:i', $mnt_end),
                        ':localbegin' => '<b id="tconv_begin"></b>',
                        ':localend' => '<b id="tconv_end"></b>'
                    ]);?>
            </li>
            <li><?=__('Während der Wartung werden alle Spiele angehalten - du brauchst alo keine Angst haben, etwas zu verpassen.');?></li>
            <li><?=__('Die tägliche Wartung ist nötig, um einige automatisierte Scripte zur Optimierung des Servers durchzuführen.');?></li>
        </ul>

        <div class="row">
            <div class="cell rw-4 ro-8">
                <div id="return_to_page" class="btn"><?=__('Seite neu laden');?></div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    // ## JS COMPRESS BEGIN ## //
    (function() {
        var lc_begin = new Date(<?=$mnt_begin*1000?>);
        var lc_end = new Date(<?=$mnt_end*1000?>);

        function pd(num, len) {
            while((""+num).length < len) {num = "0" + num;}
            return num;
        }

        $('#tconv_begin').text(pd(lc_begin.getHours(),2) + ':' + pd(lc_begin.getMinutes(),2));
        $('#tconv_end').text(pd(lc_end.getHours(),2) + ':' + pd(lc_end.getMinutes(),2));

        $('#return_to_page').click(function(){
            game.network.load('landing/redirect');
        });
    })();
    // ## JS COMPRESS END ## //
</script>
