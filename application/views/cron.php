<?php
/**
 * @var string $baseurl
 * @var string $version
 * @var mixed $autoprc
 */
?>
<!DOCTYPE html>
<html>
<head>
    <?php $time = strtotime('-1 day'); ?>
    <title>ZombVival System Report <?php echo date('d.m.Y', $time); ?></title>
    <style>
        body{background: #100C0B url("<?=$baseurl?>media/img/background.jpg") no-repeat scroll center top}
        body>div{
            background: #FFF6BF url("<?=$baseurl?>media/img/dunes.png") repeat-x scroll center bottom;
            padding: 0 0 100px;
            border: 2px solid #600;
            border-radius: 9px;
            margin: 0 auto;
            width: 900px;
            overflow: hidden;
        }
        body>div>.header {padding: 5px; background: #600; box-shadow: 0 0 5px black;}
        body>div>.header>h1 {color: #FFF6BF; text-align: center; margin: 0; text-shadow: 0 0 2px black; font-variant: small-caps}
        body>div>.header>div {color: white; text-align: right; padding-right: 10px}
        body>div>.content {padding: 10px;}
        body>div>.content>.bar {height: 0; margin: 20px; border-bottom: 2px solid #600; box-shadow: 0 0 2px rgba(102,0,0,0.4)}
        body>div>.content table {width: 100%; border-collapse: collapse;}
        body>div>.content table.head-table td {text-align: center}
        body>div>.content table.head-table>thead>tr>td {color: #600; font-weight: bold}
        body>div>.content>h2 {color: #600; margin: 4px; text-align: center; font-size: 20px; font-variant: small-caps}
    </style>
</head>
<body>
    <div>
        <div class="header">
            <h1>ZombVival</h1>
            <div>System Report <?php echo date('d.m.Y', $time); ?></div>
        </div>


        <div class="content">
            <table class="head-table">
                <thead>
                    <tr>
                        <td style="width: 33%">Server</td>
                        <td style="width: 33%">Name</td>
                        <td style="width: 33%">Version</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $_SERVER['SERVER_ADDR']?></td>
                        <td><?= $_SERVER['SERVER_NAME']?></td>
                        <td><?=$version?></td>
                    </tr>
                </tbody>

            </table>

            <div class="bar"></div>

            <h2>Auto-Processing Log</h2>

            <?php if ($autoprc === null) { ?>
                Funktion deaktiviert.
                <br />
            <?php } elseif (!$autoprc) { ?>
                <br />
                Keine Spiele erfordern eine automatische Abwicklung
            <?php } else { ?>
                <br /><table>
                    <?php foreach ($autoprc as $id => $entry) { ?>
                        <tr>
                            <td><b>Spiel #</b><?php echo $id; ?></td>
                            <td style="padding-left: 8px;"><?php echo $entry; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>

            <div class="bar"></div>

            <h2>Garbage Collector Log</h2>
            <?php if ($garbage === null) { ?>
                Funktion deaktiviert.
                <br />
            <?php } elseif (!$garbage) { ?>
                <br />
                Keine verwaisten Elemente gefunden.
            <?php } else { ?>
                <br />
                <?php echo count($garbage) ?> verwaiste Container entfernt: <?php echo implode(', ', $garbage); ?>
            <?php } ?>

            <div class="bar"></div>

            <h2>Error Log</h2>

            <?php if ($errors) foreach ($errors as $date => $g1) { ?>
                <div style="padding: 3px; margin: 3px 3px 18px;">
                    <div style="font-size: 16px; background: rgb(102,0,0); color: white; padding: 3px 3px 3px 15px;"><?php echo $date; ?> - Exception Record</div>
                    <?php foreach ($g1 as $caption => $lines) { ?>
                        <b style="color: rgb(102,0,0); padding-left: 5px;"><?php echo $caption; ?></b><br />
                        <div style="font-family: monospace; padding-left: 20px">
                            <?php foreach ($lines as $line) { ?>
                                <?php echo $line; ?><br />
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                Keine Reports gefunden
            <?php } ?>

            <div class="bar"></div>

            <i><?php echo date('r'); ?></i>
        </div>


    </div>


</body>
</html>