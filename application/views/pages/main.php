<?php
/**
 * @var bool $ingame If user is in a game
 * @var string $avatar
 * @var string $name
 * @var int $pupils
 * @var int $bc
 */
?>
<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Willkommen')?></h1>

<div class="row">
    <div class="cell rw-12 padded">

        <div class="row center">
            <div class="cell rw-2 rw-lg-0 padded nopad-lg left">
                <div class="framed main inline-block"><img class="avatar" src="<?=$avatar ? $avatar : 'media/img/mugshot.png'?>" alt="<?=$name?>" /></div>
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



        <h2 class="center"><?=__('Neuigkeiten')?></h2>
        <div id="newsboard"></div>
    </div>

</div>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    game.storage.set('news','last-seen',Date.now());

    $('#game-btn').click(function() {
        game.network.load('<?=$ingame ? 'game/redirect' : 'gamemaster/lobby' ?>');
    });

    $('#profile-btn').click(function() {
        game.network.load('ranking/soul');
    });

    $('#mentor_help').attr('title', <?=__j('Wirbst du einen neuen Spieler für ZombVival, so kann dieser zu deinem Schüler werden, indem er dich nach dem ersten Login als seinen Mentor auswählt. Danach verdienst du jedes mal, wenn dieser Spieler Seelenpunkte erwirbt, ein paar BrainCoins. Weitere Informationen hierzu erhälst du in deinem Profil. Dort findest du auch eine Übersicht über alle deine Schüler, kannst deine Mentoren-Referenznummer nachlesen und deine durch Schüler verdienten BrainCoins einsammeln.')?>).qtip(game.render.html.qtip.help('bottom'));

    var load_news = function(p, first) {
        $('#newstmp').remove();
        $('#newsboard').append('<div id="newstmp" class="center"><i class="fa fa-spin fa-circle-o-notch"></i><br /><?=__('Ladevorgang');?></div>');
        game.network.query('japi/lobby/feedproxy', {page: p}, function(data) {
            $('#newstmp').remove();
            if (data.error) {
                $('#newsboard').append($('<div id="newstmp" class="center"><?=__('Der Nachrichtendienst steht derzeit nicht zur Verfügung.');?></div>').append('<br />').append($('<div class="btn small"><?=__('Erneut versuchen');?></div>').click(function() {
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
                                $('<span class="pointer"><i class="fa fa-comment"></i> ' + (!this.posts ? '<?=__('Keine Kommentare');?>' : (this.posts == 1 ? '<?=__('1 Kommentar');?>' : game.i18n('<?=__(':num Kommentare');?>', {':num': this.posts}))) + '</span>').click(function() {
                                    window.open(view);
                                })
                            ).append(
                                $('<span class="pointer"><?=__('Kommentieren');?></span>').click(function() {
                                    window.open(response);
                                })
                            )
                        )
                    )
                });

                if (i==0 && first)
                    $('#newsboard').append('<div id="newstmp" class="center"><?=__('Es gibt gerade nichts Neues.');?></div>');
                else if (i==0)
                    $('#newsboard').append('<div id="newstmp" class="center"><?=__('Es gibt keine weiteren Neuigkeiten.');?></div>');
                else
                    $('#newsboard').append($('<div id="newstmp" class="center"></div>').append($('<div class="btn"><?=__('Ältere Artikel anzeigen');?></div>').click(function() {
                        load_news(p+1);
                    })));
            }
        });
    };

    load_news(1,true);
// ## JS COMPRESS END ## //
</script>

