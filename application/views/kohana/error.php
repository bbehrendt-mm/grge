<?php defined('SYSPATH') OR die('No direct script access.') ?>

<!DOCTYPE html>

<!-- ### GRG CORE INLINE RENDERING EXCEPTION: ERROR PAGE BEYOND THIS LINE ### -->

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

    <!-- Make all the apple mindslaves happy -->
    <link rel="apple-touch-icon" sizes="57x57" href="media/fav/apple_small.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="media/fav/apple_medium.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="media/fav/apple_big.png" />

    <!-- Make all 5 people happy who realy use metro -->
    <meta name="msapplication-TileColor" content="#660000" />
    <meta name="msapplication-TileImage" content="media/fav/ms_tile.png" />



    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="css/zombvival.min.css" />

    <style type="text/css">
        #kohana_error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }
        #kohana_error h1,
        #kohana_error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #911; color: #fff; }
        #kohana_error h1 a,
        #kohana_error h2 a { color: #fff; }
        #kohana_error h2 { background: #222; }
        #kohana_error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }
        #kohana_error p { margin: 0; padding: 0.2em 0; }
        #kohana_error a { color: #1b323b; }
        #kohana_error pre { overflow: auto; white-space: pre-wrap; }
        #kohana_error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }
        #kohana_error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }
        #kohana_error div.content { padding: 0.4em 1em 1em; overflow: hidden; }
        #kohana_error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }
        #kohana_error pre.source span.line { display: block; }
        #kohana_error pre.source span.highlight { background: #f0eb96; }
        #kohana_error pre.source span.line span.number { color: #666; }
        #kohana_error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }
        #kohana_error ol.trace li { margin: 0; padding: 0; }
        .js .collapsed { display: none; }
    </style>
    <script type="text/javascript">
    // ## JS COMPRESS BEGIN ## //
        document.documentElement.className = document.documentElement.className + ' js';
        function koggle(elem)
        {
            elem = document.getElementById(elem);

            if (elem.style && elem.style['display'])
            // Only works with the "style" attr
                var disp = elem.style['display'];
            else if (elem.currentStyle)
            // For MSIE, naturally
                var disp = elem.currentStyle['display'];
            else if (window.getComputedStyle)
            // For most other browsers
                var disp = document.defaultView.getComputedStyle(elem, null).getPropertyValue('display');

            // Toggle the state of the "display" style
            elem.style.display = disp == 'block' ? 'none' : 'block';
            return false;
        }
    // ## JS COMPRESS END ## //
    </script>
</head>

