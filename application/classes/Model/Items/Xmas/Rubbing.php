<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Xmas_Rubbing extends Model_Items_Abstract_Alcohol implements Interface_Event {

    protected static $static_info = Array(
        'name' => 'Reinigungsalkohol',
        'icon' => 'rubbing',
        'description' => 'Du könntest das trinken... wenn du wirklich sehr, sehr verzweifelt bist.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
    );

    protected static $weight = 5;
    protected static $alcohol = 150;
    protected static $energy = 0;
    protected static $thirst = 0;
    //protected static $additional_effects = [Model_Status::MS_STAT_FREEZE => -50];

}