<?php
/**
 * @var string $time String containing the life time
 * @var array $split_time Split time (minutes / hours / days / weeks)
 * @var string|boolean $cause_of_death Cause of death
 * @var int $soul_points Amount of soul points the player has earned
 * @var int $ach_points Amount of achievement points the player has earned
 * @var bool $rankable True, if the game is rankable
 * @var int $braincoins Number of earned braincoins
 * @var int $braincoins_account Number of braincoins in the users account
 * @var array $achievements Achievements
 * @var array $ratings Player Ratings
 */
if (!isset($services)) $services = array();
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><span class="hide-desktop"><?=__('Das wars!')?></span><span class="hide-mobile"><?=__('Herzlichen Glückwunsch, du bist tot!')?></span></h1>

<div class="value-box">
    <div class="row center">
        <div class="cell rw-3 padded">
            <b><?php echo __('Wochen'); ?></b><br />
            <?php echo $split_time[3] ?>
        </div>
        <div class="cell rw-3 padded">
            <b><?php echo __('Tage'); ?></b><br />
            <?php echo $split_time[2] ?>
        </div>
        <div class="cell rw-3 padded">
            <b><?php echo __('Stunden'); ?></b><br />
            <?php echo $split_time[1] ?>
        </div>
        <div class="cell rw-3 padded">
            <b><?php echo __('Minuten'); ?></b><br />
            <?php echo $split_time[0] ?>
        </div>
    </div>

    <div class="row center">
        <div class="cell rw-6 padded">
            <b><?=__('Seelenpunkte');?></b><br />
            <?= $soul_points ? $soul_points : __('Keine');?>
        </div>
        <div class="cell rw-6 padded">
            <b><span class="hide-sm"><?=__('Auszeichnungspunkte');?></span><span class="hide-md hide-lg hide-desktop"><?=__('AP');?></span></b><br />
            <?= $ach_points ? $ach_points : __('Keine');?>
        </div>
    </div>
</div>


