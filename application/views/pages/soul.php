<?php
/**
 * @var number $season Current season
 * @var bool $own_soul Is own soul
 * @var string $soul_owner Soul owner name
 * @var string $avatar owner avatar
 * @var int $soul_id
 * @var int $points_soul
 * @var int $points_ach
 * @var int $points_karma
 * @var string $rank_soul
 * @var string $rank_karma
 * @var array $achievements
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=$own_soul ? __('Meine Seele') : __('Seele von :name', [':name' => $soul_owner]);?></h1>

<div class="value-box">
    <div class="row center">
        <div class="cell rw-3 padded">
            <div class="framed cream inline-block"><img id="avatar" src="<?=$avatar ? $avatar : 'media/img/mugshot.png'?>" alt="<?=$soul_owner?>" /></div>

        </div>
        <div class="cell rw-9 padded">
            <div class="row center">
                <div class="cell rw-6 padded">
                    <b><?php echo __('Seelenpunkte'); ?></b><br />
                    <?=$points_soul?>
                </div>
                <div class="cell rw-6 padded">
                    <b><?php echo __('Auszeichnungspunkte'); ?></b><br />
                    <?=$points_ach?>
                </div>
                <div class="cell rw-6 padded">
                    <b><?php echo __('Rang'); ?></b><br />
                    <?=__($rank_soul);?>
                </div>
                <div class="cell rw-6 padded">
                    <b><?php echo __('Karma'); ?></b><br />
                    <?=__($rank_karma);?>
                </div>
            </div>
        </div>
    </div>

    <div class="row center">
        <?php foreach ($achievements as $achievement) { ?>
            <div data-aid="<?=$achievement['id']?>" class="achievement achievement-<?=$achievement['class']?>">
                <img alt="?" src="media/icons/achievements/<?=$achievement['icon']?>" />
                <span><?=$achievement['count']?></span>
            </div>
        <?php } ?>
    </div>

</div>

<div class="row right">
    <div id="soul-search" class="btn small">
        <i class="fa fa-search"></i>
        <?=__('Eine andere Seele suchen...');?>
    </div>
</div>

<div class="row">
    <div class="cell rw-12 center">
        <h2><?=$own_soul ? __('Deine Ranking-Highlights') : __(':name\'s Ranking-Highlights', [':name' => $soul_owner])?></h2>
    </div>
    <div class="cell rw-2 ro-5">
        <label for="game_season"></label><select class="form_input" id="game_season" data-container="body">
            <?php for ($i = $season; $i >= 0; $i--) { ?>
                <option value="<?=$i;?>"><?=__('Season :num', [':num' => $i]);?></option>
            <?php } ?>

        </select>
    </div>
</div>


<div id="ranking_target" class="center"></div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    var read_fetch = function(offset) {
        return function() {
            var is_mp = $('#game_type').val() == "2";
            fetch(is_mp, $('#game_mode').val(), is_mp ? "1" : $('#game_time').val(), $('#game_season').val(), Math.max(0,offset))
        }
    };

    var fetch = function(season) {
        $('#ranking_target').html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        var content = $('#content');
        var selectors = content.find('select:not(:disabled)').attr('disabled', 'disabled');

        game.network.query('japi/ranking/soul', {
            season: season,
            uid: <?=$soul_id?>
        }, function(data) {
            selectors.removeAttr('disabled');

            $('#ranking_target').empty();
            if (data.ranking) {
                var table = $('<div class="row-table padded row-table-borders row-table-striped row-table-interact"></div>').appendTo('#ranking_target');
                $('<div class="row"><div class="cell padded rw-2"><?=__('Platz')?></div><div class="cell padded rw-1"><?=__('Punkte')?></div><div class="cell padded rw-2"><?=__('Spieldauer')?></div><div class="cell padded rw-3"><?=__('Spielmodus')?></div><div class="cell padded rw-4"><?=__('Beruf')?></div></div>').appendTo(table);

                $.each(data.ranking, function(p, elem) {
                    var entry = $('<div class="row"></div>');

                    var fst = $('<div class="cell padded rw-1"></div>').appendTo(entry);
                    if (elem.pos == 1) fst.html('<img src="media/icons/superstar.gif" alt="rk-winner">');
                    else if (elem.pos <= 3) fst.html('<img src="media/icons/silverstar.gif" alt="rk-silver">');
                    else if (elem.pos <= 10) fst.html('<img src="media/icons/star.gif" alt="rk-topten">');
                    else fst.html('<i class="fa fa-star-o"></i>');

                    $('<div class="cell padded rw-1"></div>').html(elem.pos).appendTo(entry);
                    $('<div class="cell padded rw-1"></div>').html(elem.score).appendTo(entry);
                    $('<div class="cell padded rw-2"></div>').html(elem.duration).appendTo(entry);
                    $('<div class="cell padded rw-3"></div>').html(elem.mode).appendTo(entry);
                    $('<div class="cell padded rw-4"></div>').html(elem.players["0"].job).appendTo(entry);

                    entry.appendTo(table);
                });

            } else $('<span><?=$own_soul ? __('Du hast es in dieser Season nicht ins Ranking geschafft.') : __(':name hat es in dieser Season nicht ins Ranking geschafft.',[':name' => $soul_owner])?></span>').appendTo('#ranking_target');

        });
    };

    $('#content').find('#game_season').change(function() {
        fetch($(this).val());
    }).selectric({
        maxHeight: 200
    }).change();

    $('#avatar').error(function() {
        $(this).attr('src', 'media/img/mugshot.png').off('error');
    });

    <?php foreach ($achievements as $achievement) { ?>
        $('[data-aid=<?=$achievement['id']?>]').attr('title', '<?=__($achievement['count'])?> x ').qtip(game.render.html.qtip.ingame('top', {
            render: function(event,api) {
                var content = $(this).find('.qtip-content').empty();

                content
                    .append($('<b>').addClass('header').text(<?=__j($achievement['name'])?>))
                    .append($('<span />').html(game.i18n(<?=__j('Diese Auszeichnung ist ::i:: :num  Punkte::/i:: wert.')?>, {':num' : <?=__($achievement['points'])?>})));
            }
        }));
    <?php } ?>

    $('#soul-search').click(function() {
        var pp = core.popup.spawn(340, 400);

        var input, results;

        pp.append(
            $('<div />').addClass('row iconize').append(
                $('<div />').addClass('cell rw-1').append($('<i />').addClass('fa fa-search'))
            ).append(
                $('<div />').addClass('cell rw-11').append(
                    input = $('<input />').attr('type', 'text').addClass('form_input')
                )
            )
        ).append(
            $('<div />').addClass('row').append($('<div />').addClass('cell rw-12 padded').append($('<span />').addClass('small').text(<?=__j('Gib den Namen eines Spielers ein, dessen Seele du suchen möchtest.')?>)))
        ).append($('<div />').css({position: 'absolute', top: 80, left: 0, bottom: 0, right: 0, overflow: 'auto'}).append(results = $('<ul />').addClass('soul-listing')));

        input.on('keyup',function() {
            var search = $(this).val();
            if (search.length < 3) return;

            game.network.query('japi/ranking/search', {
                query: search
            }, function(data) {
                results.empty();
                if (data.users) {
                    $.each(data.users, function(k,v) {
                        results.append($('<li />').text(v.name).click(function() {
                            game.network.load('ranking/soul/' + v.id);
                        }))
                    })
                }
            });
        })


    });
// ## JS COMPRESS END ## //
</script>