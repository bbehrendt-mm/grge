<?php
/**
 * @var string $name
 */
?>
<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Willkommen bei ZombVival')?></h1>

<div class="row">
    <div class="cell rw-12 padded center">
        <b><?=__('Hallo :name, schön dass du da bist!', [':name' => $name]);?></b>

        <p class="justify"><?=__('Es ist immer toll, jemand Neues bei ZombVival begrüßen zu dürfen. Ich hoffe, dass du an diesem kleinen, unabhängig entwickelten und vollständig kostenlosen Spiel viel Spaß und Freude haben wirst.');?></p>

        <p class="justify"><?=__('Bevor es losgeht, würde ich gerne noch von dir wissen, wie du hierher gefunden hast. Falls dich ein anderer Spieler geworben hat, so könntest du ihm einen Gefallen tun, indem du seine Mentoren-Referenznummer eingibst.');?></p>
    </div>

    <div class="cell rw-8 ro-2 rw-lg-10 ro-lg-1 rw-md-12 ro-md-0 padded">
        <form>
            <div class="row">
                <div class="cell rw-1 padded right" style="padding-top: 20px;">
                    <input type="radio" name="sfrom" id="sfrom_1" value="2" />
                </div>
                <div class="cell rw-11 padded">
                    <label for="sfrom_1"><?=__('Jemand hat mich geworben. Seine Mentoren-Referenznummer lautet:');?></label><br />
                    <input class="form_input disabled" id="sfrom_ref" placeholder="<?=__('Mentoren-Referenznummer');?>" />
                </div>
            </div>

            <div class="row">
                <div class="cell rw-1 padded right" style="padding-top: 20px;">
                    <input type="radio" name="sfrom" id="sfrom_2"  value="1" />
                </div>
                <div class="cell rw-11 padded">
                    <label for="sfrom_2"><?=__('Jemand hat mich geworben, aber ich habe die Referenznummer nicht zur Hand.');?></label><br />
                </div>
            </div>

            <div class="row hidden" id="msgExplain">
                <div class="cell rw-1 padded right">&nbsp;</div>
                <div class="cell rw-11 padded">
                    <div class="note">
                        <?=__('Das macht nichts. Du kannst später einfach das Profil des Spielers aufsuchen, der dich geworben hat, und ihn dort als deinen Mentor hinzufügen.');?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="cell rw-1 padded right" style="padding-top: 20px;">
                    <input type="radio" name="sfrom" id="sfrom_3"  value="-1" />
                </div>
                <div class="cell rw-11 padded">
                    <label for="sfrom_3"><?=__('Ich habe alleine hergefunden.');?></label><br />
                </div>
            </div>

            <div class="row">
                <div class="cell rw-1 padded right" style="padding-top: 20px;">
                    <input type="radio" name="sfrom" id="sfrom_4"  value="0" />
                </div>
                <div class="cell rw-11 padded">
                    <label for="sfrom_4"><?=__('Jemand hat mich unter Drogen gesetzt und ich bin gerade hier aufgewacht! Wo bin ich? Und wo ist meine Hose??');?></label><br />
                </div>
            </div>
        </form>

    </div>

    <div class="cell rw-12 padded center">
        <p class="justify"><?=__('Wenn du einen Überblick über das Spiel und ein paar Tipps für den Start suchst, empfehle ich das ZombVival-Wiki. Hast du Fragen, Ideen, Kritik oder möchtest einen Fehler melden, schau am besten im Forum vorbei.');?></p>

        <p class="justify">
            <?=__('So, jetzt aber genug geplaudert! Viel Spaß mit dem Spiel wünschen dir');?><br />
            <i><?=__('Brainbox und der Rest des ZV-Teams');?></i>
        </p>
    </div>

    <div class="cell rw-4 ro-8 rw-md-6 ro-md-6 rw-sm-12 ro-sm-0 padded">
        <div class="btn disabled" id="btn_confirm"><?=__('Weiter');?></div>
    </div>
</div>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('[name=sfrom]').click(function() {
        var v = $(this).val();

        if (v == 1)
            $('#msgExplain').slideDown();
        else $('#msgExplain').slideUp();

        if (v == 2)
            $('#sfrom_ref').removeClass('disabled');
        else $('#sfrom_ref').addClass('disabled');

        $('#btn_confirm').removeClass('disabled');
    });

    $('#btn_confirm').click(function() {
        var v = $('[name=sfrom]:checked').val();
        var m = $('#sfrom_ref').val();

        var confirm = $('#btn_confirm').addClass('disabled');

        if (v == 2 && !m) {
            game.render.html.notify('error', <?=__j('Bitte gib die Mentoren-Referenznummer an.')?>);
            confirm.removeClass('disabled');
        } else if (v == 2)
            game.network.query('japi/account/mentorize', {mrk: m}, function(data) {
                if (data.success) {
                    game.network.load('lobby/main');
                } else {
                    confirm.removeClass('disabled');
                    game.render.html.notify('error', <?=__j('Die Referenznummer scheint nicht korrekt zu sein.')?>);
                }
            });
        else if (v == -1)
            game.network.query('japi/account/mentorize', {uid: -1}, function() {
                game.network.load('lobby/main');
            });
        else game.network.load('lobby/main');
    });
// ## JS COMPRESS END ## //
</script>

