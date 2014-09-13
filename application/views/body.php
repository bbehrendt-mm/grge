<div id="navbar">
    <div class="navsection navtext" id="lang-select">
        <img class="pointer" src="media/icons/lang/de.png" alt="de" title="<b>Deutsch</b><br />Standartsprache" />
        <img class="pointer" src="media/icons/lang/en.png" alt="en" title="<b>English</b><br />Translation by Brainbox" />
    </div>

    <div class="navsection navtext" id="main-menu"></div>
</div>

<div id="notifications"></div>

<div id="wrapper">
    <div></div>

    <div id="content"></div>
</div>

<div id="disclaimer" class="row">
    <div class="cell rw-6">
        <b><?=__('ZombVival');?></b><br />
        <i class="min"><?=__('Ein postapokalyptisches Survival-Spiel. Könnte Spuren von Zombies enthalten...');?></i><br />
        <?=__('Dieses Spiel ist ein Fanprojekt zum Browsergame ":url" von :mt. Alle Grafiken stammen, wenn nicht ausdrücklich anders angegeben, ebenfalls aus dem Spiel und sind damit Eigentum von :mt.',
            array(
                ':url' => '<a href="http://dieverdammten.de">Die Verdammten</a>',
                ':mt' => '<b>MotionTwin</b>'
            )); ?>
        <?=__('Unterstütze ZombVival!')?> <a onclick="game.xmlhttp.load('donate');"><?=__('Spenden')?> </a>
    </div>

    <div class="cell rw-3">
        <b><?=__('Programm und Design');?></b><br />
        Benjamin "<i>Brainbox</i>" Behrendt<br /><br />
        <b><?=__('Danke an');?></b><br />
        <i>MisterD</i>, <i>Krummy</i>, <i>SinSniper</i>, <i>NobbZ</i>
    </div>

    <div class="cell rw-3">
        <b><?=__('Kontakt'); ?></b><br />
        <a href="mailto: kontakt@ruine.dvspot.de">kontakt@ruine.dvspot.de</a><br />
        <img src="media/img/small.png" alt="Zombvival">
        <i class="min" style="cursor: pointer" onclick="game.xmlhttp.load('admin/view');">[Back-End]</i>
    </div>
</div>

<script type="application/javascript">
    $('#lang-select').find('> img').qtip(game.render.html.qtip.lang()).click(function() {
        game.lang($(this).attr('alt'));
        game.network.load('web/body');
    });
    game.network.load('landing/redirect');
</script>