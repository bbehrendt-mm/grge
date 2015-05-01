<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Ein Fehler ist aufgetreten!')?></h1>

<div class="row">
    <div class="cell rw-12 padded">
        <b><?=__('Dein aktueller Spielstand ist beschädigt!'); ?></b><br />
        <?=__('Aufgrund eines Fehlers wurde dein aktueller Spielstand beschädigt. Keine Angst, deine Seelenpunkte, Auszeichnungen oder Ranking-Plätze sind nicht betroffen. Allerdings kannst du dein aktuelles Spiel möglicherweise nicht fortsetzen.'); ?><br /><br />
        <?=__('Um dieses Problem zu beheben, stehen dir mehrere Möglichkeiten offen.'); ?>
        <ul>
            <li><b><?=__('Logge dich aus und wieder ein.'); ?></b> <?=__('Die einfachsten Lösungen sind manchmal die effektivsten, und einen Versuch ist es allemal wert.'); ?></li>
            <li><b><?=__('Versuche es später erneut.'); ?></b> <?=__('Möglicherweise resultiert der Fehler aus einer Überlastung des Servers. Wenn die Seite gerade allgemein langsam reagiert, oder du zuvor eine Meldung mit dem Fehler "Unable to obtain database lock" erhalten hast, solltest du es in ein paar Minuten einfach erneut versuchen.'); ?></li>
            <li>
                <b><?=__('Lasse den Server das Problem automatisch beheben.'); ?></b>
                <?=__('Wenn du diese Option wählst, wird der beschädigte Spielstand einfach gelöscht, sodass du sofort ein neues Spiel beginnen kannst. Handelt es sich bei dem Spielstand um eine Mehrspieler-Partie betrifft diese Aktion nur dich, nicht jedoch die anderen Teilnehmer der Partie. Deine gesammelten Seelenpunkte, Auszeichnungen und Ranking-Plätze sind von dieser Aktion ebenfalls nicht betroffen.'); ?>
                <div class="btn" id="btn-fixme"><?=__('Automatisch beheben'); ?></div>
            </li>
            <li><b><?=__('Informiere den Administrator.'); ?></b> <?=__('Ein Administrator ist in jedem Fall in der Lage, zu helfen. Melde dich im Forum oder klicke :hier, um eine E-Mail an den Administrator zu senden.', array(':hier' => '<a href="mailto:kontakt@ruine.dvspot.de">' . __('hier') . '</a>')); ?></li>
        </ul><br />
        <b>
            <?=__('Bitte entschuldige diesen Fehler.'); ?>
        </b>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#btn-fixme').click(function() {
        game.network.query('japi/game/fixlink',{},function(data) {
            if (data.success == "1") {
                game.render.html.notify('success',<?=__j('Dein Spielstand wurde gelöscht. Du kannst nun ein neues Spiel beginnen.')?>);
                game.network.load('gamemaster/lobby');
            } else game.render.html.notify('error',<?=__j('Die automatische Reparatur ist fehlgeschlagen. Bitte kontaktiere einen Administrator!')?>);
        })
    });
// ## JS COMPRESS END ## //
</script>