<div class="row">
    <div class="cell rw-7 rw-md-12 padded justify">
        <h2><?=__('Nach qualvollen :time hast du nun endlich ins Gras gebissen!', array(':time' => $time)); ?></h2>

        <?php if ($cause_of_death) { ?>
            <?=__('Du bist auf die folgende, unsagbar qualvolle Art und Weise aus dieser Welt gegangen'); ?>: <b><?=__($cause_of_death); ?></b>.
        <?php } else { ?>
            <?=__('Dein Tod kam sehr überraschend... Niemand kann genau sagen, was passiert ist. Trotzdem bist du tot.'); ?>.
        <?php } ?>

        <?php if ($soul_points == 0) { ?>
            <?=__('Leider hast du es trotz harter Anstrengungen nicht geschafft, Punkte zu erspielen.');?><br />
            <b><?=__('Viel Glück in deinem nächsten Leben!');?></b>
        <?php } elseif ($rankable) { ?>
            <?=__('Du hattest ein erfülltes Leben, das dir :points Punkte für deine Seele eingebracht hat.', array(':points' => '<b>' . $soul_points . '</b>')); ?><br />
            <b><?php echo __('Du hast dir einen Platz im Ranking verdient!'); ?></b> <?=__('Klicke auf "Das Spiel beenden", um ins Ranking eingetragen zu werden.'); ?>
        <?php } else { ?>
            <?=__('Dieses Spiel hätte dir :points Punkte für deine Seele eingebracht.', array(':points' => '<b>' . $soul_points . '</b>')); ?><br />
            <b><?=__('Leider wurde dein Spiel aus dem Ranking ausgeschlossen.'); ?></b> <?=__('Du erhälst keine Seelenpunkte und keinen Platz im Ranking für dieses Spiel.'); ?>
        <?php } ?>

        <br /><br />

        <?php if ($achievements) { ?>
            <?php if ($rankable && $soul_points > 0) { ?>
                <?=__('Hiermit werden dir folgende Auszeichnungen verliehen:');?>
            <?php } else { ?>
                <?=__('Leider hast du folgende Auszeichnungen knapp verpasst:');?>
            <?php } ?><br />

            <?php foreach ($achievements as $achievement) { ?>
                <div data-aid="<?=$achievement['id']?>" class="achievement <?=($rankable && $soul_points > 0) ? '' : 'achievement-missed'?> achievement-<?=$achievement['class']?>">
                    <img alt="?" src="media/icons/achievements/<?=$achievement['icon']?>" />
                    <span><?=$achievement['count']?></span>
                </div>
            <?php } ?>
        <?php } else { ?>
            <?=__('Leider hast du es nicht geschafft, Auszeichnungen bei diesem Spiel zu sammeln ...');?>
        <?php } ?>
    </div>

    <div class="cell rw-5 rw-md-0 padded">
        <div class="help noclick">
            <h4><?=__('Der Tod')?></h4>
            <?=__('Wenn deine Gesundheit auf 0 sinkt, stirbst du. Das Spiel endet dann. Aber keine Angst, du kannst sofort ein neues Spiel starten wenn du gestorben bist.');?><br /><br />
            <?=__('Nach deinem Tod wirst du auf eine Seite geleitet, die dir die Auszeichnungen und Punkte anzeigt, die du dir im Verlauf des Spiels verdient hast. Um Punkte oder Auszeichnungen zu erhalten musst du mindestens einen Seelenpunkt erspielt haben.');?><br /><br />
            <?=__('Wenn du deinen Tod durch einen Klick auf "Das Spiel beenden" bestätigst, wird dein Spiel ins Ranking aufgenommen (sofern du mindestens einen Seelenpunkt erspielt hast). Du wirst daraufhin zur Startseite von ZombVival weitergeleitet.');?>
        </div>
    </div>

    <?php if ($braincoins != 0) { ?>
        <div class="cell-small rw-14 ro-5 rw-lg-20 ro-lg-2 rw-md-24 ro-md-0">
            <div class="value-box">
                <div class="row">
                    <div class="cell rw-3 rw-sm-4 padded" style="opacity: <?=$braincoins>0 ? 1 : 0.5?>">

                        <div class="row">
                            <div class="cell rw-2 ro-2"><img src="media/icons/coin.gif" alt="bc" /></div>
                            <div class="cell rw-8 right"><?=(int)$braincoins_account?></div>

                            <div class="cell rw-2">+</div>
                            <div class="cell rw-2"><img src="media/icons/items/braincoin.gif" alt="bc" /></div>
                            <div class="cell rw-8 right"><?=abs($braincoins)?></div>

                            <div class="cell rw-2">=</div>
                            <div class="cell rw-2"><img src="media/icons/coin.gif" alt="bc" /></div>
                            <div class="cell rw-8 right"><b><?=abs($braincoins)+$braincoins_account?></b></div>

                        </div>
                    </div>
                    <div class="cell rw-8 ro-1 ro-sm-0 padded">
                        <?=($braincoins > 0) ? __('Herzlichen Glückwunsch! Du hast einige BrainCoins im Spiel gefunden, die deinem Konto nun angerechnet werden!') : __('Leider kannst du dir die gefundenen BrainCoins nicht anrechnen lassen, da du nicht lange genug überlebt hast...')?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php if ($ratings) { ?>
    <br />
    <div class="row">
        <div class="cell rw-12">
            <h3><?=__('Karma-Bewertung');?></h3>
            <div class="note">
                <b><?=__('Wenn du möchtest, kannst du hier die Spielleistung deiner Mitspieler bewerten.'); ?></b>
                <?=__('Auf diese Art kannst du deinen Mitspielern für ihren Einsatz danken oder sie dazu bewegen, ihre Spielweise zu überdenken. Jede Bewertung erfolgt annonym und kann vom jewailigen Spieler nicht zurückverfolgt werden.'); ?>
            </div>
        </div>
        <?php foreach ($ratings as $uid => $data) { ?>
            <div class="cell rw-6 padded">
                <div class="flatbox">
                    <h4><label for="rating_<?=$uid?>"><?=$data['name'];?></label></h4>
                    <div class="row">
                        <div class="cell rw-12 padded">
                            <select data-rating-for="<?=$uid?>" id="rating_<?=$uid?>">
                                <option value="no" selected="selected"><?=__('nicht bewerten.');?></option>
                                <?php $rate_g = array(-2 => 'hat die Partie ordentlich sabotiert!', -1 => 'hat kaum etwas nützliches beigetrage.', 0 => 'ist nicht besonders aufgefallen.', 1 => 'hat zum Erfolg dieser Partie beigetragen.', 2 => 'hat sich mächtig für uns ins Zeug gelegt!'); ?>
                                <?php foreach ($rate_g as $v => $k) { ?>
                                    <option value="<?=$v?>"><?=__($k);?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        <?php } ?>
    </div>
    <br />
<?php } ?>

<div class="row">
    <div class="cell rw-12">
        <div id="finalizebtn" class="btn btn-icon"><span class="btn-icon-inner"><i class="fa fa-arrow-right"></i></span><span id="finalizebtn-content"><?=__('Das Spiel beenden');?></span></div>
    </div>
</div>

<div class="row" id="logtarget">

</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#persistent').empty();
    <?php foreach ($achievements as $achievement) { ?>
        $('[data-aid=<?=$achievement['id']?>]').attr('title', '<?=$achievement['count']?> x ' + <?=__j($achievement['name'])?>).qtip(game.render.html.qtip.ingame('top'));
    <?php } ?>

    $('#finalizebtn').click(function() {
        var alias = $(this);
        alias.addClass('btn-disabled').find('.fa').attr('class','fa fa-spin fa-circle-o-notch');
        alias.find('#finalizebtn-content').html(<?=__j('Bitte warten...');?>);

        var ratings = {};
        $('[data-rating-for]').each(function() {
            if ($(this).val() != 'no')
                ratings[$(this).data('rating-for')] = $(this).val();
        });

        $('#content').find('.form_input').attr('disabled', 'disabled');

        game.network.query('japi/game/end', {ratings: ratings}, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);

                alias.removeClass('btn-disabled').find('.fa').attr('class','fa fa-arrow-right');
                alias.find('#finalizebtn-content').html(<?=__j('Das Spiel beenden');?>);

                $('#content').find('.form_input').removeAttr('disabled');

            } else
                game.network.load(data.redirect);
        });
    });

    $('select').selectric();

    core.renderLog($('#logtarget'));
// ## JS COMPRESS END ## //
</script>

