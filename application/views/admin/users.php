<?php
/**
 * @var string[] $permissions List of basic permissions
 */
/** @var array $achievements */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Benutzerverwaltung</h1>

<div class="row">
    <div class="cell rw-2 padded">
        <input type="text" id="search_value" class="form_input" placeholder="Suche..." />
    </div>
    <div class="cell rw-1 padded">
        <div class="btn" id="search"><i class="fa fa-search"></i></div>
    </div>
</div>

<div class="row">
    <div class="cell rw-6 padded">
        <div class="row-table padded row-table-borders row-table-striped row-table-interact" id="target_list">
            <div class="row">
                <div class="cell padded rw-1">&nbsp;</div>
                <div class="cell padded rw-2"><b>UID</b></div>
                <div class="cell padded rw-4"><b>Name</b></div>
                <div class="cell padded rw-5"><b>Accounts</b></div>
            </div>
        </div>
    </div>

    <div class="cell rw-6 padded">
        <h2><?=__('Infos');?></h2>
        <b id="infos_user">-</b> <i id="infos_role"></i>
        <div class="row-table padded row-table-borders row-table-striped row-table-interact">
            <div class="row">
                <div class="cell padded rw-6"><b>ALLOWED</b></div>
                <div class="cell padded rw-6"><b>DENIED</b></div>
            </div>
            <div class="row">
                <div class="cell padded rw-6" id="infos_allow">-</div>
                <div class="cell padded rw-6" id="infos_deny">-</div>
            </div>
        </div>

        <h2><?=__('Aktionen');?></h2>
        <div class="row">
            <div class="cell rw-6">Account Listing</div>
            <div class="cell rw-6">
                <label for="account_listing_status"></label><select class="form_input" id="account_listing_status" data-container="body">
                    <option value="-1">Blacklist</option>
                    <option value="0">Neutral</option>
                    <option value="1">Whitelist</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="cell-small rw-7">Achievements</div>
            <div class="cell-small rw-11">
                <label for="account_achievements"></label><select class="form_input" id="account_achievements" data-container="body">
                    <?php foreach ($achievements as $aid => $name) { ?>
                        <option value="<?=$aid?>"><?=__($name)?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="cell rw-2">
                <input type="number" placeholder="x" id="account_achievements_count" class="form_input" />
            </div>
            <div class="cell rw-1">
                <div class="btn" id="account_achievements_confirm"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>

        <h2><?=__('Rechteverwaltung');?></h2>
        <div class="row-table padded row-table-borders row-table-striped row-table-interact" id="permission_list">
            <div class="row">
                <div class="cell padded rw-8"><b>Flag</b></div>
                <div class="cell padded rw-4"><b>A / D / R / I</b></div>
            </div>
            <?php foreach ($permissions as $permission) { ?>
                <div class="row">
                    <div class="cell padded rw-8"><?=$permission?></div>
                    <div class="cell padded rw-1"><label title="Allow"><input type="radio" data-set="<?=$permission?>" name="permission_<?=$permission?>" value="1" /></label></div>
                    <div class="cell padded rw-1"><label title="Deny"><input type="radio" data-set="<?=$permission?>" name="permission_<?=$permission?>" value="-1" /></label></div>
                    <div class="cell padded rw-1"><label title="Remove"><input type="radio" data-set="<?=$permission?>" name="permission_<?=$permission?>" value="0" /></label></div>
                    <div class="cell padded rw-1"><label title="Ignore"><input type="radio" data-set="<?=$permission?>" name="permission_<?=$permission?>" value="" checked="checked" /></label></div>
                </div>
            <?php } ?>
        </div>
        <div class="btn" id="set_flags"><?=__('Setzen');?></div>
        <br />
        <div class="row">
            <div class="cell rw-10">
                <input type="text" class="form_input" placeholder="Panel PW" id="panel_pw" />
            </div>
            <div class="cell rw-1">
                <div class="btn" id="pw_set"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="cell rw-1">
                <div class="btn" id="pw_unset"><i class="fa fa-times-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#content').find('select').selectric();

    var get_selected = function() {
        return $.map($('#target_list').find(':checked'), function(k) {
            return $(k).val();
        });
    };

    var display = function(list) {
        var target = $('#target_list');
        target.find('.row:not(:first-child)').remove();
        $.each(list, function(k,v) {
            var key_list;
            target.append(
                Ω.row()
                    .append($('<div />').addClass('cell rw-1').append(
                        $('<label />').append($('<input type="checkbox" name="selection[]" />').val(v.uid).data('sel', v.uid))
                    ))
                    .append($('<div />').addClass('cell padded rw-2').text(v.uid))
                    .append($('<div />').addClass('cell padded rw-4 pointer').text(v.name).click(function() {
                        $('#account_listing_status').val(v.access).off('change').change(function() {
                            var value = $(this).val();
                            var users = get_selected();
                            if (users.length == 0) users = [v.uid];

                            $('#wrapper').addClass('disabled');
                            game.network.query('admin/japi/users/flag', {users: users, 'set': {'WHITELIST': value}}, function(data) {
                                if (data.error) {
                                    alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                                    return;
                                }

                                if (!data.success || data.success != "1") game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
                                else game.render.html.notify('success', 'OK');
                            }, function() {
                                $('#wrapper').removeClass('disabled');
                            })
                        }).selectric();

                        $('#account_achievements_confirm').off('click').on('click',function() {
                            var users = get_selected();
                            if (users.length == 0) users = [v.uid];

                            var aid = $('#account_achievements').val();
                            var count = $('#account_achievements_count').val();

                            game.network.query('admin/japi/users/achievements', {users: users, aid: aid, count: count}, function(data) {
                                if (data.error) {
                                    alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                                    return;
                                }

                                if (!data.success || data.success != "1") game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
                                else game.render.html.notify('success', 'OK');
                            }, function() {
                                $('#wrapper').removeClass('disabled');
                            })
                        });

                        info_loader(v.uid, v.name);
                    }).css(v.access != 0 ? {'font-weight': 'bold', 'color': v.access < 0 ? '#AA0000' : '#00AA00'} : {}))
                    .append(key_list = $('<div />').addClass('cell padded rw-5'))
            );

            $.each(v.auth, function(provider, data) {
                key_list.append($('<div />').addClass('pointer').text(provider).click(function() {
                    var pp = core.popup.spawn({desktop: 600, md: '100%'});

                    pp.append($('<h3 />').text(v.name + ' via ' + provider));
                    pp.append(
                        Ω.row()
                            .append(Ω.cell(false, 2).text('Remote ID'))
                            .append(Ω.cell(false, 10).text(data[0]))
                            .append(Ω.cell(false, 2).text('Variant 1'))
                            .append(Ω.cell(false, 10).text(data[1] ? data[1] : '-').css('word-wrap','break-word'))
                            .append(Ω.cell(false, 2).text('Variant 2'))
                            .append(Ω.cell(false, 10).text(data[2] ? data[2] : '-').css('word-wrap','break-word'))
                    )

                }))
            })
        });
        target.find(':checkbox').customRadioCheck();
    };

    var loader = function(action, args) {
        $('#wrapper').addClass('disabled');

        game.network.query('admin/japi/users/' + action, args, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                return;
            }

            if (!data.list) game.render.html.notify('info', <?=__j('Die Suche lieferte keine Ergebnisse.');?>);
            else display(data.list);
        }, function() {
            $('#wrapper').removeClass('disabled');
        })
    };

    var info_loader = function(uid,name) {
        $('#infos_user').text(name);
        $('#infos_role').text('');
        $('#infos_allow').text('-');
        $('#infos_deny').text('-');

        $('#wrapper').addClass('disabled');

        game.network.query('admin/japi/users/info', {id: uid}, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                return;
            }

            var tmp = [];
            $.each(data.flags.allowed, function(k,v) {tmp.push(v)});
            $('#infos_allow').text(tmp.length ? tmp.join(', ') : '-');
            tmp = [];
            $.each(data.flags.denied, function(k,v) {tmp.push(v)});
            $('#infos_deny').text(tmp.length ? tmp.join(', ') : '-');

            if (!data.admin.access) $('#infos_role').text(<?=__j('Kein Admin-Zugang');?>);
            else if (data.admin.access && data.admin.disabled) $('#infos_role').text(<?=__j('Inaktiver Admin-Zugang');?>);
            else $('#infos_role').text(<?=__j('Administrator');?>);
        }, function() {
            $('#wrapper').removeClass('disabled');
        })
    };

    $('#search').click(function() {
        loader('search', {
            query: $('#search_value').val()
        });
    });

    var password = function(users,pw) {
        var args = {
            users: users,
            set: pw
        };

        $('#wrapper').addClass('disabled');
        game.network.query('admin/japi/users/password', args, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                return;
            }

            if (!data.success || data.success != "1") game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
            else game.render.html.notify('success', pw.length ? <?=__j('Das Password wurde gesetzt.');?> : <?=__j('Das Password wurde entfernt.');?>);
        }, function() {
            $('#wrapper').removeClass('disabled');
        });
    };

    $('#set_flags').click(function() {
        var args = {
            users: get_selected(),
            set: {}
        };

        if (args.users.length == 0) {
            game.render.html.notify('error', <?=__j('Auswahl erforderlich!');?>);
            return;
        }

        $('#permission_list').find(':checked').each(function() {
            if ($(this).val() != '') {
                args.set[$(this).data('set')] = $(this).val();
            }
        });

        $('#wrapper').addClass('disabled');
        game.network.query('admin/japi/users/flag', args, function(data) {
            if (data.error) {
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
                return;
            }

            if (!data.success || data.success != "1") game.render.html.notify('error', <?=__j('Ein Fehler ist aufgetreten...');?>, <?=__j('Oops');?>);
            else game.render.html.notify('success', <?=__j('Die Flags wurden übermittelt.');?>);
        }, function() {
            $('#wrapper').removeClass('disabled');
        })
    });

    $('#pw_set').click(function() {
        var users = get_selected();
        var pw = $('#panel_pw').val();

        if (!users.length || !pw.length)
            game.render.html.notify('error', <?=__j('Auswahl erforderlich!');?>);
        else password(users,pw);
    });
    $('#pw_unset').click(function() {
        var users = get_selected();

        if (!users.length)
            game.render.html.notify('error', <?=__j('Auswahl erforderlich!');?>);
        else password(users,'');
    });
// ## JS COMPRESS END ## //
</script>