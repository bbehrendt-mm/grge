<?php
/**
 * @var int $remaining Seconds until the pause can be disabled
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Spiel pausiert')?></h1>



<div class="row">
    <div class="cell rw-5 padded justify">

        <div class="center">
            <img id="sleepby" src="media/img/zombie_sleep.gif" />
        </div>

        <div id="unpause_btn" class="btn"><?=__('Spiel fortsetzen');?></div>
        <span id="blocktext"></span>

    </div>
    <div class="cell rw-7 padded">
        <div class="help noclick">
            <h4><?=__('Pause')?></h4>
            <?=__('Beim klassischen Zeitfluss hast du jederzeit die Möglichkeit, das Spiel vollständig zu pausieren. Ist das Spiel pausiert, kannst du keinerlei Aktionen durchführen. Dafür werden auch die Ticks angehalten.');?><br /><br />
            <?=__('Hast du die Pause aktiviert, musst du eine bestimmte Zeit warten, bis du sie wieder deaktivieren kannst. Es gibt jedoch keine Maximaldauer für eine Pause - du kannst das spiel also so lange pausieren, wie du möchtest.');?>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
(function() {
    $('#persistent').empty();

    var s = <?=(int)$remaining?>;
    var btn = $('#unpause_btn');
    var txt = $('#blocktext');

    var z = function(i) {
        var zomb = $('#sleepby');

        if (!zomb.length) return;

        var from = {top: zomb.position().top + 10, left: zomb.position().left + 10};
        var dif = {top: -50 - Math.random() * 20, left: 30 + Math.random() * 15};

        $('<div />').addClass('sleep-animation').css({
            left: from.left,
            top: from.top,
            'transform': 'scale(0)',
            opacity: 0
        }).prependTo(zomb.parent()).animate({
            'transform': 'scale(0.8)',
            opacity: 1,
            left: from.left + dif.left * 0.75,
            top: from.top + dif.top * 0.75
        }, 800, 'linear', function() {
            $(this).animate({
                'transform': 'scale(1)',
                opacity: 0,
                left: from.left + dif.left,
                top: from.top + dif.top
            }, 200, 'linear', function() {
                $(this).remove();
            })
        });

        window.setTimeout(function() {z(i == 5 ? 0 : i+1)},i == 5 ? 1500 : 250)
    };

    z(0);

    if (s) {
        btn.addClass('disabled');
        core.snippets.countdown(s, function(str, v) {
            if (v == 0) {
                btn.removeClass('disabled');
                txt.remove();
                game.render.html.notify('success', <?=__j('Die Sperre ist abgelaufen - du kannst deine Pause nun beenden!')?>);
                return false;
            }
            txt.html(game.i18n(<?=__j('Du kannst diese Pause in ::i:: :time ::/i:: beenden.')?>, {':time': str}));
            return true;
        })
    }

    btn.click(function() {
        if (confirm(<?=__j('Möchtest du dein Spiel jetzt fortsetzen?')?>))
            core.command('player/pause', {set: 0});
    });
})();
// ## JS COMPRESS END ## //
</script>

