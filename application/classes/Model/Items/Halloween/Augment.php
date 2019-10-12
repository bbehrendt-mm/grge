<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Halloween_Augment extends Model_Items_Augments_Augment {

    protected static $static_info = Array(
        'name' => 'Verdorbenes Organ',
        'icon' => 'hw19_aug1',
        'description' => 'Wenn du noch nicht bereit bist, deinen kompletten Körper den dunklen Mächten zu opfern, fang doch einfach erst einmal mit einzelnen Organen an!',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $num_aug_plus = 2;
    protected static $num_aug_minus = 1;
    protected static $num_aug_sum_plus = 6;
    protected static $num_aug_sum_minus = 1;

}	