<?php
/**
 * @var bool $ingame If user is in a game
 */
?>
<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Neuigkeiten')?></h1>

<div class="row">
    <div class="cell rw-12 padded">
        <div class="btn" id="game-btn"><?=$ingame ? __('Zurück zum Spiel') : __('Ein Spiel starten') ?></div><br />
        <div id="newsboard"></div>
    </div>

</div>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#game-btn').click(function() {
        game.network.load('<?=$ingame ? 'game/redirect' : 'gamemaster/lobby' ?>');
    });

    var load_news = function(p, first) {
        $('#newstmp').remove();
        $('#newsboard').append('<div id="newstmp" class="center"><i class="fa fa-spin fa-circle-o-notch"></i><br />Ladevorgang...</div>');
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

