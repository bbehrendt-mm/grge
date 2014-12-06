<?php
/**
 * @var string $user Username
 * @var number $duration Remaining token lifetime
 * @var string $expires Expiration string
 */
?>

<h1 class="noclick"><i class="fa fa-arrow-circle-right"></i>Kontrollzentrum</h1>

<div class="row">
    <div class="cell rw-6 padded">
        <h2>Login-Informationen</h2>
        <div class="row">
            <div class="cell rw-4">Benutzer</div>
            <div class="cell rw-8"><?=$user?></div>
        </div>
        <div class="row">
            <div class="cell rw-4">Login-Gültigkeit</div>
            <div class="cell rw-8">
                <?=$expires?><br />
                <?=$duration?> Sekunden (ca. <?=round($duration/60)?> Minuten)
            </div>
        </div>
        <div class="row">
            <div class="cell rw-4">Aktionen</div>
            <div class="cell rw-8">
                <div class="btn" id="logout_adm">Admin-Token zurückziehen</div>
                <div class="btn" id="logout">Ausloggen</div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
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