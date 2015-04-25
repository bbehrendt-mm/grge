<?php
    //Redirect
    defined('INDEX_CALL') or die('Install');
?>
<html>
<head>
    <!-- Meta -->
    <meta content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="de">
    <meta name="robots" content="index,nofollow" />
</head>
<body style="background: #544e51">

    <div style="background: #f4f4f4; margin: 30px; padding: 30px;">
        <h2>ZombVival Database Installer</h2>

        <p>WARNING: This tool may unleash zombies in your database!</p>


        <?php if (isset($_POST['user']) && isset($_POST['name']) && isset($_POST['prefix'])) {

            global $prefix;
            $prefix = $_POST['prefix'];

            function line($tr) {echo htmlentities($tr) . '<br />'; return false;}
            function flat($fetch) {return array_map(function($a) {return $a[0];},$fetch);}

            /**
             * @param mysqli $mysqli
             * @param $queries
             * @param bool $drop_results
             * @return bool|mysqli_result
             */
            function db(&$mysqli, $queries, $drop_results = true) {
                global $prefix;
                if (!$mysqli->multi_query(str_replace('___PREFIX___', $prefix ,$queries)))
                    return line('Database error! Error code: ' . $mysqli->errno . ' (' . $mysqli->error . ')');

                if ($drop_results) {
                    do {if ($res = $mysqli->store_result()){$res ->close();}}
                    while ($mysqli->next_result());
                    return true;
                } else return $mysqli->store_result();
            }

            function update() {
                line('Establishing database connection ...');
                $mysqli = new mysqli('localhost', $_POST['user'], $_POST['pass'], $_POST['name']);
                if ($mysqli->connect_errno)
                    return line('Connection failed! Error code: ' . $mysqli->connect_errno . ' (' . $mysqli->connect_error . ')');
                else line('Connected');

                line('Creating structures...');
                if (!db($mysqli, file_get_contents('install/create.sql'))) return false;
                line('Basic structure has been set up.');

                line('Checking for old profile data...');

                if ($result = db($mysqli, 'DESCRIBE ___PREFIX___users;', false)) {
                    
					$struct = [];
					while ($struct[] = $result->fetch_array());

					$struct = flat($struct);
                    if (!in_array('avatar', $struct)) {
                        line('AVATAR data missing. Creating...');
                        if (!db($mysqli, 'ALTER TABLE ___PREFIX___users ADD avatar VARCHAR(511) NULL DEFAULT NULL AFTER name;')) return false;
                        line('AVATAR data created.');
                    }

                    if (in_array('ban', $struct)) {
                        line('BANN data found in profile. Moving to USER FLAGS...');
                        if (!db($mysqli, file_get_contents('install/move_ban_s7.sql'))) return false;
                        line('BANN data moved.');
                    }

                    if (in_array('origin', $struct) && in_array('mtid', $struct)) {
                        line('AUTH data found in profile. Moving to AUTHENTICATION XREF...');
                        if (!db($mysqli, file_get_contents('install/move_profiles_s7.sql'))) return false;
                        line('AUTH data moved.');
                    }

                    if (in_array('dataset', $struct)) {
                        line('Depricated DATASET blob found in profile. Removing...');
                        if (!db($mysqli, 'ALTER TABLE ___PREFIX___users DROP dataset')) return false;
                        line('DATASET blob removed.');
                    }

                } else return false;
                line('Profile data is up to date.');

                return true;
            };

            update();

        } else { ?>

            <form method="post" action="index.php?gw=i">
                <b>Please enter database credentials.</b><br />

                <?php
                    $p_user = ''; $p_pass = ''; $p_name = ''; $p_prefix = '';
                    if (file_exists('modules/database/config/database.php')) {
                        define('SYSPATH', true);
                        $cfg = (include 'modules/database/config/database.php');

                        if (isset($cfg['default']) && isset($cfg['default']['type']) && $cfg['default']['type'] === 'MySQL' && isset($cfg['default']['connection'])) {
                            if (isset($cfg['default']['connection']['username'])) $p_user = $cfg['default']['connection']['username'];
                            if (isset($cfg['default']['connection']['password'])) $p_pass = $cfg['default']['connection']['password'];
                            if (isset($cfg['default']['connection']['database'])) $p_name = $cfg['default']['connection']['database'];

                            if (isset($cfg['default']['table_prefix'])) $p_prefix = $cfg['default']['table_prefix'];
                        }
                    }
                ?>

                <input name="user" type="text" value="<?=$p_user?>" placeholder="Database Username" style="width: 400px;" /> <input name="pass" type="password" value="<?=$p_pass?>" placeholder="Database Password" style="width: 400px;" /><br /><br />
                <input name="name" type="text" value="<?=$p_name?>" placeholder="Database Name" style="width: 400px;" /> <input name="prefix" type="text" value="<?=$p_prefix?>" placeholder="Table Prefix" style="width: 400px;" /><br /><br />
                <button type="submit">Confirm</button>
            </form>

        <?php } ?>
    </div>
</body>
</html>