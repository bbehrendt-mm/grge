<?php
/**
 * @var bool $ingame If user is in a game
 * @var string $avatar
 * @var string $name
 * @var int $pupils
 * @var int $bc
 * @var array $current_events
 * @var array $next_events
 */
?>
<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Willkommen')?></h1>

<div class="row">
    <div class="cell rw-12 padded">

        <div class="row center">
            <div class="cell rw-2 rw-lg-0 padded nopad-lg left">
                <div class="framed main inline-block"><img class="avatar" src="<?=$avatar ?: 'media/img/mugshot.png'?>" alt="<?=$name?>" /></div>
            </div>

            <div class="cell rw-5 rw-lg-6 rw-md-12 left">
                <div class="row">
                    <div class="cell rw-7 padded left">
                        <?=__('Hallo :name', [':name' => '<span style="font-size: 20px; font-weight: bold">' . $name . '</span>']);?><br />
                        <?=__('Du hast :num Schüler.', [':num' => '<span style="font-size: 20px; font-weight: bold">' . $pupils . '</span>']);?>
                    </div>

                    <div class="cell rw-5 padded left">
                        <?=__('Kontostand');?><br />
                        <span style="font-size: 20px; font-weight: bold"><?=$bc?></span> <img src="media/icons/coin.gif" alt="BC" />
                    </div>
                </div>

                <div class="row">
                    <div class="cell rw-12 padded" style="font-size: 10px;">
                        <?=__('Wie man Schüler sammelt und mit ihnen Kohle verdient?');?> <a id="mentor_help" href="#"><?=__('Ganz einfach!');?></a>
                    </div>
                </div>
            </div>

            <div class="cell rw-5 rw-lg-6 rw-md-12">

                <div class="row">
                    <div class="cell rw-12 rw-md-6 padded">
                        <div class="btn" id="game-btn"><?=$ingame ? __('Zurück zum Spiel') : __('Ein Spiel starten') ?></div>
                    </div>
                    <div class="cell rw-12 rw-md-6 padded">
                        <div class="btn" id="profile-btn"><?=__('Mein Profil');?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($current_events) { ?>
            <h2 class="center"><?=__('Aktuelle Events')?></h2>
            <?php foreach ($current_events as $event) { ?>
                <div class="event-base event-current event-<?=$event['key']?>">
                    <div class="header">
                        <b><?=__($event['name'])?></b>
                        <i data-has-date="1" data-ts-start="<?=$event['begin']?>" data-ts-end="<?=$event['end']?>"></i>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if ($next_events) { ?>
            <h2 class="center"><?=__('Kommende Events')?></h2>
            <?php foreach ($next_events as $event) { ?>
                <div class="event-base event-upcoming event-<?=$event['key']?>">
                    <div class="header">
                        <b><?=__($event['name'])?></b>
                        <i data-has-date="1" data-ts-start="<?=$event['begin']?>" data-ts-end="<?=$event['end']?>"></i>
                    </div>
                    <div class="content">
                        <div>

                        </div>
                        <div>
                            <span data-ts-start="<?=$event['begin']?>"></span><br />
                            <span data-ts-end="<?=$event['end']?>"></span><br />
                            <b><?=__('Alle Zeitangaben sind in lokaler Zeit.');?></b>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <h2 class="center"><?=__('Neuigkeiten')?></h2>
        <div id="newsboard"></div>
    </div>

</div>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    game.storage.set('news','last-seen',Date.now());

    var update_event_dates = function() {};
    update_event_dates = function() {
        var updated = false;
        $('.event-base [data-has-date]').each(function() {
            var now = Date.now();
            var begin = parseInt($(this).data('ts-start')) * 1000;
            var end = parseInt($(this).data('ts-end')) * 1000;

            if (end < now || end <= begin) return;

            var event_running = begin <= now && end > now;
            var ts = (event_running ? end : begin) - now;

            var days =    Math.floor( ts / 86400000 );
            var hours =   Math.floor( (ts-86400000*days) / 3600000 );
            var minutes = Math.floor( (ts-86400000*days-3600000*hours) / 60000 );
            var seconds = Math.floor( (ts-86400000*days-3600000*hours-minutes*60000) / 1000 );

            var base_str;
            if      (days > 1)   base_str = event_running ? <?=__j(':n_days Tage');?> : <?=__j(':n_days Tagen');?>;
            else if (days === 1) base_str = <?=__j(':n_days Tag');?>;
            else                 base_str = <?=__j(':n_hours::n_mins::n_secs');?>;

            if (minutes < 10) minutes = '0' + minutes;
            if (seconds < 10) seconds = '0' + seconds;

            base_str = game.i18n( base_str, { ':n_days': days, ':n_hours': hours, ':n_mins': minutes, ':n_secs': seconds} );

            $(this).text(  game.i18n(
                event_running ? <?=__j('Noch :time');?> : <?=__j('Startet in :time');?>, {':time': base_str})
            );

            updated = true;
        });
        if (updated) window.setTimeout(update_event_dates, 1000);
    };
    update_event_dates();

    $('.event-base .content [data-ts-start]').each(function() {
        var ts = parseInt($(this).data('ts-start')) * 1000;
        $(this).text(game.i18n(<?=__j('Startet :datetime')?>, {
            ':datetime': (new Date( ts )).toLocaleString()
        }) );
    });

    $('.event-base .content [data-ts-end]').each(function() {
        var ts = parseInt($(this).data('ts-end')) * 1000;
        $(this).text(game.i18n(<?=__j('Endet :datetime')?>, {
            ':datetime': (new Date( ts )).toLocaleString()
    }) );
});

    $('#game-btn').click(function() {
        game.network.load('<?=$ingame ? 'game/redirect' : 'gamemaster/lobby' ?>');
    });

    $('#profile-btn').click(function() {
        game.network.load('ranking/soul');
    });

    $('#mentor_help').attr('title', <?=__j('Wirbst du einen neuen Spieler für ZombVival, so kann dieser zu deinem Schüler werden, indem er dich nach dem ersten Login als seinen Mentor auswählt. Danach verdienst du jedes mal, wenn dieser Spieler Seelenpunkte erwirbt, ein paar BrainCoins. Weitere Informationen hierzu erhälst du in deinem Profil. Dort findest du auch eine Übersicht über alle deine Schüler, kannst deine Mentoren-Referenznummer nachlesen und deine durch Schüler verdienten BrainCoins einsammeln.')?>).qtip(game.render.html.qtip.help('bottom'));

    var load_news = function(p, first) {
        $('#newstmp').remove();
        $('#newsboard').append($('<div id="newstmp" class="center"><i class="fa fa-spin fa-circle-o-notch"></i><br /></div>').append($('<span />').text(<?=__j('Ladevorgang');?>)));
        game.network.query('japi/lobby/feedproxy', {page: p}, function(data) {
            $('#newstmp').remove();
            if (data.error) {
                $('#newsboard').append($('<div id="newstmp" class="center" />').text(<?=__j('Der Nachrichtendienst steht derzeit nicht zur Verfügung.');?>).append('<br />').append($('<div class="btn small" />').text(<?=__j('Erneut versuchen');?>).click(function() {
                    load_news(p);
                })));
            } else {
                var i = 0;
                if (data.feeds) $.each(data.feeds, function() {
                    var response = this.response;
                    var view = this.view;
                    i++;
                    $('#newsboard').append(
                        $('<div class="article"/>').append(
                            $('<div/>').append(
                                $('<h3/>').html(this.content.subject)
                            ).append(
                                $('<span/>').html('<b>' + this.author.name + '</b>, ' + this.date)
                            )
                        ).append(
                            $('<div/>').html(this.content.text)
                        ).append(
                            $('<div/>').append(
                                $('<span class="pointer"><i class="fa fa-comment"></i> ' + (!this.posts ? <?=__j('Keine Kommentare');?> : (this.posts == 1 ? <?=__j('1 Kommentar');?> : game.i18n(<?=__j(':num Kommentare');?>, {':num': this.posts}))) + '</span>').click(function() {
                                    window.open(view);
                                })
                            ).append(
                                $('<span class="pointer" />').text(<?=__j('Kommentieren');?>).click(function() {
                                    window.open(response);
                                })
                            )
                        )
                    )
                });

                if (i==0 && first)
                    $('#newsboard').append($('<div id="newstmp" class="center" />').text(<?=__j('Es gibt gerade nichts Neues.');?>));
                else if (i==0)
                    $('#newsboard').append($('<div id="newstmp" class="center" />').text(<?=__j('Es gibt keine weiteren Neuigkeiten.');?>));
                else
                    $('#newsboard').append($('<div id="newstmp" class="center"></div>').append($('<div class="btn">').text(<?=__j('Ältere Artikel anzeigen');?>).click(function() {
                        load_news(p+1);
                    })));
            }
        });
    };

    load_news(1,true);
// ## JS COMPRESS END ## //
</script>

