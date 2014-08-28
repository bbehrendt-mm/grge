<?php
	if (!file_exists('syslock.f')) {
		header("HTTP/1.0 404 Not Found");
		die;
	}

	$msg = file_get_contents('syslock.f');
?>

<!DOCTYPE html>
<html>
	<head>		
		<!-- Meta -->
		<meta content="text/html; charset=UTF-8" />
		<meta http-equiv="content-language" content="de">
		<meta name="robots" content="nofollow" />
		<meta name="description" content="Ein Single Player Survival Game. Könnte Spuren von Zombies enthalten..." />
		<meta name="author" content="Benjamin 'Brainbox' Behrendt" />
		
		<!-- Basics -->
		<link rel="icon" href=/favicon.ico sizes="16x16 24x24 32x32 48x48 256x256" type="image/vnd.microsoft.icon" />
		<title>ZombVival ~ Der Server ist offline!</title>
		
		<!-- Make all the apple mindslaves happy -->
		<link rel="apple-touch-icon" sizes="57x57" href="application/assets/images/apple_small.png" />
		<link rel="apple-touch-icon" sizes="72x72" href="application/assets/images/apple_medium.png" />
		<link rel="apple-touch-icon" sizes="114x114" href="application/assets/images/apple_big.png" />
	</head>
		<body style="background-color: rgb(40,40,40); color: rgb(30,30,30); text-align: center;">
		
		<div style="color: #FFF6BF; font-size: 45px; font-weight: bold; font-family: cursive; margin-top: 110px; text-shadow: 0 0 8px black;">
			ZombVival
		</div>
		
		<div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0px; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
			<div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
				Wartungsarbeiten
			</div>
			<div style="padding: 10px; font-family: sans-serif; text-align: justify;">
				Das Spiel wird momentan gewartet um ein Systemupdate aufzuspielen und/oder eine St&ouml;rung zu beheben. Der normale Spielbetrieb wird so bald wie m&ouml;glich wieder aufgenommen. Aktualisiere diese Seite, um einen neuen Verbindungsversuch zu starten.
                Weitere Informationen findest du im <a href="http://forum.zombvival.de">Forum</a>. Bitte entschuldige die Unannehmlichkeiten.
			</div>
			<pre><?php echo $msg; ?></pre>
		</div>

        <div style="box-shadow: 0 0 8px black; margin: 10px auto; width: 810px; padding: 0px; border: 2px solid rgb(200,200,200); border-radius: 5px; background-color: rgb(230,230,230);">
            <div style="background-color: rgb(160,40,40); font-size: 24px; color: rgb(230,230,230); font-variant: small-caps; font-family: sans-serif; padding: 4px; font-weight: bold; border-bottom: 2px solid rgb(140,20,20); box-shadow: 0 0 6px black;">
                General Maintenance
            </div>
            <div style="padding: 10px; font-family: sans-serif; text-align: justify;">
                The game is currently offline to apply an update or fix a critical issue. Normal gameplay will resume as soon as possible. Refresh this page to start a new connection attempt or check the <a href="http://forum.zombvival.de">Forum</a> for further information.
                Please excuse the inconvienience.
            </div>
            <pre><?php echo $msg; ?></pre>
        </div>
        <div x-destination="gamebody">
            <script type="text/javascript">
                if (game) document.location.reload();
            </script>
        </div>
	</body>

</html>