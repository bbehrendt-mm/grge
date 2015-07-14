<?php
/**
 * @var array $sp_modes Single player modes
 * @var array $mp_modes Multiplayer modes
 * @var number $season Current season
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Season-Ranking');?></h1>

<div class="row">
    <h2><?=__('Bitte wähle, welches Ranking du sehen möchtest.');?></h2>

    <div class="cell rw-3 rw-md-6 padded">
        <label for="game_type"></label><select class="form_input" id="game_type" data-container="body">
            <option value="1"><?=__('Einzelspieler');?></option>
            <option value="2"><?=__('Mehrspieler');?></option>
        </select>
    </div>

    <div class="cell rw-3 rw-md-6 padded">
        <label for="game_season"></label><select class="form_input" id="game_season" data-container="body">
            <option value="<?=$season;?>"><?=__('Season :num', [':num' => $season]);?></option>
        </select>
    </div>
    <div class="cell rw-3 rw-md-6 padded">
        <label for="game_mode"></label><select class="form_input" id="game_mode" data-container="body"></select>
    </div>
    <div class="cell rw-3 rw-md-6 padded">
        <label for="game_time"></label><select class="form_input" id="game_time" data-container="body"></select>
    </div>

    <div class="cell rw-12 right">
        <div id="btn_switch_global" class="btn small"><?=__('Zum globalen Ranking wechseln');?></div>
    </div>
</div>

<div class="row center navigation">
    <div class="btn small" title="<?=__('Zum Anfang');?>"><i class="fa fa-angle-double-left"></i></div>
    <div class="btn small" title="<?=__('Eine Seite zurück');?>"><i class="fa fa-angle-left"></i></div>
    <div class="btn small" title="<?=__('Zu bestimmter Seite springen');?>"></div>
    <div class="btn small" title="<?=__('Eine Seite weiter');?>"><i class="fa fa-angle-right"></i></div>
    <div class="btn small" title="<?=__('Zum Ende');?>"><i class="fa fa-angle-double-right"></i></div>
</div>

<div id="ranking_target" class="center"></div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
(function() {
    var game_type_elem = $('#game_type').change(function () {
        var gm = $('#game_mode').empty();
        var gs = $('#game_season');
        var gt = $('#game_time');

        var season_backup = Math.max(($(this).val() == "2" ? 4 : 0), parseInt(gs.val()));
        gs.empty();

        var modes = [];
        switch ($(this).val()) {
            case "1":
                modes = <?=json_encode($sp_modes, JSON_FORCE_OBJECT)?>;
                gt.html('<option value="0"><?=__('Klassischer Zeitfluss')?></option><option value="1"><?=__('Variabler Zeitfluss')?></option>');
                break;
            case "2":
                modes = <?=json_encode($mp_modes, JSON_FORCE_OBJECT)?>;
                gt.html('<option value="1"><?=__('Variabler Zeitfluss')?></option>');
                break;
            default:
                break;
        }
        $.each(modes, function (id, name) {
            $('<option value="' + id + '">' + name + '</option>').appendTo(gm);
        });
        for (var i = ($(this).val() == "2" ? 4 : 0); i <= <?=$season?>; i++)
            $('<option value="' + i + '">' + game.i18n('<?=__('Season :num');?>', {':num': i}) + '</option>').prependTo(gs);
        gs.val(season_backup);

        $('#content').find('select').selectric('refresh');

    }).trigger('change');

    var read_fetch = function (offset) {
        return function () {
            var is_mp = $('#game_type').val() == "2";
            fetch(is_mp, $('#game_mode').val(), is_mp ? "1" : $('#game_time').val(), $('#game_season').val(), Math.max(0, offset))
        }
    };

    var fetch = function (is_mp, mode, time, season, offset) {
        $('#ranking_target').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        var content = $('#content');
        var selectors = content.find('select:not(:disabled)').attr('disabled', 'disabled');
        var navigation = content.find('.navigation > div').addClass('btn-disabled').off('click');

        game.network.query('japi/ranking/' + (is_mp ? 'multi' : 'single'), {
            mode: mode,
            time: time,
            season: season,
            length: 20,
            offset: offset
        }, function (data) {
            selectors.removeAttr('disabled');

            $('#ranking_target').empty();
            if (data.ranking && data.games) {
                var table = $('<div class="row-table padded row-table-borders row-table-striped row-table-interact"></div>').appendTo('#ranking_target');

                if (is_mp)
                    $('<div class="row"><div class="cell padded rw-2 rw-lg-1"><span class="hide-md hide-sm"><?=__('Platz')?></span><span class="hide-desktop hide-lg">#</span></div><div class="cell padded rw-1"><?=__('Punkte')?></div><div class="cell padded rw-3 rw-lg-4 rw-sm-0"><?=__('Name')?></div><div class="cell padded rw-6 rw-sm-10"><?=__('Spieler')?></div></div>').appendTo(table);
                else $('<div class="row"><div class="cell padded rw-2 rw-lg-1"><span class="hide-md hide-sm"><?=__('Platz')?></span><span class="hide-desktop hide-lg">#</span></div><div class="cell padded rw-1"><?=__('Punkte')?></div><div class="cell padded rw-2 rw-lg-3 rw-md-0"><?=__('Spieldauer')?></div><div class="cell padded rw-3 rw-md-4"><?=__('Spieler')?></div><div class="cell padded rw-4 rw-md-6"><?=__('Beruf')?></div></div>').appendTo(table);

            } else $('<span><?=__('Es wurden keine Spiele im Ranking gefunden, die deinen Suchkriterien entsprechen.')?></span>').appendTo('#ranking_target');

            $.each(data.ranking, function (p, elem) {
                var entry = $('<div class="row pointer"></div>');
                if (elem.mark) entry.addClass('marked');

                var icon = null;
                if (p == 1)         icon = '<img src="media/icons/superstar.gif" alt="rk-winner">';
                else if (p <= 3)    icon = '<img src="media/icons/silverstar.gif" alt="rk-silver">';
                else if (p <= 10)   icon = '<img src="media/icons/star.gif" alt="rk-topten">';
                else                icon = '<i class="fa fa-star-o"></i>';

                $('<div class="cell padded rw-1 rw-lg-0"></div>').html(icon).appendTo(entry);
                $('<div class="cell padded rw-1"></div>').text(p).appendTo(entry);
                $('<div class="cell padded rw-1"></div>').text(elem.score).appendTo(entry);

                if (is_mp) {
                    $('<div class="cell padded rw-3 rw-lg-4 rw-sm-0"></div>').text(elem.name).appendTo(entry);
                    var pl = $('<div class="cell padded rw-6 rw-sm-10"></div>').appendTo(entry);
                    var has_players = false;
                    if (elem.players)
                        $.each(elem.players, function (k, v) {
                            has_players = true;
                            var player = $('<span class="inline-player">' + v.name + '</span>').appendTo(pl);

                            var qtmp = $('<div class="row"></div>');
                            $('<div class="cell padded rw-4 right"><b><?=__('Beruf');?></b></div>').appendTo(qtmp);
                            $('<div class="cell padded rw-8 center"></div>').html(v.job).appendTo(qtmp);
                            $('<div class="cell padded rw-4 right"><b><?=__('Überlebt');?></b></div>').appendTo(qtmp);
                            $('<div class="cell padded rw-8 center"></div>').html(v.life).appendTo(qtmp);
                            $('<div class="cell padded rw-4 right"><b><?=__('Punkte');?></b></div>').appendTo(qtmp);
                            $('<div class="cell padded rw-8 center"></div>').html(v.score).appendTo(qtmp);

                            player.attr('title', $('<div>').append(qtmp).html()).qtip(game.render.html.qtip.player('top'));
                        });
                    if (!has_players)
                        pl.html('--');
                } else {
                    $('<div class="cell padded rw-2 rw-lg-3 rw-md-0"></div>').html(elem.duration).appendTo(entry);
                    $('<div class="cell padded rw-3 rw-md-4"></div>').html('<span class="inline-player">' + elem.players["0"].name + '</span>').appendTo(entry);
                    $('<div class="cell padded rw-4 rw-md-6"></div>').html(elem.players["0"].job).appendTo(entry);
                }

                entry.click(function() {
                    window.open('ranking/game/' + season + '/' +  elem.id);
                }).appendTo(table);


            });

            var max_offset = Math.floor(data.games / 20) * 20;
            var current_page = Math.floor(offset / 20);

            if (max_offset == 0)
                $('.navigation').hide();
            else $('.navigation').show();
            if (offset > 0) {
                $(navigation.get(0)).removeClass('btn-disabled').click(read_fetch(0));
                $(navigation.get(1)).removeClass('btn-disabled').click(read_fetch(offset - 20));
            }

            $(navigation.get(2)).removeClass('btn-disabled').click(function () {
                var entry = prompt('<?=__('Zu welcher Seite möchtest du springen?')?>', current_page + 1);
                if (entry == null)
                    return;
                var p = parseInt(entry);
                if (!isNaN(p) && p > 0 && p != (current_page + 1) && p <= max_offset / 20 + 1)
                    read_fetch((p - 1) * 20)();
            }).html(game.i18n('<?=__('Seite :c/:m')?>', {':c': current_page + 1, ':m': max_offset / 20 + 1}));

            if (offset < max_offset) {
                $(navigation.get(3)).removeClass('btn-disabled').click(read_fetch(offset + 20));
                $(navigation.get(4)).removeClass('btn-disabled').click(read_fetch(max_offset));
            }
        });
    };

    $('#content').find('select').change(read_fetch(0)).selectric({
        maxHeight: 200
    });
    $('.navigation').hide();
    game_type_elem.trigger('change');

    $('#btn_switch_global').click(function() {game.network.load('ranking/global')});
})();
// ## JS COMPRESS END ## //
</script>