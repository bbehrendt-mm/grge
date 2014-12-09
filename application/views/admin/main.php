<?php
/**
 * @var string $user Username
 * @var number $duration Remaining token lifetime
 * @var string $expires Expiration string
 *
 * @var bool $allow_translate Allow Translation functions
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
<div class="row">
    <div class="cell rw-12 padded">
        <div class="tile" id="goto_lang">
            <i class="fa fa-language"></i>
        </div>
    </div>
</div>
<script type="application/javascript">
    $('#goto_lang').click(function(){
        game.network.load('admin/translate');
    })<?php if (!$allow_translate) { ?>.addClass('disabled')<?php } ?>;

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