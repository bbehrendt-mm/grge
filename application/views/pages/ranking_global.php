<?php
/**
 * @var array $achievements
 * @var int $preset
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Globales Ranking');?></h1>

<div class="row">
    <h2><?=__('Bitte wähle, welches Ranking du sehen möchtest.');?></h2>

    <div class="cell rw-3 rw-lg-4 rw-md-6 rw-sm-12 padded">
        <label for="ranking_type"></label><select class="form_input" id="ranking_type" data-container="body">
            <option value="1"><?=__('Auszeichnungen');?></option>
            <option value="2"><?=__('Seelenpunkte');?></option>
        </select>
        <input type="hidden" value="<?=$preset?>" id="selected_achievement">
    </div>

    <div class="cell rw-9 rw-lg-8 rw-md-6 rw-sm-12 right">
        <div id="btn_switch_seasonal" class="btn small"><?=__('Zum saisonalen Ranking wechseln');?></div>
    </div>
</div>
<div class="row">
    <div id="ranking_container" class="cell padded rw-6 rw-md-12">
        <div class="cell rw-12">
            <div class="center navigation">
                <div class="btn small" title="<?=__('Zum Anfang');?>"><i class="fa fa-angle-double-left"></i></div>
                <div class="btn small" title="<?=__('Eine Seite zurück');?>"><i class="fa fa-angle-left"></i></div>
                <div class="btn small" title="<?=__('Zu bestimmter Seite springen');?>"></div>
                <div class="btn small" title="<?=__('Eine Seite weiter');?>"><i class="fa fa-angle-right"></i></div>
                <div class="btn small" title="<?=__('Zum Ende');?>"><i class="fa fa-angle-double-right"></i></div>
            </div>
        </div>

        <div id="ranking_target" class="center"></div>
    </div>

    <div id="achievement_container" class="center padded cell rw-6 rw-md-12">
        <?php foreach ($achievements as $achievement) if ($achievement['count'] > 0) { ?>
            <div class="hotbox inline-block <?=$achievement['id'] == $preset ? 'active' : ''?>">
                <div data-aid="<?=$achievement['id']?>" class="achievement achievement-<?=$achievement['class']?>">
                    <img alt="?" src="media/icons/achievements/<?=$achievement['icon']?>" />
                    <span><?=$achievement['points']?></span>
                </div>
            </div>
        <?php } ?><br /><br />
        <?php foreach ($achievements as $achievement) if ($achievement['count'] <= 0) { ?>
            <div data-aid="-1" class="achievement achievement-unknown">
                <img alt="?" src="media/icons/achievements/<?=$achievement['icon']?>" />
            </div>
        <?php } ?>
    </div>
</div>



<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
(function() {
    var read_fetch = function (offset) {
        return function () {
            switch ($('#ranking_type').val()) {
                case '1': load_achievement_ranking($('#selected_achievement').val(),offset); break;
                case '2': load_soulpoint_ranking(offset); break;
                default: alert('INVALID MODE');
            }
        }
    };


    var load_achievement_ranking = function(aid, offset) {
        $('#ranking_target').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        var content = $('#content');
        var selectors = content.find('select:not(:disabled)').attr('disabled', 'disabled');
        var navigation = content.find('.navigation > div').addClass('btn-disabled').off('click');

        game.network.query('japi/ranking/achievements', {aid: aid, offset: offset, length: 20}, function (data) {
            selectors.removeAttr('disabled');

            $('#ranking_target').empty();
            if (data.ranking) {
                var table = $('<div class="row-table row-table-borders row-table-striped row-table-interact"></div>').appendTo('#ranking_target');
                $('<div class="row"><div class="cell padded rw-3"><span class="hide-md hide-sm"><?=__('Platz')?></span><span class="hide-desktop hide-lg">#</span></div><div class="cell padded rw-6"><?=__('Spieler')?></div><div class="cell padded rw-3"><?=__('Punkte')?></div></div>').appendTo(table);

            } else $('<span><?=__('Es wurden keine Spiele im Ranking gefunden, die deinen Suchkriterien entsprechen.')?></span>').appendTo('#ranking_target');

            if (data.user) {
                if (data.user.pos < offset) {
                    var entry = $('<div class="row marked"></div>');

                    $('<div class="cell padded rw-2"></div>').html(get_icon(data.user.pos)).appendTo(entry);
                    $('<div class="cell padded rw-1"></div>').text(data.user.pos).appendTo(entry);
                    $('<div class="cell padded rw-6"></div>').appendTo(entry).append($('<span class="inline-player"></span>').text(data.user.name));
                    $('<div class="cell padded rw-3"></div>').text(data.user.value).appendTo(entry);

                    entry.appendTo(table);
                    $('<div class="row"><div class="cell padded rw-12 center" />...</div>').appendTo(table);
                }
            }

            $.each(data.ranking, function (p, elem) {
                var entry = $('<div class="row"></div>');
                var icon = get_icon(p);

                if (elem.mark) entry.addClass('marked');

                $('<div class="cell padded rw-2"></div>').html(icon).appendTo(entry);
                $('<div class="cell padded rw-1"></div>').text(p).appendTo(entry);
                $('<div class="cell padded rw-6"></div>').appendTo(entry).append($('<span class="inline-player"></span>').text(elem.name));
                $('<div class="cell padded rw-3"></div>').text(elem.value).appendTo(entry);

                entry.appendTo(table);
            });

            if (data.user) {
                if (data.user.pos > (offset + 20)) {
                    var entry2 = $('<div class="row marked"></div>');

                    $('<div class="cell padded rw-2"></div>').html(get_icon(data.user.pos)).appendTo(entry2);
                    $('<div class="cell padded rw-1"></div>').text(data.user.pos).appendTo(entry2);
                    $('<div class="cell padded rw-6"></div>').appendTo(entry2).append($('<span class="inline-player"></span>').text(data.user.name));
                    $('<div class="cell padded rw-3"></div>').text(data.user.value).appendTo(entry2);

                    $('<div class="row"><div class="cell padded rw-12 center" />...</div>').appendTo(table);
                    entry2.appendTo(table);
                }
            }

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

    var get_icon = function(p) {
        if (p == 1)         return '<img src="media/icons/superstar.gif" alt="rk-winner">';
        else if (p <= 3)    return '<img src="media/icons/silverstar.gif" alt="rk-silver">';
        else if (p <= 10)   return '<img src="media/icons/star.gif" alt="rk-topten">';
        else                return '<i class="fa fa-star-o"></i>';
    };

    var load_soulpoint_ranking = function(offset) {
        $('#ranking_target').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        var content = $('#content');
        var selectors = content.find('select:not(:disabled)').attr('disabled', 'disabled');
        var navigation = content.find('.navigation > div').addClass('btn-disabled').off('click');

        game.network.query('japi/ranking/soulpoints', {offset: offset, length: 20}, function (data) {
            selectors.removeAttr('disabled');

            $('#ranking_target').empty();
            if (data.ranking) {
                var table = $('<div class="row-table row-table-borders row-table-striped row-table-interact"></div>').appendTo('#ranking_target');
                $('<div class="row"><div class="cell padded rw-3"><span class="hide-md hide-sm"><?=__('Platz')?></span><span class="hide-desktop hide-lg">#</span></div><div class="cell padded rw-6"><?=__('Spieler')?></div><div class="cell padded rw-3"><?=__('SP')?></div></div>').appendTo(table);

            } else $('<span><?=__('Es wurden keine Spiele im Ranking gefunden, die deinen Suchkriterien entsprechen.')?></span>').appendTo('#ranking_target');

            if (data.user) {
                if (data.user.pos < offset) {
                    var entry = $('<div class="row marked"></div>');

                    $('<div class="cell padded rw-2"></div>').html(get_icon(data.user.pos)).appendTo(entry);
                    $('<div class="cell padded rw-1"></div>').text(data.user.pos).appendTo(entry);
                    $('<div class="cell padded rw-6"></div>').appendTo(entry).append($('<span class="inline-player"></span>').text(data.user.name));
                    $('<div class="cell padded rw-3"></div>').text(data.user.points).appendTo(entry);

                    entry.appendTo(table);
                    $('<div class="row"><div class="cell padded rw-12 center" />...</div>').appendTo(table);
                }
            }

            $.each(data.ranking, function (p, elem) {
                var entry = $('<div class="row"></div>');
                var icon = get_icon(p);

                if (elem.mark) entry.addClass('marked');

                $('<div class="cell padded rw-2"></div>').html(icon).appendTo(entry);
                $('<div class="cell padded rw-1"></div>').text(p).appendTo(entry);
                $('<div class="cell padded rw-6"></div>').appendTo(entry).append($('<span class="inline-player"></span>').text(elem.name));
                $('<div class="cell padded rw-3"></div>').text(elem.points).appendTo(entry);

                entry.appendTo(table);
            });

            if (data.user) {
                if (data.user.pos > (offset + 20)) {
                    var entry2 = $('<div class="row marked"></div>');

                    $('<div class="cell padded rw-2"></div>').html(get_icon(data.user.pos)).appendTo(entry2);
                    $('<div class="cell padded rw-1"></div>').text(data.user.pos).appendTo(entry2);
                    $('<div class="cell padded rw-6"></div>').appendTo(entry2).append($('<span class="inline-player"></span>').text(data.user.name));
                    $('<div class="cell padded rw-3"></div>').text(data.user.points).appendTo(entry2);

                    $('<div class="row"><div class="cell padded rw-12 center" />...</div>').appendTo(table);
                    entry2.appendTo(table);
                }
            }

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

    $('[data-aid=-1]').attr('title', '-').qtip(game.render.html.qtip.ingame('top', {
        render: function(event,api) {
            $(this).find('.qtip-content')
                .empty()
                .append($('<b />').addClass('header').text('???'))
                .append($('<p />').text(<?=__j('Bisher hat es noch niemand geschafft, diese Auszeichnung zu erhalten...')?>))
        }
    }));
    <?php foreach ($achievements as $achievement) { ?>
        $('[data-aid=<?=$achievement['id']?>]').parent('.hotbox').attr('title', '-').qtip(game.render.html.qtip.ingame('top', {
            render: function(event,api) {
                $(this).find('.qtip-content')
                    .empty()
                    .append($('<b />').addClass('header').text(<?=__j($achievement['name'])?>))
                    .append($('<p />').text(<?=__j('Diese Auszeichnung wurde bereits :num mal verliehen!', [':num' => $achievement['count']])?>))
            }
        }));
    <?php } ?>
    $('[data-aid]:not(.achievement-unknown)').each(function() {
        var aid = $(this).data('aid');
        $(this).parent('.hotbox').click(function() {

            if ($(this).hasClass('active')) {
                $('#selected_achievement').val('0');
                $(this).removeClass('active');
            } else {
                $(this).addClass('active').siblings('.hotbox').removeClass('active');
                $('#selected_achievement').val(aid);
            }

            (read_fetch(0))();
        });
    });

    $('#content').find('select').trigger('change').selectric({
        maxHeight: 200
    });

    $('#btn_switch_seasonal').click(function() {game.network.load('ranking/lists')});
    $('#ranking_type').change(function() {
        $('#selected_achievement').val('0');
        $('[data-aid]:not(.achievement-unknown)').parent('.hotbox').removeClass('active');
        if ($(this).val() != '1') {
            $('#achievement_container').hide();
            $('#ranking_container').addClass('ro-3 rw-lg-9 ro-lg-1 ro-md-0');
        } else {
            $('#achievement_container').show();
            $('#ranking_container').removeClass('ro-3 rw-lg-9 ro-lg-1 ro-md-0');
        }
        (read_fetch(0)());
    });

    (read_fetch(0)())
})();
// ## JS COMPRESS END ## //
</script>