<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Northpole_Home extends Model_Places_Home {

    protected static $location_name = 'Verschneites Versteck';
    protected static $description = 'Eigentlich war es eine gute Idee - zum Nordpol fliehen, die Zombieapokalypse aussitzen, danach zurückkehren und die Erde neu bevölkern. Leider hast du vergessen, dein Flugzeug vollzutanken sowie Vorräte und einen für die Wiederbevölkerung geeigneten Sexualpartner mitzunehmen. Tja, und nun sitzt du hier fest ...';
    protected static $icon = 'np_home';

    protected static $temperature_engine = 2;
    protected $temperature_scale     = 0.06;
    protected $temperature_deisolation = 0.15;
}	