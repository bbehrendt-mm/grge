<?php
/**
 * @var int $season
 * @var string $title
 * @var bool $beta
 * @var string $event
 * @var string $version
 */
?>
<div id="static">
    <div id="navbar">
        <div class="navsection navtext" id="lang-select">
            <img class="pointer" src="media/icons/lang/de.png" alt="de" title="<b>Deutsch</b><br />Standartsprache" />
            <img class="pointer" src="media/icons/lang/en.png" alt="en" title="<b>English</b><br />Translation by Brainbox" />
            <img class="pointer" src="media/icons/lang/es.png" alt="es" title="<b>Español</b><br />Sin terminar! Si usted desea ayudar, por favor póngase en contacto con Brainbox." />
            <img class="pointer" src="media/icons/lang/fr.png" alt="fr" title="<b>Français</b><br />Non terminé! Pour vous aider, contactez Brainbox!" />
        </div>

        <div class="navsection navtext" id="main-menu" data-toggle="0"></div>

        <div class="navsection navtext hide-desktop" id="nav-toggle"><i class="fa fa-bars"></i> </div>
    </div>

    <div id="notifications"></div>

    <div id="hint"></div>

    <div id="wrapper">
        <div>
            <div id="infoband" title="<?=$version?>">
                <?php if ($event) { ?>
                    <span class="hide-mobile"><?=__('Season :num', [':num' => '<b>' . $season . ($beta ? ' BETA' : '') . '</b>']);?> - </span>
                    <span><i><b><?=__($event);?></b></i></span>
                <?php } else { ?>
                    <span><?=__('Season :num', [':num' => '<b>' . $season . ($beta ? ' BETA' : '') . '</b>']);?></span>
                    <span class="hide-mobile"> - <i><?=__($title);?></i></span>
                <?php } ?>
            </div>
            <div id="persistent"></div>
        </div>

        <div id="content">
            <div class="center"><i class="fa fa-circle-o-notch fa-spin"></i> <?=__('Sprachabhängige Scripte werden nachgeladen...')?></div>
        </div>
    </div>

    <div id="disclaimer" class="row">
        <div class="cell rw-6 rw-lg-12 padded-lg">
            <b><?=__('ZombVival');?></b><br />
            <i class="min"><?=__('Ein postapokalyptisches Survival-Spiel. Könnte Spuren von Zombies enthalten...');?></i><br />
            <div class="hide-desktop"><img src="media/img/small.png" alt="Zombvival"></div>
            <?=__('Dieses Spiel ist ein Fanprojekt zum Browsergame ":url" von :mt. Alle Grafiken stammen, wenn nicht ausdrücklich anders angegeben, ebenfalls aus dem Spiel und sind damit Eigentum von :mt.',
                array(
                    ':url' => '<a href="http://dieverdammten.de">Die Verdammten</a>',
                    ':mt' => '<b>MotionTwin</b>'
                )); ?>
            <?=__('Unterstütze ZombVival!')?> <a href="#" id="main_donate"><?=__('Spenden')?> </a>
        </div>

        <div class="cell rw-3 rw-lg-6 rw-sm-12 padded-lg">
            <b><?=__('Programm und Design');?></b><br />
            Benjamin "<i>Brainbox</i>" Behrendt<br /><br />
            <b><?=__('Danke an');?></b><br />
            <i>MisterD</i>, <i>Krummy</i>, <i>SinSniper</i>, <i>NobbZ</i>, <i>Mastertron</i>, <i>Storm</i>
        </div>

        <div class="cell rw-3 rw-lg-6 rw-sm-12 padded-lg">
            <b><?=__('Kontakt'); ?></b><br />
            <a href="mailto: kontakt@ruine.dvspot.de">kontakt@ruine.dvspot.de</a><br />
            <div class="hide-mobile"><img src="media/img/small.png" alt="Zombvival"></div>
            <i id="main_backend" class="min pointer">[Back-End]</i>
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

    $('#nav-toggle').add('#main-menu').click(function() {
        var t = $('#main-menu');
        t.attr('data-toggle', t.attr('data-toggle') == '0' ? '1' : '0');
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
        var scroll = $(document).scrollTop();
        var p = $('#persistent');
        if (scroll > (95 - p.height()))
            p.addClass('float');
        else p.removeClass('float');
    });

    $(window).on('popstate', function(e) {
        if (e.originalEvent.state.curl)
            game.network.load(e.originalEvent.state.curl);
    });

    $.ajax({
        url: 'web/core/?l=' + game.lang(),
        dataType: "script",
        headers: { 'X-Skip-ETag': game.storage.get('update','force_next_update',false) ? '1' : '0' },
        cache: true,
        success: function() {
            game.storage.set('update','force_next_update',false);
            game.network.load('landing/redirect');
        }
    }).fail(function( jqxhr, settings, exception ) {
        $('#content').empty()
            .append($('<div />').addClass('center').text(exception.message));
    });

// ## JS COMPRESS END ## //
</script>