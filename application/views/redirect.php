<?php
    /**
     * @var string $url
     * @var string|null $path
     * @var array|null $others
     */
?>
<html>
<head>
    <!-- Meta -->
    <meta content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="de">
    <meta name="robots" content="noindex,nofollow" />
    <meta name="keywords" content="Zombie,Survival,Browsergame,Die Verdammten,Die2Nite,GRGE">
    <meta name="description" content="Ein Single Player Survival Game. Könnte Spuren von Zombies enthalten..." />
    <meta name="author" content="Benjamin 'Brainbox' Behrendt" />

    <!-- Basics -->
    <title>ZombVival Evolved!</title>
</head>
<body>
    Redirecting ...

    <form action="<?=$url?>" method="<?=empty($others) ? 'get' : 'post'?>" style="display: none">
        <?php if ($path !== null) { ?>
            <input type="hidden" name="r" value="<?=$path?>">
        <?php } else if (!empty($others)) foreach ($others as $key => $val) if ($val !== null) { ?>
            <input type="hidden" name="<?=$key?>" value="<?=htmlspecialchars($val)?>">
        <?php } ?>
    </form>

    <script type="text/javascript">
    // ## JS COMPRESS BEGIN ## //
        document.forms[0].submit();
    // ## JS COMPRESS END ## //
    </script>
</body>
</html>