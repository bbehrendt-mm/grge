<?php
    error_reporting(0);
    $users = [
        'Brainbox' => '7a94f46dd33f9d711f8da5657dbebe8cd948514b3b30304fa2387f9715d8a075',
    ]
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

        <?php
            if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($users[$_SERVER['PHP_AUTH_USER']]) || $users[$_SERVER['PHP_AUTH_USER']] !== hash('sha256',$_SERVER['PHP_AUTH_PW'])) {
                header('WWW-Authenticate: Basic realm="ZombVival System Installer"');
                header('HTTP/1.0 401 Unauthorized');
                echo '<b>Authentication failed!</b>';
                exit;
            } else { ?>

                <?php if (isset($_POST['user']) && isset($_POST['name'])) {

                    function line($tr) {echo htmlentities($tr) . '<br />'; return false;}
                    function flat($fetch) {return array_map(function($a) {return $a[0];},$fetch);}

                    /**
                     * @param mysqli $mysqli
                     * @param $queries
                     * @param bool $drop_results
                     * @return bool|mysqli_result
                     */
                    function db(&$mysqli, $queries, $drop_results = true) {
                        if (!$mysqli->multi_query($queries))
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
                        if (!db($mysqli, file_get_contents('create.sql'))) return false;
                        line('Basic structure has been set up.');

                        line('Checking for old profile data...');

                        if ($result = db($mysqli, 'DESCRIBE grg_users;', false)) {
                            $struct = flat($result->fetch_all());
                            if (!in_array('avatar', $struct)) {
                                line('AVATAR data missing. Creating...');
                                if (!db($mysqli, 'ALTER TABLE grg_users ADD avatar VARCHAR(511) NULL DEFAULT NULL AFTER name;')) return false;
                                line('AVATAR data created.');
                            }

                            if (in_array('ban', $struct)) {
                                line('BANN data found in profile. Moving to USER FLAGS...');
                                if (!db($mysqli, file_get_contents('move_ban_s7.sql'))) return false;
                                line('BANN data moved.');
                            }

                            if (in_array('origin', $struct) && in_array('mtid', $struct)) {
                                line('AUTH data found in profile. Moving to AUTHENTICATION XREF...');
                                if (!db($mysqli, file_get_contents('move_profiles_s7.sql'))) return false;
                                line('AUTH data moved.');
                            }

                            if (in_array('dataset', $struct)) {
                                line('Depricated DATASET blob found in profile. Removing...');
                                if (!db($mysqli, 'ALTER TABLE grg_users DROP dataset')) return false;
                                line('DATASET blob removed.');
                            }

                        } else return false;
                        line('Profile data is up to date.');

                        return true;
                    };

                    update();

                } else { ?>

                    <form method="post" action="index.php">
                        <b>Please enter database credentials.</b><br />
                        <input name="user" type="text" placeholder="Database Username" style="width: 400px;" /> <input name="pass" type="password" placeholder="Database Password" style="width: 400px;" /><br /><br />
                        <input name="name" type="text" placeholder="Database Name" style="width: 400px;" /><br /><br />
                        <button type="submit">Confirm</button>
                    </form>

                <?php } ?>

            <?php } ?>
    </div>
</body>
</html>