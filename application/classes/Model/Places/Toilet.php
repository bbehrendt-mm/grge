<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Toilet extends Model_Places_Abstract_Place {
	
	protected static $name = 'Öffentliche Toiletten';
	protected static $description = 'Diese öffentlichen Toiletten sind im Prinzip das Hilton jedes Penners. Für andere Menschen sind diese Toiletten eher wie Paris Hilton: Ziemlich schmutzig, übler Geruch und die halbe Welt war schonmal drin.';
    protected static $icon = 'toilet';
    protected static $outside = false;
}	