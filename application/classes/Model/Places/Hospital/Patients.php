<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Patients extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Patientenzimmer';
	protected static $description = 'Ein übliches Patientenzimmer... klein, muffig, ein billiger Fernseher an der Wand. Auf dessen Fernbedienungen ist die MDR-Taste ziemlich abgenutzt. Überall auf dem Boden und an den Wänden ist Blut. Alles in allem sieht es hier wie in jedem anderen Krankenhaus aus...';
    protected static $outside = false;
    protected static $icon = 'hospital_patients';

    public function uin($uin = NULL) {
        $t = parent::uin($uin);
        if ($uin !== null) {
            $num = random_int(0,4);
            for ($i = 0; $i < $num; $i++)
                $this->inventory->add(new Model_Items_Body());
        }
        return $t;
    }
}