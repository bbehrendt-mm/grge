<?php
    /**
     * @var array $games An array containing lobby data: [gameid (i), lang (s), slots (i), name (s), password (b), locked (b), mode (i), players => [name (s), id (i), job (i), cod (s|NULL)]]
     * @var array $database An array containing the game mode and profession database: [modes => [jobs (a), locked (b), meta => [name (s), caption (s), body (s), headline (s)], requirements => [ext (a), ext_note (a), job (a), mode (a)], type (s)], jobs => [level (i), levels (a), meta => [name (s), caption (s)], locked (b), next_level (i|NULL), points (i), requirements => [ext (a), ext_note (a), job (a), mode (a)]]]
     * @var bool $lock True, if the player is blocked from accessing public games
     * @var int $lock_count Number of active complaints
     * @var int[] $lock_timerange Array, the first element contains the timestamp where the next complaint will be lifted, the second element contains the timestamp where the last complaint will be lifted
     * @var string[] $languages List of language flags
     */

    //ToDo: Unstartable Jobs
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Spielauswahl</h1>

<form id="data-container" class="hidden"></form>
<div id="cv_target" class="row">
    <div class="row">
        <ul class="breadcrumbs cell rw-12" id="revert-container">
            <li><?=__('Lobby');?></li>
        </ul>
    </div>

    <div class="row" id="cvtarget">

        <div class="cell rw-12 padded">

            <!-- Game Select  -->
            <div class="row" data-conditional="1" data-provide='["mode","id","password","protect","name","slots"]' data-rely='[]'>
                <h2><?=__('Tritt einer Mehrspieler-Partie bei...');?></h2>

                <div class="cell rw-12 padded">
                    <div class="help noclick">
                        <h4><?=__('Mehrspieler-Lobby');?></h4>
                        <?=__('Hier findest du offene Mehrspieler-Partien, denen du beitreten kannst. Ist eine Partie voll, so wird sie automatisch aus dieser Liste entfernt.');?><br />
                        <?=__('ZombVival kann im Mehrspieler-Modus nur Spaß machen, wenn sich alle Spieler fair verhalten. Bitte behandle deine Mitspieler so, wie du selbst behandelt werden möchtest. Fehlverhalten anderer Spieler kannst du jederzeit an Brainbox melden.');?>
                    </div>
                </div>

                <div class="row">
                    <?php if ($lock_count > 0) { ?>
                        <div class="cell rw-6 padded">
                            <div class="help noclick">
                                <h4><?=__('Beschwerden');?></h4>
                                <?=__('Beschwerden sind eine automatisierte Maßnahme, um gegen Griefer im Spiel vorzugehen. Wenn du in einer Mehrspieler-Partie frühzeitig stirbst erhälst du eine Beschwerde, die nach einer gewissen Zeit wieder verschwindet. Hast du mehr als :max aktive Beschwerden angehäuft, kannst du öffentlichen Partien nicht mehr beitreten. Du kannst allerdings weiterhin eigene Spiele starten und passwortgeschützten Partien beitreten.', [':max' => $lock_max]);?><br /><br />
                                <?php if ($lock) { ?>
                                    <b><?=__('Da du zuviele Beschwerden angehäuft hast, kannst du bis :time nicht mehr an öffentlichen Pastien teilnehmen!', [':time' => date(__('G:i \U\h\r \a\m d.m.'), $lock_timerange[0])]);?></b>
                                <?php } elseif ($lock_count == 1) { ?>
                                    <?=__('Du hast momentan ::b::eine::/b:: aktive Beschwerde!');?>
                                <?php } else { ?>
                                    <?=__('Du hast momentan ::b:::num::/b:: aktive Beschwerden!', [':num' => $lock_count]);?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php foreach($games as $game) { ?>
                        <div class="cell rw-6 padded">
                            <div data-modeset="<?=$game['mode']?>" data-set='{"mode":<?=$game['mode']?>,"id":<?=$game['gameid']?>,"protect":"","name":"","slots":"0","password":<?=$game['password'] ? 'false' : '""'?>,"flow":-1}' data-caption="<?=__($database['modes'][$game['mode']]['meta']['name'])?> (<?=$game['name']?>)" class="<?=$game['locked'] ? 'hotbox disabled' : 'hotbox' ?>">
                                <b class="head"><?=$game['name']?></b>
                                <i class="subtitle">
                                    <img src="media/icons/<?=$game['locked'] ? 'lock.gif' : "lang/{$game['lang']}.png" ?>" alt="<?=$game['locked'] ? 'locked' : $game['lang'] ?>" />
                                    <?=__($database['modes'][$game['mode']]['meta']['name'])?>,
                                    <?=$game['slots'] == 1 ? __('noch 1 Platz frei') : __('noch :num Plätze frei', [':num' => $game['slots']]) ?>
                                </i>
                                <?php foreach($game['players'] as $player) { ?>
                                    <span data-id="player-<?=$game['gameid']?>-<?=$player['id']?>" class="inline-player"><?=$player['name']?></span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <h2><?=__('...  oder eröffne eine eigene Einzel- oder Mehrspieler-Partie!');?></h2>
                <div class="row">
                    <div class="cell rw-6">
                        <div class="row">
                            <?php foreach ($database['modes'] as $mid => $data) if ($data['type'] == 'single') { ?>
                                <div class="cell rw-6 padded">
                                    <div data-modeset="<?=$mid?>" data-set='{"mode":<?=$mid?>,"id":-1,"protect":"","password":"","name":"","slots":"1"}' data-caption="<?=__($data['meta']['name'])?>" class="<?=$data['locked'] ? 'hotbox disabled' : 'hotbox' ?>">
                                        <b class="head"><?=__($data['meta']['name'])?></b>
                                        <i class="subtitle"><?=__('Einzelspieler-Modus');?></i>
                                        <?=__($data['meta']['caption'])?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="cell rw-6">
                        <div class="row">
                            <?php foreach ($database['modes'] as $mid => $data) if ($data['type'] == 'multi_custom') { ?>
                                <div class="cell rw-6 padded">
                                    <div data-modeset="<?=$mid?>" data-set='{"mode":<?=$mid?>,"id":-1,"password":"","flow":-1}' data-caption="<?=__($data['meta']['name'])?>" class="<?=$data['locked'] ? 'hotbox disabled' : 'hotbox' ?>">
                                        <b class="head"><?=__($data['meta']['name'])?></b>
                                        <i class="subtitle"><?=__(':min bis :max Spieler', array(':min' => $data['slots'][0], ':max' => $data['slots'][1]))?></i>
                                        <?=__($data['meta']['caption'])?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enter Password -->
            <div class="row" data-conditional="1" data-provide='["password"]' data-rely='["mode","id","name"]'>
                <h2><?=__('Dieses Spiel erfordert ein Passwort.');?></h2>
                <div class="cell rw-6 ro-1 padded">
                    <input type="text" class="form_input" id="password_in" maxlength="10" placeholder="<?=__('Passwort eingeben');?>" />
                </div>
                <div class="cell rw-4 padded">
                    <div class="btn" id="password_ok"><?=__('Weiter');?></div>
                </div>
            </div>

            <!-- Startup Settings -->
            <div class="row" data-conditional="1" data-provide='["protect","name","slots","lang"]' data-rely='["mode","id"]'>
                <h2><?=__('Spieleinstellungen');?></h2>
                <div class="cell rw-2 ro-1  padded">
                    <label for="lang_in"></label>
                    <select class="form_input" id="lang_in">
                        <?php foreach ($languages as $lang_id => $language) { ?>
                            <option value="<?=$lang_id?>">
                                <?=$language?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="cell rw-6 padded">
                    <input type="text" class="form_input" id="name_in" maxlength="96" placeholder="<?=__('Spielnamen eingeben');?>" />
                </div>
                <div class="cell rw-2 padded">
                    <label for="slots_in"></label><select class="form_input" id="slots_in"></select>
                </div>
                <div class="cell rw-6 ro-1 padded">
                    <input type="text" class="form_input" id="protect_in" maxlength="10" placeholder="<?=__('Passwort eingeben');?>" />
                    <label><input type="checkbox" id="protect_no" /><?=__('Kein Passwort verwenden');?></label>
                </div>
                <div class="cell rw-4 padded">
                    <div class="btn" id="protect_rnd"><?=__('Passwort generieren');?></div>
                    <div class="btn" id="protect_ok"><?=__('Weiter');?></div>
                </div>
                <div class="cell rw-10 ro-1 padded">
                    <div class="help noclick">
                        <h4><?=__('Über den Passwortschutz');?></h4>
                        <?=__('Wenn du dein Spiel mit einem Passwort schützt, können nur Spieler beitreten, die dieses Passwort kennen. Denk daran, dir das Passwort zu notieren, denn nach der Erzeugung des Spiels kannst du es nicht mehr einsehen! Entscheidest du dich gegen die Verwendung eines Passworts, wird dein Spiel öffentlich zugänglich. Spieler, die aufgrund von Beschwerden nicht mehr an öffentlichen Spielen teilnehmen können, dürfen deinem Spiel dann nicht mehr beitreten.');?>
                    </div>
                </div>
            </div>

            <!-- Select Profession -->
            <div class="row" data-conditional="1" data-provide='["job"]' data-rely='["mode","id","password","protect","slots","name"]'>
                <h2><?=__('Wähle deinen Beruf!');?></h2>
                <?php foreach ($database['jobs'] as $jid => $data) { ?>
                    <div class="cell rw-3 padded">
                        <div data-jobset="<?=$jid?>" data-set='{"job":<?=$jid?>}' data-caption="<?=__($data['meta']['name'])?>" class="<?=$data['locked'] ? 'hotbox disabled' : 'hotbox' ?>">
                            <b class="head"><?=__($data['meta']['name'])?></b>
                            <i class="subtitle"><?=__(':num Seelenpunkte', [':num' => $data['points']])?></i>
                            <div class="center">
                                <?php if ($data['locked']) { ?>
                                    <img src="media/icons/lock.gif" alt="x" />
                                <?php } else if (count($data['levels']) == 0) { ?>
                                    <img src="media/icons/silverstar.gif" alt="+" />
                                <?php } elseif (count($data['levels']) + 1 == $data['level']) { ?>
                                    <img src="media/icons/superstar.gif" alt="++" />
                                <?php } else for ($i = 0; $i < $data['level']; $i++) { ?>
                                    <img src="media/icons/star.gif" alt="*" />
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Select Timeflow -->
            <div class="row" data-conditional="1" data-provide='["flow"]' data-rely='["mode","id","password","protect","job","slots","name"]'>
                <h2><?=__('Wähle eine Spielgeschwindigkeit!');?></h2>
                <!-- Variable -->
                <div class="cell rw-3 padded"><div data-set='{"flow":-1}' data-caption="<?=__('Variabler Zeitfluss')?>" class="hotbox">
                    <b class="head"><?=__('Variabler Zeitfluss')?></b>
                    <i class="subtitle"><?=__('Jaja, ihr habt mich überredet.')?></i>
                </div></div>

                <!-- Classic -->
                <div class="cell rw-3 padded"><div data-set='{"flow":900}' data-caption="15 <?=__('Minuten')?>" class="hotbox">
                    <b class="head"><?=__('Super-Zeitlupe')?></b>
                    <b class="head">15 <?=__('Minuten')?></b>
                    <i class="subtitle"><?=__('Slow and steady wins the race!')?></i>
                </div></div>
                <div class="cell rw-3 padded"><div data-set='{"flow":600}' data-caption="10 <?=__('Minuten')?>" class="hotbox">
                    <b class="head"><?=__('Zeitlupe')?></b>
                    <b class="head">10 <?=__('Minuten')?></b>
                    <i class="subtitle"><?=__('Hol dir erstmal \'nen Kaffee.')?></i>
                </div></div>
                <div class="cell rw-3 padded"><div data-set='{"flow":300}' data-caption="5 <?=__('Minuten')?>" class="hotbox">
                    <b class="head"><?=__('Echtzeit')?></b>
                    <b class="head">5 <?=__('Minuten')?></b>
                    <i class="subtitle"><?=__('Zeitmanipulation gibt\'s nicht!')?></i>
                </div></div>
                <div class="cell rw-3 padded"><div data-set='{"flow":120}' data-caption="2 <?=__('Minuten')?>" class="hotbox">
                    <b class="head"><?=__('Zeitraffer')?></b>
                    <b class="head">2 <?=__('Minuten')?></b>
                    <i class="subtitle"><?=__('Entspannt und doch fordernd.')?></i>
                </div></div>
                <div class="cell rw-3 padded"><div data-set='{"flow":60}' data-caption="1 <?=__('Minute')?>" class="hotbox">
                    <b class="head"><?=__('Super-Zeitraffer')?></b>
                    <b class="head">1 <?=__('Minute')?></b>
                    <i class="subtitle"><?=__('Für die Eiligen unter uns.')?></i>
                </div></div>
                <div class="cell rw-3 padded"><div data-set='{"flow":30}' data-caption="30 <?=__('Sekunden')?>" class="hotbox">
                    <b class="head"><?=__('High Speed')?></b>
                    <b class="head">30 <?=__('Sekunden')?></b>
                    <i class="subtitle"><?=__('Darf etwas weniger Reallife sein?')?></i>
                </div></div>
                <div class="cell rw-3 padded"><div data-set='{"flow":15}' data-caption="15 <?=__('Sekunden')?>" class="hotbox">
                    <b class="head"><?=__('Ultra High Speed')?></b>
                    <b class="head">15 <?=__('Sekunden')?></b>
                    <i class="subtitle"><?=__('Ohmeingottwokam dasdennjetzther???')?></i>
                </div></div>

                <div class="cell rw-10 ro-1 padded">
                    <div class="help noclick">
                        <h4><?=__('Der Fluss der Zeit');?></h4>
                        <?=__('Der Lauf der Zeit ist in ZombVival in sog. "Ticks" organisiert. Oben rechts auf der Seite wird ein Countdown bis zum nächsten Tick eingeblendet. Bei jedem Tick wird dein Status neu berechnet, außerdem gibt es eine Chance das Dinge geschehen. Wenn du beispielsweise in der Umgebung deines Verstecks stehst, hast du bei jedem Tick die Chance eine Ruine aufzudecken. Bist du in einer Ruine, hast du die Chance einen Gegenstand zu finden und/oder von Zombies attackiert zu werden.');?><br /><br />
                        <?=__('Ein "Tick" entspricht 5 Minuten Spielzeit. Hast du bei den Spieleinstellungen einen klassischen, festen Zeitfluss gewählt, so musst du direkt beim Start eines Spiels festlegen, wie lange ein Tick in echter Zeit dauern soll. Stellst du dort beispielsweise 1 Minute ein, so läuft das Spiel in 5-facher Geschwindigkeit. Hast du den variablen Zeitfluss gewählt, so kannst du die Geschwindigkeit während des Spiels ändern. Beim klassischen Zeitfluss hingegen hast du jederzeit die Möglichkeit, das Spiel vollständig zu pausieren. Ist das Spiel pausiert, kannst du keinerlei Aktionen durchführen. Dafür werden auch die Ticks angehalten.');?>
                    </div>
                </div>
            </div>

            <div class="row" data-conditional="1" data-provide='["estore"]' data-rely='["mode","id","password","protect","job","flow","name","slots"]'>
                <h2><?=__('Kanns losgehen?');?></h2>
                <?php
                $question = Tool_Gambling::select([
                    'Die Zombies warten schon sehnsüchtig auf dich.', 'Es ist Fütterungszeit...', 'Du hängst doch nicht wirklich an deinem Leben, oder?',
                    'Mario, die Prinzessin wurde entführt! .... Oh halt, falsches Spiel.', 'Ist das eine Machete in deiner Hose, oder freust du dich nur auf die Zombies?',
                    'Der Weltuntergang wartet.'
                ]);
                $tease = Tool_Gambling::select([
                    'Oder willst du lieber weiter mit deinen Barbiepuppen spielen?', 'Oder hast du doch zu viel Angst?', 'Oder bist du wirklich so eine Lusche wie alle sagen?',
                    'Du willst die Zombies doch nicht enttäuschen, oder?', 'Oder traust du dich nur in Spiele, bei denen man sich Vorteile erkaufen kann?',
                    'Oder willst du deine Zeit lieber mit etwas sinnvollem verbringen?'
                ]);
                $retreat = Tool_Gambling::select([
                    'Mir fällt gerade ein, ich hab den Ofen angelassen...','Nein, danke','Öhm... ich spiele doch lieber weiter WoW','Jetzt, wo ich so drüber nachdenke...'
                ]);
                ?>
                <i><?=__($question);?></i> <?=__($tease);?><br /><br />
                <b><?=__('Letzt liegt es an dir: Bist du bereit, in die furchterregende Welt von ZombVival einzutauchen?');?></b>

                <div class="cell rw-8 padded">
                    <div class="btn btn-icon" id="btn_cancel">
                        <span class="btn-icon-inner"><i class="fa fa-times"></i></span>
                        <?=__($retreat)?>
                    </div>
                </div>
                <div class="cell rw-4 padded">
                    <div class="btn btn-icon" id="btn_confirm">
                        <span class="btn-icon-inner"><i class="fa fa-arrow-right"></i></span>
                        <?=__('Auf geht\'s!')?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //

    $('#persistent').empty();
    $('[data-conditional=1]').hide();
    <?php
        foreach ($database['modes'] as &$db_entry)
            unset($db_entry['locked'],$db_entry['type'],$db_entry['meta'],$db_entry['requirements']);
        foreach ($database['jobs'] as &$jb_entry)
            $jb_entry = ['meta' => ['name' => __($jb_entry['meta']['name'])]];
        foreach ($games as &$game_entry)
            unset($game_entry['locked'],$game_entry['lang'],$game_entry['mode'],$game_entry['password'],$game_entry['slots'],$game_entry['name']);
     ?>

    var games = <?=json_encode($games)?>;
    var database = <?=json_encode($database)?>;


    var rebuild = function() {
        var has = $('#data-container').find('> input').map(function() {
            return $(this).attr('name');
        }).get();

        $('[data-conditional=1]').each(function() {
            var meets_condition = true;
            var is_fulfilled = false;

            $.each($(this).data('rely'), function(k,v) {
                if ($.inArray(v,has) == -1)
                    meets_condition = false;
            });

            $.each($(this).data('provide'), function(k,v) {
                if ($.inArray(v,has) > -1)
                    is_fulfilled = true;
            });

            if (!meets_condition || is_fulfilled)
                $(this).hide();
            else $(this).show();
        });
    };

    var get_set = function(value) {
        var node = $('#data-container').find('> input[name=' + value + ']');
        if (node.size() >= 0)
            return node.val();
        else return null;
    };

    var write_set = function(obj, caption) {
        var container = $('#data-container');
        $.each(obj, function(k,v) {
            if (v === false || v === undefined || v === null)
                return;
            if (container.find('> input[name=' + k + ']').val(v).size() == 0)
                container.append('<input type="hidden" name="' + k + '" value="' + v + '" />')

        });

        $('#revert-container').append(
            $('<li />').html(caption).click(function() {
                $(this).next().click();

                $.each(obj, function (k) {
                    container.find('> input[name=' + k + ']').remove();
                });
                $(this).remove();
                rebuild();
            })
        );
        rebuild();
    };

    $('#protect_rnd').click(function() {
        var pw = '';
        for (var i = 0; i < 10; i++) {
            var block = "0123456789abcdefghijkmnpqrstuvwxyz";
            pw += block[Math.floor(Math.random()*block.length)];
        }
        $('#protect_in').val(pw).trigger('change');
    }).click();

    $('#protect_no').click(function() {
        if ($(this).is(':checked')) {
            $('#protect_in').add('#protect_rnd').attr('disabled', 'disabled');
            $('#protect_ok').removeAttr('disabled');
        } else
            $('#protect_in').add('#protect_rnd').removeAttr('disabled').trigger('change');
    }).customRadioCheck();

    $('#protect_in').on('blur keydown keyup change', function() {
        if ($(this).val().length == 0)
            $('#protect_ok').attr('disabled', 'disabled');
        else $('#protect_ok').removeAttr('disabled');
    });

    $('#protect_ok').click(function() {
        var name;
        if (!(name = $('#name_in').val())) {
            game.render.html.notify('error', <?=__j('Bitte gib deiner Partie einen Namen.');?>);
            return;
        }
        if (name.length < 4) {
            game.render.html.notify('error', <?=__j('Dein Spielname muss mindestens 4 Zeichen lang sein.');?>);
            return;
        }
        if ($('#protect_no').is(':checked'))
            write_set({'protect': '','name': name.slice(0,96),'slots':$('#slots_in').val(),'lang':$('#lang_in').val()}, name + ' (' + <?=__j('Kein Passwort');?> + ')');
        else write_set({'name': name.slice(0,96),'slots':$('#slots_in').val(),'lang':$('#lang_in').val(),'protect': $('#protect_in').val().slice(0,10)}, name + ' (' + game.i18n(<?=__j('Passwort ":pw"');?>, {':pw': $('#protect_in').val().slice(0,10)}) + ')');
    });

    $('#password_ok').click(function() {
        var edit = $('#password_in');
        var pw;
        if (!(pw = edit.val())) {
            game.render.html.notify('error', <?=__j('Du musst ein Passwort eingeben, um dieser Partie beitreten zu können!');?>);
            return;
        }

        var alias = this;
        $(alias).addClass('disabled').text(<?=__j('Validiere...');?>);
        edit.addClass('disabled');
        game.network.query('japi/gamemaster/check_pw', {password: pw,id: get_set('id')}, function(data) {
            $(alias).removeClass('disabled').text(<?=__j('Weiter');?>);
            edit.removeClass('disabled');
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            } else
                if (data.proceed) {
                    write_set({'password': pw}, <?=__j('Passwort bestätigt');?>);
                    game.render.html.notify('success', <?=__j('Dein Passwort wurde akzeptiert.');?>);
                } else game.render.html.notify('error', <?=__j('Das angegebene Passwort war leider nicht korrekt. Bitte prüfe deine Eingabe und versuche es erneut.');?>);
        })
    });

    $.each(games, function(k, lobby) {
        $.each(lobby.players, function(k, player) {
            $('[data-id=player-' + lobby.gameid + '-' + player.id + ']').addClass(player.cod ? 'red' : '').attr('title', player.cod ? player.cod : database.jobs[player.job].meta.name).qtip(game.render.html.qtip.player('top'));
        })
    });

    $('[data-modeset]').click(function() {
        $('[data-jobset]').parent().hide();
        $.each(database.modes[$(this).data('modeset')].jobs, function(k,v) {
            $('[data-jobset=' + v + ']').parent().show();
        });

        if (database.modes[$(this).data('modeset')].slots) {
            var ssel = $('#slots_in');
            ssel.html('');
            for (var i = database.modes[$(this).data('modeset')].slots[1]; i >= database.modes[$(this).data('modeset')].slots[0]; i--)
                ssel.append($('<option />').attr('value', i).text(game.i18n(<?=__j(':n Spieler');?>, {':n': i})));
            ssel.selectric();
        }
    });

    $('[data-set]').click(function() {
        write_set($(this).data('set'),$(this).data('caption'));
    });

    $('#btn_cancel').click(function() {
        game.network.load('lobby/main');
    });

    $('#btn_confirm').click(function() {
        var tmp_obj = {};
        $.each($('#data-container').serializeArray(), function(k,v) {
            tmp_obj[v.name] = v.value;
        });

        var layer = $('<div />');

        game.render.html.modal.fade();
        setTimeout(function() {
            layer.css({
                position: 'fixed',
                width: $(document).width()/2,
                height: 413 * (($(document).width()/2)/1280),
                top: 100,
                left: $(document).width()/4,
                opacity: 0,
                transform: 'scale(0.75)',
                'z-index': $.topZIndex('*') + 1
            }).addClass('logo').appendTo('body');

            layer.animate({
                opacity: 1,
                top: 300,
                transform: 'scale(1)'
            }, 4000, 'swing');
        }, 500);

        setTimeout(function() {
            game.network.query('japi/gamemaster/start', tmp_obj, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            } else
                game.network.load(data.redirect, {}, null, function() {
                    game.render.html.modal.clear();
                    layer.animate({
                        opacity: 0,
                        transform: 'scale(2)',
                        top: 400
                    }, 300, 'swing', function() {
                        layer.remove();
                    });
                });
            })}, 3000);
    });

    $('#lang_in').val(game.lang()).selectric({
        optionsItemBuilder: function(a) {
            return '<img src="media/icons/lang/' + a.value + '.png" alt="" />' + a.text;
        }
    });
    rebuild();
// ## JS COMPRESS END ## //
</script>

