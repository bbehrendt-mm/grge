<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Booklet extends Model_Items_Abstract_Book {
	
	protected static $effects = array(
        Model_Player::MP_STAT_SLEEPY => -0.3
    );

    protected static $reading_speed = 3;
    protected static $pagerange = array(3,12);
    protected static $weight = 2;

    protected static $static_info = Array(
        'icon' => 'books/booklet',
        'description' => 'Dieses kleine Heftchen beschreibt die komplexe Funktionsweise eines Haushaltsgegenstands in 6 verschiedenen Sprachen - eine davon ist chinesisch, der Rest Kauderwelsch.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_LITERATURE,
    );

    protected static $instances_info = Array(
        Array('name' => 'Gebrauchsanleitung für einen Kühlschrank'),
        Array('name' => 'Gebrauchsanleitung für eine Waschmaschine'),
        Array('name' => 'Gebrauchsanleitung für einen Wäschetrockner'),
        Array('name' => 'Gebrauchsanleitung für eine HiFi-Anlage'),
        Array('name' => 'Gebrauchsanleitung für ein Mobiltelefon'),
        Array('name' => 'Gebrauchsanleitung für einen Vibrator'),
        Array('name' => 'Gebrauchsanleitung für einen Großen Hadronen-Speicherring'),
    );
	
}	