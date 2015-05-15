<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Hospital_Korridor extends Model_Places_Abstract_Trap implements Interface_Corridor {

    protected static $chance = 0.2;
	
	protected static $name = 'Stationskorridor';
	protected static $description = 'In diesem Flügel des Krankenhauses lagen die stationär aufgenommenen Patienten. Eine provisorische Barrikade blockierte den Eingang zu diesem Flügel des Krankenhauses - sie stellte jedoch genau wie die größtenteil bettlägrigen Patienten kein allzu großes Hindernis für die Zombiemassen dar.';
    protected static $outside = false;
    protected static $icon = 'default';
}