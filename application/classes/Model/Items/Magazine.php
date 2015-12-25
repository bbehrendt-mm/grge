<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Magazine extends Model_Items_Abstract_Book {
	
	protected static $effects = array(
        Model_Status::MS_STAT_SLEEPY => 0.1,
        Model_Status::MS_STAT_ENERGY => 0.3
    );

    protected static $reading_speed = 4;
    protected static $pagerange = array(40,80);
    protected static $weight = 4;

    protected static $static_info = Array(
        'name' => 'Klatschmagazin',
        'description' => 'Du brauchst die absolut neusten, heissesten und erfundensten Infos darüber, wer gerade mit wem zusammen ist, wen betrügt, in wessen Film mitspielt, mit wem auf einer Koksparty gesichtet wurde oder wessen grausam verbrannten Körper heimlich nachts im Wald verscharrt hat? All dies und noch viel mehr findest du in Manifest schlechten Geschmacks, das selbst für Hitler zu unmenschlich wäre.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_LITERATURE,
    );

    protected static $instances_info = Array(
        Array('icon' => 'books/mag1'),
        Array('icon' => 'books/mag2'),
        Array('icon' => 'books/mag3'),
        Array('icon' => 'books/mag4'),
        Array('icon' => 'books/mag5'),
        Array('icon' => 'books/mag6'),
        Array('icon' => 'books/mag7'),
        Array('icon' => 'books/mag8'),
        Array('icon' => 'books/mag9'),
        Array('icon' => 'books/mag10'),
    );
	
}	