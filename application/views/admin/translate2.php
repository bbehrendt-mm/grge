<?php
/**
 * @var string $base Base lang
 * @var string[] $langs Available Languages
 * @var bool $adv_priv Additional privileges
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Übersetzungen');?></h1>

<div class="row" id="lang-settings">
    <div class="cell rw-12 padded">
        <div class="toolbox">
            <div class="row">
                <div class="cell rw-6 padded">
                    <div class="row">
                        <div class="cell rw-6 center">
                            <b><?=__('Quellsprache');?></b>
                            <div class="row"><div class="cell rw-12 padded">
                                <label for="lng-from"></label><select id="lng-from">
                                    <option value="" selected>-- <?=__('Bitte auswählen');?> --</option>
                                    <option value="<?=$base?>"><?=$base?></option>
                                    <?php foreach ($langs as $lang) {?>
                                        <option value="<?=$lang?>"><?=$lang?></option>
                                    <?php } ?>
                                </select>
                            </div></div>
                        </div>
                        <div class="cell rw-6 center">
                            <b><?=__('Zielsprache');?></b>
                            <div class="row"><div class="cell rw-12 padded">
                                <label for="lng-to"></label><select id="lng-to">
                                    <option value="" selected>-- <?=__('Bitte auswählen');?> --</option>
                                    <?php foreach ($langs as $lang) {?>
                                        <option value="<?=$lang?>"><?=$lang?></option>
                                    <?php } ?>
                                </select>
                            </div></div>
                        </div>
                    </div>
                </div>
                <div class="cell rw-6 padded">
                    <div class="note">
                        <b><?=__('Einstellungen');?></b><br />
                        <?=__('Bitte wähle die Quell- und Zielsprache für deine Übersetzung, um mit der Arbeit zu beginnen.');?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row" id="lang-settings">
    <div class="cell rw-12 padded">
        <div class="toolbox">

            <div class="row center tools">
                <div class="cell rw-2">
                    <i id="tl_save" class="fa fa-save pointer" title="<?=__('Speichern & Weiter');?>"></i>
                </div>
                <div class="cell rw-2">
                    <i id="tl_next" class="fa fa-arrow-right pointer" title="<?=__('Überspringen');?>"></i>
                </div>
                <div class="cell rw-2 <?=$adv_priv ? '' : 'disabled'?>">
                    <i id="tl_delete" class="fa fa-trash pointer" title="<?=__('Löschen');?>"></i>
                </div>
                <div class="cell rw-2">
                    <i id="tl_reset" class="fa fa-undo pointer" title="<?=__('Zurücksetzen');?>"></i>
                </div>
                <div class="cell rw-2">
                    <i id="tl_ggl" class="fa fa-google pointer" title="<?=__('Google-Übersetzer öffnen');?>"></i>
                </div>
                <div class="cell rw-2">
                    <i id="tl_search" class="fa fa-search pointer" title="<?=__('Übersetzung suchen');?>"></i>
                </div>
            </div>

            <div class="row">
                <div class="cell rw-6 padded center">
                    <b><?=__('Original');?></b><br />
                    <textarea class="form_input resize-v" style="height: 256px" id="input-from" readonly="readonly"></textarea>
                </div>
                <div class="cell rw-6 padded center">
                    <b><?=__('Übersetzung');?></b><br />
                    <textarea class="form_input resize-v" style="height: 256px" id="input-to"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="cell rw-6 ro-6 right padded">
                    <b><?=__('Probleme?');?></b> <div class="btn small" id="goto_old"><?=__('Zum alten Übersetzungstool');?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="cell rw-12 padded">
        <b><?=__('Übersetzungsfortschritt');?>: </b> <span id="completion"><?=__('Unbekannt');?></span>
    </div>
</div>

<div class="row">
    <div class="cell rw-6 ro-3">
        <div class="note">
            <b><?=__('Vielen Dank!');?></b><br />
            <?=__('Vielen herzlichen Dank dafür, dass du dabei hilfst, ZombVival in eine andere Sprache zu übersetzen.');?>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
(function() {
    $('#goto_old').click(function() {
        game.network.load('admin/translate/old');
    });

    var current_request;

    var lang_from = null, lang_to = null, current_id = null;

    var sel_from = $('#lng-from');
    var sel_to = $('#lng-to');

    var reset = function() {
        $('#tl_delete, #tl_next, #tl_reset, #tl_save, #input-from, #input-to').addClass('disabled');
        $('#input-from, #input-to').val('');

        current_id = null;
    };

    var connect = function(action, args, callback) {
        if (!args) args = {};
        if (!(args.from = lang_from) || !(args.to = lang_to)) return;

        $('.tools').addClass('disabled');
        if (current_request) current_request.abort();
        current_request = game.network.query('admin/japi/translate/' + action, args, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                return;
            }

            callback(data);
        }, function() {
            $('.tools').removeClass('disabled');
        });
    };

    var display = function(id, original, translation) {
        $('#tl_delete, #tl_next, #tl_reset, #tl_save, #input-from, #input-to').removeClass('disabled');

        $('#input-from').val(original);
        $('#input-to').val(translation ? translation : original);

        current_id = id;
    };

    var next = function(save) {
        var translation = $('#input-to').val();

        if ((!translation || !current_id) && save) {
            game.render.html.notify('error', <?=__j('Leere Übersetzungen können nicht gespeichert werden!');?>);
            return;
        }

        connect('next', save ? {id: current_id, translation: translation} : {id: current_id}, function(data) {
            if (save && !data.success) {
                game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>);
                return;
            } else if (save && !data.success)
                game.render.html.notify('success', <?=__j('Deine Übersetzung wurde erfolgreich gespeichert.');?>, <?=__j('Vielen Dank!');?>);

            if (data.next)
                display(data.next.id, data.next.original, data.next.translation);
            else {
                game.render.html.notify('success', <?=__j('Es liegen aktuell keine weiteren Texte vor, die übersetzt werden können.');?>);
                reset();
            }

            if (data.completion)
                $('#completion').html(game.i18n(<?=__j('::b:::prc Prozent::/b:: der Texte wurden bereits übersetzt (:trans/:total). Es fehlen ::b:::missing::/b:: Einträge.')?>, {':prc': Math.round10(100 * data.completion[0]/data.completion[1]), ':trans': data.completion[0], ':total': data.completion[1], ':missing': data.completion[1] - data.completion[0]}));
        });
    };

    var remove = function() {
        <?php if ($adv_priv) { ?>
            if (!confirm(<?=__j('Der Eintrag wird aus allen Übersetzungsdateien entfernt. Sicher?')?>)) return;

            connect('del', {id: current_id}, function(data) {
                if (data.success) {
                    game.render.html.notify('success', <?=__j('Der Eintrag wurde entfernt.');?>, <?=__j('Vielen Dank!');?>);
                    next();
                }
                else game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
            });
        <?php } ?>
        reset();
    };

    $('#lng-from, #lng-to').selectric().change(function() {
        reset();

        if (sel_from.val() && sel_to.val()) {
            if (sel_from.val() == sel_to.val()) {
                game.render.html.notify('error', <?=__j('Quell- und Zielsprache dürfen nicht identisch sein.');?>);
                return;
            }
            lang_from = sel_from.val();
            lang_to = sel_to.val();

            next();
        }
    });

    $('#tl_search').click(function() {
        var popup = core.popup.spawn({desktop: 600, md: '100%'});

        var tar;

        popup.append(
            NF.row()
                .append(NF.cell(true, 12).append($('<input />').addClass('form_input').attr({type: 'text', placeholder: <?=__j('Suchbegriff eingeben...');?>}).keyup(function() {
                    var query = $(this).val();
                    if (query.length < 4) return;
                    $(tar).empty().append(NF.row().append(NF.cell(true, 12, 0, 'center').append(NF.fa('circle-o-notch', true))));
                    connect('search', {from: lang_from, to: lang_to, q: query}, function(data) {
                        $(tar).empty();
                        var has = false;
                        $.each(data.result, function(id, entry) {
                            has = true;
                            $(tar).append(NF.cell(false, 12).append(
                                $('<div />').addClass('hotbox').css({'font-size': 12, 'font-family': 'monospace'}).append(NF.row()
                                    .append(NF.cell(true, 6).text(entry.from))
                                    .append(NF.cell(true, 6).text(entry.to))
                            ).click(function() {
                                popup.trigger('unpop');
                                display(entry.id, entry.from, entry.to)
                            })))
                        });
                        if (!has) $(tar).text(<?=__j('Es wurden keine Übersetzungen gefunden.');?>);
                    })
                })))
                .append(NF.cell(true, 12).append(tar = NF.row().css({'max-height': 300, 'overflow': 'auto'})))
        )
    });

    $('#tl_save').click(function() {next(true)});
    $('#tl_next').click(function() {next()});
    $('#tl_reset').click(function() {$('#input-to').val($('#input-from').val())});
    $('#tl_delete').click(function() {remove()});
    $('#tl_ggl').click(function() {window.open(encodeURI('https://translate.google.de/#' + lang_from + '/' + lang_to + '/' + $('#input-from').val()));});

    reset();
})();
// ## JS COMPRESS END ## //
</script>