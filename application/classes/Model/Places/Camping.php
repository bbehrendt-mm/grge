<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Camping extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Campingplatz';
	protected static $description = 'Wer sich kein Hotel leisten kann, auf Körperkontakt mit anderen Campern steht und keinerlei Ansprüche an Komfort stellt, der ist genau richtig auf dem Campingplatz. Einziges Problem: All diese Dinge treffen besonders gut auf Zombies zu...';
    protected static $icon = 'camping';

    protected static $auto_doorways = array('camping');
}	