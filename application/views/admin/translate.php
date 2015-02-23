<?php
/**
 * @var string $base Base lang
 * @var string[] $langs Available Languages
 * @var bool $adv_priv Additional privileges
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Übersetzungen');?></h1>

<div class="row" id="actionbar">

    <div class="cell rw-1 padded">
        <label for="mark"></label><select id="mask">
            <option value="<?=$base?>"><?=$base?></option>
            <?php foreach ($langs as $lang) {?>
                <option value="<?=$lang?>"><?=$lang?></option>
            <?php } ?>
        </select>
    </div>

    <div class="cell rw-1 padded">
        <label for="lang"></label><select id="lang">
            <?php foreach ($langs as $lang) {?>
                <option value="<?=$lang?>"><?=$lang?></option>
            <?php } ?>
        </select>
    </div>

    <div class="cell rw-2 padded">
        <div class="btn" id="from_prefetch"><?=__('Lade Cache');?></div>
    </div>
    <div class="cell rw-2 padded">
        <div class="btn" id="from_auto"><?=__('Autosuche');?></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="from_all"><?=__('Voll');?></div>
    </div>
    <div class="cell rw-4 padded">
        <input type="text" class="form_input" placeholder="<?=__('Suchbegriff eingeben...');?>" id="searchbox" />
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="from_search"><i class="fa fa-search"></i></div>
    </div>
</div>

<div class="row">
    <div class="cell rw-1 ro-6 padded">
        <div class="btn" id="format_b"><i class="fa fa-bold"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="format_i"><i class="fa fa-italic"></i></div>
    </div>
</div>

<div class="row">
    <div class="cell rw-6 padded">
        <textarea class="form_input resize-v" style="height: 256px" id="lang-from" readonly="readonly"></textarea>
    </div>
    <div class="cell rw-6 padded">
        <textarea class="form_input resize-v" style="height: 256px" id="lang-to"></textarea>
    </div>
</div>

<div class="row disabled" id="toolbar">
    <div class="cell rw-5 padded" id="pos_display"></div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_del"><i class="fa fa-trash-o"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_prev"><i class="fa fa-angle-left"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_reset"><i class="fa fa-undo"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_save"><i class="fa fa-floppy-o"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_next"><i class="fa fa-angle-right"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_trg"><i class="fa fa-google"></i></div>
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="toolbar_trb"><i class="fa fa-windows"></i></div>
    </div>
</div>

<div class="row">
    <form class="<?=$adv_priv ? '' : 'disabled'?>" enctype="multipart/form-data" action="admin/files/import_translations" method="POST" target="_blank" id="grl_uploader">
        <div class="row">
            <div class="cell rw-2 padded">
                <label for="lang2"></label><select name="lang" id="lang2">
                    <?php foreach ($langs as $lang) {?>
                        <option value="<?=$lang?>"><?=$lang?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="cell rw-4 padded">
                <div class="btn" id="file_export"><?=__('Exportieren');?></div>
            </div>
            <div class="cell rw-4 padded">
                <div class="btn" id="file_import">
                    <?=__('Importieren');?>
                    <input name="grl" id="grl" type="file" />
                </div>
            </div>
        </div>
    </form>
</div>


<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#lang, #lang2, #mask').selectric();



    var translation_source = {};
    var translation_mask = {};
    var translation_data = {};
    var translation_length = 0;
    var translation_index = 0;

    var jump = function(dif) {
        var e = $('#lang-from');
        var f = $('#lang-to');
        if (e.data('length') == 0)
            return;

        translation_index += dif;
        while (translation_index < 0)
            translation_index += translation_length;
        while (translation_index >= translation_length)
            translation_index -= translation_length;

        e.val(translation_mask[translation_index]);
        f.val(translation_data[translation_index]);
        $('#pos_display').text((translation_index + 1) + " / " + translation_length)
    };

    var format = function(add_tag) {
        var f = $('#lang-to');
        var str = f.val();
        var start = f[0].selectionStart;
        var end = f[0].selectionEnd;

        f.val(str.slice(0,start) + '::' + add_tag + '::' + str.slice(start,end) + '::/' + add_tag + '::' + str.slice(end));
    };

    var loader = function(source,search) {
        var args = {
            language: $('#lang').val(),
            mask: $('#mask').val(),
            source: source,
            search: (source == 'all') ? search : ''
        };

        if (args.language == args.mask) {
            game.render.html.notify('error', <?=__j('Quell- und Zielsprache dürfen nicht identisch sein.');?>);
            return;
        }

        $('#toolbar, #actionbar').addClass('disabled');

        game.network.query('admin/japi/translate/get', args, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                return;
            }
            translation_index = translation_length = 0;
            $.each(data.translations, function(k,v) {
                translation_source[translation_length] = k;
                translation_mask[translation_length] = data.masks[k] ? data.masks[k] : k;
                translation_data[translation_length] = v;
                translation_length++;
            });

            $('#lang-from').val(translation_length > 0 ? translation_mask[0] : '');
            $('#lang-to').val(translation_length > 0 ? translation_data[0] : '');
            $('#pos_display').text(translation_length > 0 ? ("1 / " + translation_length) : '');

            if (translation_length > 0) {
                $('#toolbar').removeClass('disabled');
            } else game.render.html.notify('info', <?=__j('Es wurden keine Übersetzungen gefunden.');?>);
        }, function() {
            $('#actionbar').removeClass('disabled');
        })
    };

    $('#from_auto').click(function() {
        loader('auto');
    });
    $('#from_prefetch').click(function() {
        loader('prefetch');
    });
    $('#from_all').click(function() {
        loader('all', '');
    });

    $('#from_search').click(function() {
        loader('all', $('#searchbox').val());
    });

    $('#toolbar_del').click(function() {
        if (!confirm(<?=__j('Der Eintrag wird aus allen Übersetzungsdateien entfernt. Sicher?')?>)) return;

        $('#toolbar, #actionbar').addClass('disabled');

        game.network.query('admin/japi/translate/del', {
            from: translation_source[translation_index]
        }, function(data) {

            if (data.error)
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            else if (data.success > 0) {
                game.render.html.notify('success', <?=__j('Der Eintrag wurde entfernt.');?>, <?=__j('Vielen Dank!');?>);

                var tmp_1 = {}, tmp_2 = {}, tmp_3 = {};
                for (var i = 0; i < translation_length; i++)
                    if (i != translation_index) {
                        tmp_1[(i > translation_index) ? i-1 : i] = translation_data[i];
                        tmp_2[(i > translation_index) ? i-1 : i] = translation_source[i];
                        tmp_3[(i > translation_index) ? i-1 : i] = translation_mask[i];
                    }

                translation_data = tmp_1;
                translation_source = tmp_2;
                translation_mask = tmp_3;
                translation_length--;
                if (translation_index >= translation_length) translation_index = 0;
                jump(0);
            } else game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
        }, function() {
            $('#toolbar, #actionbar').removeClass('disabled');
        });
    })<?php if (!$adv_priv) { ?>.addClass('disabled')<?php } ?>;


    $('#toolbar_prev').click(function() {
        jump(-1);
    });
    $('#toolbar_next').click(function() {
        jump(1);
    });
    $('#toolbar_reset').click(function() {
        $('#lang-to').val($('#lang-from').val());
    });
    $('#toolbar_save').click(function() {
        $('#toolbar, #actionbar').addClass('disabled');

        game.network.query('admin/japi/translate/set', {
            from: translation_source[translation_index],
            to: $('#lang-to').val(),
            language: $('#lang').val()
        }, function(data) {
            if (data.error)
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            else if (data.success > 0) {
                translation_data[translation_index] = $('#lang-to').val();
                game.render.html.notify('success', <?=__j('Deine Übersetzung wurde erfolgreich gespeichert.');?>, <?=__j('Vielen Dank!');?>);
                jump(1);
            } else game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
        }, function() {
            $('#toolbar, #actionbar').removeClass('disabled');
        });
    });

    $('#toolbar_trg').click(function() {
        var text = translation_data[translation_index].replace(/::\/?\w::/g, '');
        window.open(encodeURI('https://translate.google.de/#' + $('#mask').val() + '/' + $('#lang').val() + '/' + text));
    });
    $('#toolbar_trb').click(function() {}).addClass('disabled');

    $('#format_b').mousedown(function() {
        format('b');
    });
    $('#format_i').mousedown(function() {
        format('i');
    });

    $('#file_export').click(function() {
        window.open('admin/files/export_translations/' + $('#lang2').val());
    });

    $('#grl').change(function() {
        $('#grl_uploader').submit();
    });
// ## JS COMPRESS END ## //
</script>