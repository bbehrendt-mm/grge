<?php
    namespace {
        //Redirect
        defined('INDEX_CALL') or die('Gateway');
    }

    namespace Gateway {
        $users = [
            'Brainbox' => '0758397bf35982c7f07e467e074348730fdd6c80db7473f6d6bd144142731e7059406f62bf549c260ed4aa1b5454e6412f20fcbdd7411e3028310c314f457e1c',
        ];

        $encryption_mode = MCRYPT_RIJNDAEL_256;
        $encryption_key = '169a3f7b4da04886085949edbe3d70ce204dd7e6c4a9503b1a14f84073a90168';

        function encrypt($data) {
            global $encryption_mode,$encryption_key;
            $key = pack('H*', $encryption_key);

            $data = serialize($data);
            $data .= hash('sha512',$data,false);

            $iv_size = mcrypt_get_iv_size($encryption_mode, MCRYPT_MODE_CBC);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

            return base64_encode($iv . mcrypt_encrypt($encryption_mode,$key,$data,MCRYPT_MODE_CBC,$iv));
        }

        function decrypt($cipher) {
            global $encryption_mode,$encryption_key;
            $key = pack('H*', $encryption_key);

            $cipher = base64_decode($cipher);
            $iv_size = mcrypt_get_iv_size($encryption_mode, MCRYPT_MODE_CBC);

            $data = trim(mcrypt_decrypt($encryption_mode,$key,substr($cipher, $iv_size),MCRYPT_MODE_CBC,substr($cipher, 0, $iv_size)));

            $hash = substr($data,-128);
            $data = substr($data,0,-128);

            if (hash('sha512',$data,false) !== $hash) return false;
            if (($data = unserialize($data)) == false) return false;
            return $data;
        }

        $gateway_control = isset($_REQUEST['gw']) ? (empty($_REQUEST['gw']) ? true : $_REQUEST['gw']) : false;

        if ($gateway_control && isset($_REQUEST['u']) && isset($_REQUEST['p'])) {
            if (isset($users[$_REQUEST['u']]) && $users[$_REQUEST['u']] === hash('sha512',$_REQUEST['p'])) {
                $stamp = time() + 1800;
                setcookie('c_admin',encrypt($stamp),$stamp);
                $authorized = true;
            } else $authorized = false;
        } else $authorized = isset($_COOKIE['c_admin']) && ($ts = decrypt($_COOKIE['c_admin'])) && $ts > time();

        if ($authorized && !$gateway_control) return 0;

        if ($authorized && $gateway_control && $gateway_control !== true)
            switch ($gateway_control) {
                // Installer
                case 'i':
                    include 'install/index.php';
                    return 1;

                // Log Out
                case 'n':
                    setcookie('c_admin','',0);
                    $authorized = false;
                    $gateway_control = false;
                    break;

                // Maintenance
                case 'm_on':
                    file_put_contents('.maintenance','');
                    break;
                case 'm_off':
                    unlink('.maintenance');
                    break;
            }

        // Mainenance
        if ($gateway_control === 'n' && $authorized) {
            setcookie('c_admin','',0);
            $authorized = false;
            $gateway_control = false;
        }

        $maintenance = file_exists('.maintenance');

        // Check for maintenance file
        if (!$maintenance && !$gateway_control) return 0;
        else foreach (apache_request_headers() as $header => $value) {
            if (strtolower($header) == 'x-requested-with' && strtolower($value) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => [
                        'code' => 'GRGE-0002-0004',
                        'name' => 'E_SERVER_MAINTENANCE',
                        'message' => 'The server is currently under maintenance.',
                        'details' => null,
                    ]
                ], JSON_FORCE_OBJECT);
                die;
            }
        }
?>
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

    <style>

    </style>
</head>
<body style="background-color: rgb(40,40,40); color: rgb(30,30,30); text-align: center;">

    <div style="color: #FFF6BF; font-size: 45px; font-weight: bold; font-family: cursive; margin-top: 110px; text-shadow: 0 0 8px black;">
        ZombVival
    </div>

    <?php if ((!$gateway_control || $gateway_control === true) && !$authorized) { ?>

        <?php if ($maintenance) { ?>

            <div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
                <div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
                    Wartungsarbeiten
                </div>
                <div style="padding: 10px; font-family: sans-serif; text-align: justify;">
                    Das Spiel wird momentan gewartet um ein Systemupdate aufzuspielen und/oder eine St&ouml;rung zu beheben. Der normale Spielbetrieb wird so bald wie m&ouml;glich wieder aufgenommen. Aktualisiere diese Seite, um einen neuen Verbindungsversuch zu starten.
                    Weitere Informationen findest du im <a href="http://forum.zombvival.de">Forum</a>. Bitte entschuldige die Unannehmlichkeiten.
                </div>
            </div>

            <div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
                <div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
                    General Maintenance
                </div>
                <div style="padding: 10px; font-family: sans-serif; text-align: justify;">
                    The game is currently offline to apply an update or fix a critical issue. Normal gameplay will resume as soon as possible. Refresh this page to start a new connection attempt or check the <a href="http://forum.zombvival.de">Forum</a> for further information.
                    Please excuse the inconvienience.
                </div>
            </div>

        <?php } else { ?>

            <div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
                <div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
                    ZombVival Service Gateway
                </div>
                <div style="padding: 10px; font-family: sans-serif; text-align: justify;">
                    This is the ZombVival Service Gateway. If you're not an administrator, then there is nothing for you to see here; <a href="index.php">please return to the game.</a>
                </div>
            </div>

        <?php } ?>

        <div style="margin: 10px auto; width: 810px; padding: 0; text-align: right; font-family: monospace">
            <a style="color: #FFF6BF; font-size: small; text-decoration: none" href="index.php?gw=f">[Admin]</a>
        </div>

    <?php } elseif (!$authorized && $gateway_control && $gateway_control !== true) { ?>

        <div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
            <div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
                Login
            </div>
            <div style="padding: 10px; font-family: sans-serif; text-align: justify;">
                <p>This form is for <b>administrative login only!</b> Please do not attempt to log in using your game credentials. </p>
                <form action="index.php?gw=<?=$gateway_control?>" method="post" >
                    <input name="u" type="text" placeholder="Username" /><br />
                    <input name="p" type="password" placeholder="Password" /><br />
                    <button type="submit">Confirm</button>
                </form>
            </div>
        </div>
    <?php } elseif ($authorized) { ?>
        <div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
            <div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
                Authorized
            </div>
            <div style="padding: 10px; font-family: sans-serif; text-align: justify;">
                You are authorized.<br />
                <a style="color: #a34600; text-decoration: none; font-family: monospace" href="index.php">[Bypass Gateway]</a>
                <?php if ($maintenance) { ?>
                    <a style="color: #a34600; text-decoration: none; font-family: monospace" href="index.php?gw=m_off">[Disable Maintenance Mode]</a>
                <?php } else { ?>
                    <a style="color: #a34600; text-decoration: none; font-family: monospace" href="index.php?gw=m_on">[Enable Maintenance Mode]</a>
                <?php } ?>
                <a style="color: #a34600; text-decoration: none; font-family: monospace" href="index.php?gw=i">[Database Setup]</a>
                <a style="color: #a34600; text-decoration: none; font-family: monospace" href="index.php?gw=n">[Logout]</a>
            </div>
        </div>
    <?php } ?>

</body>
</html>
<?php return 1; } ?>