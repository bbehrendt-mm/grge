<span id="main-menu-game"><?=__('Spielen')?></span>
<span id="main-menu-main"><?=__('Neuigkeiten')?></span>
<span id="main-menu-ranking"><?=__('Ranking')?></span>
<span id="main-menu-logout"><?=__('Logout')?></span>
<script type="application/javascript">
// ## JS COMPRESS BEGIN ## //
    $('#main-menu-game').click(function() {
        game.network.load('game/redirect');
    });
    $('#main-menu-main').click(function() {
        game.network.load('lobby/main');
    });
    $('#main-menu-ranking').click(function() {
        game.network.load('ranking/lists');
    });
    $('#main-menu-logout').click(function(){
        $(this).html('<i class="fa fa-spin fa-circle-o-notch"></i>');
        game.network.query('japi/account/logout', {}, function(data) {
            if (data.error)
                alert(data.error.code + ' [' + data.error.name + ']: ' + data.error.message);
            else {
                game.network.load(data.redirect);
                game.render.html.notify('smile','<?=__('Komm bald zurück! Die Zombies fühlen sich sonst so einsam...')?>','<?=__('Bis bald!');?>', 4000)
            }
        });
    });
// ## JS COMPRESS END ## //
</script>