<div id="static">
    <div id="navbar">
        <div class="navsection navtext" id="lang-select">
            <img class="pointer" src="media/icons/lang/de.png" alt="de" title="<b>Deutsch</b><br />Standartsprache" />
            <img class="pointer" src="media/icons/lang/en.png" alt="en" title="<b>English</b><br />Translation by Brainbox" />
            <img class="pointer" src="media/icons/lang/es.png" alt="es" title="<b>Español</b><br />Sin terminar! Si usted desea ayudar, por favor póngase en contacto con Brainbox." />
        </div>

        <div class="navsection navtext" id="main-menu"></div>
    </div>

    <div id="notifications"></div>

    <div id="hint"></div>

    <div id="wrapper">
        <div>
            <div id="persistent"></div>
        </div>

        <div id="content">
            <div class="center"><i class="fa fa-circle-o-notch fa-spin"></i> <?=__('Sprachabhängige Scripte werden nachgeladen...')?></div>
        </div>
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
            <?=__('Unterstütze ZombVival!')?> <a href="#" id="main_donate"><?=__('Spenden')?> </a>
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
            <i id="main_backend" class="min pointer" style="cursor: pointer">[Back-End]</i>
        </div>

        <div class="cell rw-10 ro-1 center">
            <b><?=__('Eine Übersicht über Bilder anderer Autoren findet sich hier:')?></b> <a href="#" id="license_link"><?=__('Bildmaterial')?></a>
        </div>
    </div>


</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#lang-select').find('> img').qtip(game.render.html.qtip.lang()).click(function() {
        game.lang($(this).attr('alt'));
        game.network.load('web/body');
    });

    $('#license_link').click(function() {
        game.network.load('account/license');
    });

    $('#main_donate').click(function() {
        alert('Coming soon!');
    });

    $('#main_backend').click(function() {
        game.network.load('admin/account/login');
    });

    $(window).scroll(function() {
        var scroll = $('body').scrollTop();
        var p = $('#persistent');
        if (scroll > (95 - p.height()))
            p.addClass('float');
        else p.removeClass('float');
    });

    $.getScript('web/core/?l=' + game.lang(), function() {
        game.network.load('landing/redirect');
    }).fail(function( jqxhr, settings, exception ) {
        $('#content').empty()
            .append($('<div />').addClass('center').text(exception.message));
    });

// ## JS COMPRESS END ## //
</script>