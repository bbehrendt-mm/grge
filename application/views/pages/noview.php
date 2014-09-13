<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>???</h1>

<div class="row" id="page-test-1">
    <div class="cell rw-8 ro-2 padded">
        <h2><?=__('Die Seite konnte nicht geladen werden!');?></h2>

        <?=__('Leider wurde diese Seite noch nicht für ::b::ZombVival Evolution::/b:: überarbeitet. Bitte kehre zur ::i::klassischen ZombVival-Webseite::/i:: zurück, um diese Seite aufzurufen.')?>
        <br /><br />
        <div class="row">
            <div class="cell rw-8 ro-4 padded">
                <a href="http://zvg.boerde.de" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-space-shuttle"></i></span><span><?=__('Zur klassischen Webseite');?></span></a>
            </div>
        </div>

    </div>
</div>
<script type="application/javascript">
    /*game.render.canvas.drawHTML($('#page-test-1').html(), $('#test-canvas'), function() {
        game.render.canvas.backup($('#test-canvas'));

        var loop = function() {
            var c = $('#test-canvas');
            game.render.canvas.restore(c);
            game.render.canvas.distort.apply(c, game.render.canvas.distort.filters.mpegDistortion(Math.random() > .5 ? 16 : 8,Math.random() > .80 ? 5 : 0, Math.random() >.5));

            window.requestAnimationFrame(loop);
        };

        window.requestAnimationFrame(loop);
    });*/
</script>