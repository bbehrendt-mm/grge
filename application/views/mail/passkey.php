<?php
    /**
     * @var string $username
     * @var string $key
     */
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background: #1a191f; }
        body > div:first-child { background: #fff6bf; border-radius: 10px; padding: 30px; margin: 15px; }
        h1 { color: #940202; margin: 0; }
        .sc_key { font-size: 32px; font-weight: bold; }
        .sc_footer { color: #555555; font-size: 11px; }
    </style>
</head>
<body>
    <div>
        <h1>Zombvival</h1>
        <h2><?=__('E-Mail Adresse bestätigen')?></h2>

        <p class="sc_intro">
            <?=__('Hallo ::b:::user::/b::', [':user' => $username])?>,<br/>
            <?=__('um die Registrierung deiner E-Mail Adresse abzuschließen, gib bitte folgenden Code auf ZombVival ein.')?>
        </p>
        <p class="sc_key">
            <?=$key?>
        </p>

        <p class="sc_footer">
            <?=__('Wenn du diese E-Mail erhalten hast, ohne dich auf ZombVival zu registrieren, ignoriere sie bitte.')?><br/><br/>
            <?=__('Dies ist eine automatisch generierte E-Mail. Bitte antworte nicht auf sie.')?>
        </p>
    </div>
</body>

</html>