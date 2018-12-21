<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Twinoid extends Model_Items_Abstract_Pillbox {
	
	protected static $static_info = Array(
			'name' => 'Schachtel mit Twinoid',
			'icon' => 'twinoid',
			'description' => 'Twinoid macht müde Verdammte munter! Nach einer Twinoi-Kapsel ströhmt neue Energie durch deinen Körper, die dich antreibt und deine Wunden heilt.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);

    protected static $take_msg = 'Direkt nachdem du die Twinoid schluckst fühlst du dich wieder besser!';
    protected static $singular_name = 'Twinoid';
    protected static $pill_effects = Array(
        Model_Status::MS_STAT_ENERGY => 5,
        Model_Status::MS_STAT_HEALTH => 5
    );
}	