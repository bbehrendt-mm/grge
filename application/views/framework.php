<html>
<head>
    <!-- Meta -->
    <meta content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="de">
    <meta name="robots" content="nofollow" />
    <meta name="description" content="Ein Single Player Survival Game. Könnte Spuren von Zombies enthalten..." />
    <meta name="author" content="Benjamin 'Brainbox' Behrendt" />

    <!-- Basics -->
    <link rel="icon" href="media/fav/favicon.ico" sizes="16x16 24x24 32x32 48x48 256x256" />
    <title>ZombVival Evolved!</title>

    <link href='http://fonts.googleapis.com/css?family=Strait|Open+Sans|Exo+2:400,900' rel='stylesheet' type='text/css'>

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
    <script type="application/javascript" src="js/zombvival.min.js" ></script>


    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/jquery.qtip.min.css" />
    <link rel="stylesheet" type="text/css" href="css/zombvival.min.css" />
</head>
<body>

    <div id="boot">
        <b>ZombVival Evolution</b>
        <i class="fa fa-spin fa-circle-o-notch"></i>
    </div>

    <script type="text/javascript">
        window.addEventListener('load',function(){
            game.network.load('web/body',{},true);
        });
    </script>

</body>
</html>