<span id="main-menu-login"><?=__('Login');?></span>
<script type="application/javascript">
    $('#main-menu-login').click(function(){
        game.network.load('account/login');
    });
</script>