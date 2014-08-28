<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital extends Model_Places_Abstract_Place {
	
	protected static $name = 'Außenbereich des Krankenhauses';
	protected static $description = 'Kurz nach dem Ausbruch der Zombieseuche wurden in diesem Krankenhaus die ersten Opfer behandelt. Die hier ansässigen Ärzte konnten jedoch wenig mehr tun als der Seuche bei ihrer Ausbreitung zuzusehen. Du hast Glück, das Krankenhaus wurde bisher noch nicht geplündert - hier wirst du also noch allerhand hilfreicher Dinge finden können. Warum das Krankenhaus noch nicht geplündert wurde? Na, weil es darin vor Zombies nur so wimmelt natürlich!';
    protected static $icon = 'hospital';
    protected static $outside = true;

    protected static $auto_doorways = array('hospital');
}	