<body onload="jQuery('.lang-dep').hide(); jQuery('.lang-en').show();">
<div id="wrapper">
    <div></div>
    <div id="content">
        <h1 style="text-align: center">
            <img style="margin: 10px; cursor: pointer; height: 22px; width: 32px;" src="media/icons/lang/de.png" onclick="$('.lang-dep').hide(); $('.lang-de').show();">
            <img style="margin: 10px; cursor: pointer; height: 22px; width: 32px;" src="media/icons/lang/en.png" onclick="$('.lang-dep').hide(); $('.lang-en').show();">
        </h1>
        <div class="row lang-dep lang-de">
            <h2>Kritischer Fehler</h2>
            <b>Hier ist leider etwas schief gegangen! <i>Bitte entschuldige diesen Fehler.</i></b><br /><br />
            Offensichtlich bist du soeben über einen peinlichen Systemfehler gestolpert. Es wäre nett, wenn du diesen Fehler im <a href="#" onclick="window.open('http://forum.zombvival.de')">Forum</a> melden würdest, damit er so schnell wie möglich behoben werden kann.<br /><br />

            <b>Was du jetzt tun kannst:</b>
            <ul>
                <li>Versuche, die Seite <a href="#" onclick="document.location.href = 'index.php';">neu zu laden</a>.</li>
                <li><a href="#" onclick="document.cookie = 'session=; expires=Thu, 01 Jan 1970 00:00:00 UTC'; document.location.reload(true);">Logge dich aus</a> und wieder ein.</li>
                <li>Versuche, deine letzte Aktion zu wiederholen.</li>
                <li>Schau im Forum nach, ob bereits Informationen zu diesem Fehler vorliegen. Wenn nicht, poste selbst eine Fehlerbeschreibung.</li>
            </ul>

            Bitte füge folgende Informationen ein, wenn du diesen Fehler meldest:<br />

            <div style="font-family: monospace">
                <br />
                <?php echo $class ?> [ <?php echo $code ?> ]: <?php echo htmlspecialchars( (string) $message, ENT_QUOTES, Kohana::$charset, TRUE); ?><br />
                Aufgetreten bei <?php echo Debug::path($file) ?> [ <?php echo $line ?> ]
            </div>
        </div>

        <div class="row lang-dep lang-en">
            <h2>Critical Error</h2>
            <b>Something seems to have gone horribly wrong. <i>Please excuse this inconvenience.</i></b><br /><br />
            It seems you have stumbled upon some sort of embarrassing system error. It would be nice if you could report this on the <a href="#" onclick="window.open('http://forum.zombvival.de')">forum</a>, so it can be fixed as soon as possible.<br /><br />

            <b>Here are some things you can do now:</b>
            <ul>
                <li>Try <a href="#" onclick="document.location.href = 'index.php';">reloading</a> the page.</li>
                <li><a href="#" onclick="document.cookie = 'evolution=; expires=Thu, 01 Jan 1970 00:00:00 UTC'; document.location.reload(true);">Log out</a> and back in.</li>
                <li>Try repeating your last action.</li>
                <li>Check the forum for details about this problem. If there aren't any, post them yourself.</li>
            </ul>

            When reporting the error, please include the following information:<br />

            <div style="font-family: monospace">
                <br />
                <?php echo $class ?> [ <?php echo $code ?> ]: <?php echo htmlspecialchars( (string) $message, ENT_QUOTES, Kohana::$charset, TRUE); ?><br />
                Occured at <?php echo Debug::path($file) ?> [ <?php echo $line ?> ]
            </div>
        </div>

        <?php if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1','::1'))) { ?>

            <div class="row">
                <h2>Debugging-Informationen</h2>
                <?php
                // Unique error identifier
                $error_id = uniqid('error');

                ?>
                <div id="kohana_error">
                    <h1><span class="type"><?php echo $class ?> [ <?php echo $code ?> ]:</span> <span class="message"><?php echo htmlspecialchars( (string) $message, ENT_QUOTES, Kohana::$charset, TRUE); ?></span></h1>
                    <div id="<?php echo $error_id ?>" class="content">
                        <p><span class="file"><?php echo Debug::path($file) ?> [ <?php echo $line ?> ]</span></p>
                        <?php echo Debug::source($file, $line) ?>
                        <ol class="trace">
                            <?php foreach (Debug::trace($trace) as $i => $step): ?>
                                <li>
                                    <p>
                <span class="file">
                    <?php if ($step['file']): $source_id = $error_id.'source'.$i; ?>
                        <a href="#<?php echo $source_id ?>" onclick="return koggle('<?php echo $source_id ?>')"><?php echo Debug::path($step['file']) ?> [ <?php echo $step['line'] ?> ]</a>
                    <?php else: ?>
                        {<?php echo __('PHP internal call') ?>}
                    <?php endif ?>
                </span>
                                        &raquo;
                                        <?php echo $step['function'] ?>(<?php if ($step['args']): $args_id = $error_id.'args'.$i; ?><a href="#<?php echo $args_id ?>" onclick="return koggle('<?php echo $args_id ?>')"><?php echo __('arguments') ?></a><?php endif ?>)
                                    </p>
                                    <?php if (isset($args_id)): ?>
                                        <div id="<?php echo $args_id ?>" class="collapsed">
                                            <table cellspacing="0">
                                                <?php foreach ($step['args'] as $name => $arg): ?>
                                                    <tr>
                                                        <td><code><?php echo $name ?></code></td>
                                                        <td><pre><?php echo Debug::dump($arg) ?></pre></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </table>
                                        </div>
                                    <?php endif ?>
                                    <?php if (isset($source_id)): ?>
                                        <pre id="<?php echo $source_id ?>" class="source collapsed"><code><?php echo $step['source'] ?></code></pre>
                                    <?php endif ?>
                                </li>
                                <?php unset($args_id, $source_id); ?>
                            <?php endforeach ?>
                        </ol>
                    </div>
                    <h2><a href="#<?php echo $env_id = $error_id.'environment' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Environment') ?></a></h2>
                    <div id="<?php echo $env_id ?>" class="content collapsed">
                        <?php $included = get_included_files() ?>
                        <h3><a href="#<?php echo $env_id = $error_id.'environment_included' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Included files') ?></a> (<?php echo count($included) ?>)</h3>
                        <div id="<?php echo $env_id ?>" class="collapsed">
                            <table cellspacing="0">
                                <?php foreach ($included as $file): ?>
                                    <tr>
                                        <td><code><?php echo Debug::path($file) ?></code></td>
                                    </tr>
                                <?php endforeach ?>
                            </table>
                        </div>
                        <?php $included = get_loaded_extensions() ?>
                        <h3><a href="#<?php echo $env_id = $error_id.'environment_loaded' ?>" onclick="return koggle('<?php echo $env_id ?>')"><?php echo __('Loaded extensions') ?></a> (<?php echo count($included) ?>)</h3>
                        <div id="<?php echo $env_id ?>" class="collapsed">
                            <table cellspacing="0">
                                <?php foreach ($included as $file): ?>
                                    <tr>
                                        <td><code><?php echo Debug::path($file) ?></code></td>
                                    </tr>
                                <?php endforeach ?>
                            </table>
                        </div>
                        <?php foreach (array('_SESSION', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER') as $var): ?>
                            <?php if (empty($GLOBALS[$var]) OR ! is_array($GLOBALS[$var])) continue ?>
                            <h3><a href="#<?php echo $env_id = $error_id.'environment'.strtolower($var) ?>" onclick="return koggle('<?php echo $env_id ?>')">$<?php echo $var ?></a></h3>
                            <div id="<?php echo $env_id ?>" class="collapsed">
                                <table cellspacing="0">
                                    <?php foreach ($GLOBALS[$var] as $key => $value): ?>
                                        <tr>
                                            <td><code><?php echo htmlspecialchars( (string) $key, ENT_QUOTES, Kohana::$charset, TRUE); ?></code></td>
                                            <td><pre><?php echo Debug::dump($value) ?></pre></td>
                                        </tr>
                                    <?php endforeach ?>
                                </table>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
            </div>
        <?php } ?>
    </div>
</div>
</body>
</html>









