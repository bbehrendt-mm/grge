<html>
<head>
    <!-- Meta -->
    <meta content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="de">
    <meta name="robots" content="index,nofollow" />
    <meta name="keywords" content="Zombie,Survival,Browsergame,Die Verdammten,Die2Nite,GRGE">
    <meta name="description" content="Ein Single Player Survival Game. Könnte Spuren von Zombies enthalten..." />
    <meta name="author" content="Benjamin 'Brainbox' Behrendt" />

    <!-- Basics -->
    <link rel="icon" href="media/fav/favicon.ico" sizes="16x16 24x24 32x32 48x48 256x256" />
    <title>ZombVival Evolved!</title>

    <!-- Make all the apple mindslaves happy -->
    <link rel="apple-touch-icon" sizes="57x57" href="media/fav/apple_small.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="media/fav/apple_medium.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="media/fav/apple_big.png" />

    <!-- Make all 5 people happy who realy use metro -->
    <meta name="msapplication-TileColor" content="#660000" />
    <meta name="msapplication-TileImage" content="media/fav/ms_tile.png" />

    <!-- Let's get some scripting action -->
    <script type="application/javascript" src="js/jquery.min.js" ></script>
    <script type="application/javascript" src="js/jquery.qtip.min.js" ></script>
    <script type="application/javascript" src="js/jquery.transform2d.js" ></script>
    <script type="application/javascript" src="js/jquery.topzindex.min.js" ></script>
    <script type="application/javascript" src="js/jquery.selectric.min.js" ></script>
    <script type="application/javascript" src="js/rasterizeHTML.allinone.js" ></script>
    <script type="application/javascript" src="js/framework.min.js" ></script>
    <script type="application/javascript" src="js/jquery.ext.min.js" ></script>

    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.qtip.min.css" />
    <link rel="stylesheet" type="text/css" href="css/zombvival.base.min.css" />
    <link rel="stylesheet" type="text/css" href="css/zombvival.min.css" />

</head>
<body>

    <div id="boot" style="display: none">
        <b>ZombVival Evolution</b>
        <i class="fa fa-spin fa-circle-o-notch"></i>
    </div>

    <div id="nojs">
        <b><?=__('JavaScript erforderlich');?></b>
        <i class="fa fa-exclamation-triangle"></i>
        <span><?=__(
        'Bitte lasse die Verwendung von JavaScript für die Domain :domain zu und überprüfe, ob dein Internetbrowser auf dem neusten Stand ist. Solltest du diese Meldung trotz aktiviertem JavaScript und aktuellem Browser angezeigt bekommen, melde dich bitte bei :admin.',
        array(
        ':domain' => '<i>zvg.boerde.de</i>',
        ':admin' => '<a href="mailto:kontakt@ruine.dvspot.de">Brainbox</a>',
        ));?></span>
    </div>

    <script type="text/javascript">
    // ## JS COMPRESS BEGIN ## //
        document.getElementById('boot').style.display = 'block';
        document.getElementById('nojs').style.display = 'none';
        window.addEventListener('load',function(){
            game.network.load('web/body',{},true);
        });
    // ## JS COMPRESS END ## //
    </script>
</body>
</html>