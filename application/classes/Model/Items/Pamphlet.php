<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Pamphlet extends Model_Items_Abstract_Book {
	
	protected static $effects = array(
        Model_Status::MS_STAT_SLEEPY => -0.05,
        Model_Status::MS_STAT_ENERGY => 0.2
    );

    protected static $reading_speed = 1;
    protected static $pagerange = array(66,99);
    protected static $weight = 6;

    protected static $static_info = Array(
        'name' => 'Religiöse Schrift',
        'icon' => 'books/bible',
        'description' => 'Dir ist langweilig und du würdest dich gerne uneingeladen in jemandes Leben einmischen, weißt aber nicht in wessen? Keine Sorge, mithilfe dieser Schrift wirst du Rechtfertigungen finden um gegen alle möglichen Minderheiten zu hetzen, um von deinem eigenen, völlig verkorksten Leben abzulenken.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_LITERATURE,
        'deco' => 1,
    );

}	