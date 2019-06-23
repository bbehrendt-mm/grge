<?php
/**
 * @var array $sp_modes Single player modes
 * @var array $mp_modes Multiplayer modes
 * @var array $titles
 * @var number $season Current season
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Season-Ranking');?></h1>

<h2><?=__('Bitte wähle, welches Ranking du sehen möchtest.');?></h2>

<div class="row">
    <div class="cell rw-4 padded">
        <label for="game_type"></label><select class="form_input" id="game_type" data-container="body">
            <option value="1"><?=__('Einzelspieler');?></option>
            <option value="2"><?=__('Mehrspieler');?></option>
        </select>
    </div>

    <div class="cell rw-4 padded">
        <label for="game_mode"></label><select class="form_input" id="game_mode" data-container="body"></select>
    </div>
    <div class="cell rw-4 padded">
        <label for="game_time"></label><select class="form_input" id="game_time" data-container="body"></select>
    </div>

</div>

<div class="row">
    <div class="cell  ro-3 rw-6 ro-md-2 rw-md-8 ro-sm-0 rw-sm-12 padded">
        <label for="game_season"></label><select class="form_input" id="game_season" data-container="body">
            <option value="<?=$season;?>"><?=__('Season :num', [':num' => $season]);?></option>
        </select>
    </div>
</div>
<div>
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
        var titles = <?=json_encode($titles, JSON_FORCE_OBJECT);?>;
        for (var i = ($(this).val() == "2" ? 4 : 0); i <= <?=$season?>; i++)
            $('<option value="' + i + '">' + game.i18n('<?=__('Season :num');?>', {':num': i}) + ' - ' + titles[i] + '</option>').prependTo(gs);
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

                var cur_row = NF.row().appendTo(table);
                cur_row
                    .append($('<div class="cell padded rw-2 rw-lg-1" />').append($('<span class="hide-md hide-sm" />').text(<?=__j('Platz')?>)).append($('<span class="hide-desktop hide-lg" />').text('#')))
                    .append($('<div class="cell padded rw-1">').text(<?=__j('Punkte')?>));

                if (is_mp)
                    cur_row
                        .append($('<div class="cell padded rw-3 rw-lg-4 rw-sm-0">').text(<?=__j('Name')?>))
                        .append($('<div class="cell padded rw-6 rw-sm-10">').text(<?=__j('Spieler')?>));
                else cur_row
                    .append($('<div class="cell padded rw-2 rw-lg-3 rw-md-0">').text(<?=__j('Spieldauer')?>))
                    .append($('<div class="cell padded rw-3 rw-md-4">').text(<?=__j('Spieler')?>))
                    .append($('<div class="cell padded rw-4 rw-md-6">').text(<?=__j('Beruf')?>));

            } else $('<span />').text(<?=__j('Es wurden keine Spiele im Ranking gefunden, die deinen Suchkriterien entsprechen.')?>).appendTo('#ranking_target');

            $.each(data.ranking, function (p, elem) {
                p = elem.pos;
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
                            if (v.premium) player.addClass('black');

                            var qtmp = NF.row();
                            NF.cell(true, 4, 0, 'right').append($('<b />').text(<?=__j('Beruf');?>)).appendTo(qtmp);
                            NF.cell(true, 8, 0, 'center').append($('<span />').addClass(v.premium ? 'inline-premium black' : '').text(v.job)).appendTo(qtmp);
                            NF.cell(true, 4, 0, 'right').append($('<b />').text(<?=__j('Überlebt');?>)).appendTo(qtmp);
                            NF.cell(true, 8, 0, 'center').text(v.life).appendTo(qtmp);
                            NF.cell(true, 4, 0, 'right').append($('<b />').text(<?=__j('Punkte');?>)).appendTo(qtmp);
                            NF.cell(true, 8, 0, 'center').text(v.score).appendTo(qtmp);

                            player.attr('title', $('<div>').append(qtmp).html()).qtip(game.render.html.qtip.player('top'));
                        });
                    if (!has_players)
                        pl.html('--');
                } else {
                    $('<div class="cell padded rw-2 rw-lg-3 rw-md-0"></div>').html(elem.duration).appendTo(entry);
                    $('<div class="cell padded rw-3 rw-md-4"></div>').html('<span class="inline-player ' + (elem.players["0"].premium ? 'black' : '') + '">' + elem.players["0"].name + '</span>').appendTo(entry);

                    $('<div class="cell padded rw-4 rw-md-6"></div>').html('<span class="' + (elem.players["0"].premium ? 'inline-premium black' : '') + '">' + elem.players["0"].job + '</span>').appendTo(entry);
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
                var entry = prompt('<?=__j('Zu welcher Seite möchtest du springen?')?>', current_page + 1);
                if (entry == null)
                    return;
                var p = parseInt(entry);
                if (!isNaN(p) && p > 0 && p != (current_page + 1) && p <= max_offset / 20 + 1)
                    read_fetch((p - 1) * 20)();
            }).html(game.i18n(<?=__j('Seite :c/:m')?>, {':c': current_page + 1, ':m': max_offset / 20 + 1}));

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