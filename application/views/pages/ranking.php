<?php
/**
 * @var array $sp_modes Single player modes
 * @var array $mp_modes Multiplayer modes
 * @var number $season Current season
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Ranking');?></h1>

<div class="row">
    <h2>Bitte wähle, welches Ranking du sehen möchtest.</h2>

    <div class="cell rw-3 padded">
        <label for="game_type"></label><select class="form_input" id="game_type">
            <option value="1"><?=__('Einzelspieler');?></option>
            <option value="2"><?=__('Mehrspieler');?></option>
        </select>
    </div>

    <div class="cell rw-3 padded">
        <label for="game_season"></label><select class="form_input" id="game_season">
            <option value="<?=$season;?>"><?=__('Season :num', [':num' => $season]);?></option>
        </select>
    </div>
    <div class="cell rw-3 padded">
        <label for="game_mode"></label><select class="form_input" id="game_mode"></select>
    </div>
    <div class="cell rw-3 padded">
        <label for="game_time"></label><select class="form_input" id="game_time"></select>
    </div>
</div>

<div id="ranking_target" class="center"></div>

<script type="application/javascript">
    var game_type_elem = $('#game_type').change(function() {
        var gm = $('#game_mode').empty();
        var gs = $('#game_season');
        var gt = $('#game_time');

        var season_backup = Math.max(($(this).val() == "2" ? 4 : 0),parseInt(gs.val()));
        gs.empty();

        var modes = [];
        switch($(this).val()) {
            case "1":
                modes = <?=json_encode($sp_modes, JSON_FORCE_OBJECT)?>;
                gt.html('<option value="0"><?=__('Klassischer Zeitfluss')?></option><option value="1"><?=__('Variabler Zeitfluss')?></option>');
                break;
            case "2":
                modes = <?=json_encode($mp_modes, JSON_FORCE_OBJECT)?>;
                gt.html('<option value="1"><?=__('Variabler Zeitfluss')?></option>');
                break;
        }
        $.each(modes, function(id,name) {
            $('<option value="' + id + '">' + name + '</option>').appendTo(gm);
        });
        for (var i = ($(this).val() == "2" ? 4 : 0); i <= <?=$season?>; i++)
            $('<option value="' + i + '">' + game.i18n('<?=__('Season :num');?>', {':num': i}) + '</option>').prependTo(gs);
        gs.val(season_backup);
    }).trigger('change');

    var fetch = function(is_mp, mode, time, season, offset) {
        $('#ranking_target').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        var selectors = $('#content').find('select:not(:disabled)').attr('disabled', 'disabled');

        game.network.query('japi/ranking/' + (is_mp ? 'multi' : 'single'), {
            mode: mode,
            time: time,
            season: season,
            length: 20,
            offset: offset
        }, function(data) {
            selectors.removeAttr('disabled');
            $('#ranking_target').empty();
            if (data.ranking && data.games) {
                var table = $('<div class="row-table padded row-table-borders row-table-striped row-table-interact"></div>').appendTo('#ranking_target');

                if (is_mp)
                    $('<div class="row"><div class="cell padded rw-2"><?=__('Platz')?></div><div class="cell padded rw-1"><?=__('Punkte')?></div><div class="cell padded rw-3"><?=__('Name')?></div><div class="cell padded rw-6"><?=__('Spieler')?></div></div>').appendTo(table);
                else $('<div class="row"><div class="cell padded rw-2"><?=__('Platz')?></div><div class="cell padded rw-1"><?=__('Punkte')?></div><div class="cell padded rw-2"><?=__('Spieldauer')?></div><div class="cell padded rw-3"><?=__('Spieler')?></div><div class="cell padded rw-4"><?=__('Beruf')?></div></div>').appendTo(table);

            } else $('<span><?=__('Es wurden keine Spiele im Ranking gefunden, die deinen Suchkriterien entsprechen.')?></span>').appendTo('#ranking_target');

            $.each(data.ranking, function(p, elem) {
                var entry = $('<div class="row"></div>');

                var fst = $('<div class="cell padded rw-1"></div>').appendTo(entry);
                if (p == 1) fst.html('<img src="media/icons/superstar.gif" alt="rk-winner">');
                else if (p <= 3) fst.html('<img src="media/icons/silverstar.gif" alt="rk-silver">');
                else if (p <= 10) fst.html('<img src="media/icons/star.gif" alt="rk-topten">');
                else fst.html('<i class="fa fa-star-o"></i>');

                $('<div class="cell padded rw-1"></div>').html(p).appendTo(entry);
                $('<div class="cell padded rw-1"></div>').html(elem.score).appendTo(entry);

                if (is_mp) {
                    $('<div class="cell padded rw-1"></div>').html(elem.score).appendTo(entry);
                    $('<div class="cell padded rw-3"></div>').html(elem.name).appendTo(entry);
                    var pl = $('<div class="cell padded rw-5"></div>').appendTo(entry);
                    if (elem.players)
                        $.each(elem.players, function(k,v) {
                            var player = $('<span class="inline-player">' + v.name + '</span>').appendTo(pl);

                            var qtmp = $('<div class="row"></div>');
                            $('<div class="cell padded rw-4"><b><?=__('Beruf');?></b></div>').appendTo(qtmp);
                            $('<div class="cell padded rw-6"></div>').html(v.job).appendTo(qtmp);

                            player.attr('title', qtmp.html()).qtip(game.render.html.qtip.player('top'));
                        });
                } else {
                    $('<div class="cell padded rw-2"></div>').html(elem.duration).appendTo(entry);
                    $('<div class="cell padded rw-3"></div>').html('<span class="inline-player">' + elem.players["0"].name + '</span>').appendTo(entry);
                    $('<div class="cell padded rw-4"></div>').html(elem.players["0"].job).appendTo(entry);
                }

                entry.appendTo(table);
            });
        });
    };

    $('#content').find('select').change(function() {
        var is_mp = $('#game_type').val() == "2";
        fetch(is_mp, $('#game_mode').val(), is_mp ? "1" : $('#game_time').val(), $('#game_season').val(), 0)
    });

    game_type_elem.trigger('change');
</script>