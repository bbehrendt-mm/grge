<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Augment2 extends Model_Items_Augments_Augment {

    protected static $static_info = Array(
        'name' => 'Dämonisches Organ',
        'icon' => 'hw19_aug2',
        'description' => 'Hast du dich nicht immer schon mal gefragt, was passieren würde, wenn deine Vena Cava Superior von einem Dämon besessen wäre?',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $num_aug_plus = 3;
    protected static $num_aug_minus = 1;
    protected static $num_aug_sum_plus = 10;
    protected static $num_aug_sum_minus = 3;

}	