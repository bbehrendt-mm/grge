<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Burnedhouse extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Verbranntes Haus';
	protected static $description = 'Du stehst im Garten einer Ruine, die wohl früher einmal ein wunderschönes Einfamilienhaus war. Durch die offenen Türen und Fenster erkennst du jedoch, dass das Haus im Inneren weniger stark durch den Brand beschädigt ist. Du könntest versuchen, hineinzugehen - aber sei vorsichtig, das Haus sieht nicht mehr allzu stabil aus ...';
    protected static $icon = 'burned';
    protected static $outside;

    protected static $auto_doorways = array('bhouse', 'thouse');
}	