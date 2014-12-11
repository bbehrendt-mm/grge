<?php
/**
 * @var string $user Username
 * @var number $duration Remaining token lifetime
 * @var string $expires Expiration string
 *
 * @var bool $allow_translate Allow Translation functions
 * @var bool $allow_userlist Allow user listing
 */

?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i><?=__('Kontrollzentrum');?></h1>

<div class="row">
    <div class="cell rw-6 padded">
        <div class="row">
            <div class="cell rw-4"><?=__('Benutzer');?></div>
            <div class="cell rw-8"><?=$user?></div>
        </div>
        <div class="row">
            <div class="cell rw-4"><?=__('Login-Gültigkeit');?></div>
            <div class="cell rw-8">
                <?=$expires?><br />
                <?=__(':s Sekunden (ca. :m Minuten)', [':s' => $duration, ':m' => round($duration/60)]);?>
            </div>
        </div>
    </div>
    <div class="cell rw-6 padded">
        <div class="btn" id="logout_adm"><?=__('Admin-Token zurückziehen');?></div>
        <div class="btn" id="logout"><?=__('Ausloggen');?></div>
    </div>
</div>
<div class="row" id="tiles">
    <div class="cell rw-12 padded">
        <div class="tile" data-ref="translate" data-icon="language" data-active="<?=$allow_translate ? 1 : 0 ?>"></div>
        <div class="tile" data-ref="users" data-icon="users" data-active="<?=$allow_userlist ? 1 : 0 ?>"></div>
    </div>
</div>
<script type="application/javascript">

    $('#tiles').find('.tile[data-active]').each(function() {
        $(this).append('<i class="fa fa-' + $(this).data('icon') + '" />');
        if ($(this).data('active') == '1')
            $(this).click(function(){
                game.network.load('admin/' + $(this).data('ref'));
            });
        else $(this).addClass('disabled');
    });

    $('#logout').click(function(){
        $(this).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
        game.network.query('japi/account/logout', {}, function(data) {
            if (data.error)
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            else {
                game.network.load(data.redirect);
            }
        });
    });

    $('#logout_adm').click(function(){
        $(this).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
        game.network.query('admin/japi/account/logout', {}, function(data) {
            if (data.error)
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            else {
                game.network.load(data.redirect);
            }
        });
    })
</script>