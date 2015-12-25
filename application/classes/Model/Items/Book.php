<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Book extends Model_Items_Abstract_Book {
	
	protected static $effects = array(
        Model_Status::MS_STAT_SLEEPY => 0.3,
        Model_Status::MS_STAT_ENERGY => 0.45
    );

    protected static $reading_speed = 2;
    protected static $pagerange = array(250,600);
    protected static $weight = 10;

    protected static $static_info = Array(
        'name' => 'Klassiker',
        'icon' => 'books/book',
        'description' => 'Diesen Klassiker der Literatur sollte man gelesen haben. Hast du das nicht, dann hol es gefälligst nach bevor du stirbst! Zeit genug hast du ja jetzt...',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_LITERATURE,
        'deco' => 2,
    );

}	