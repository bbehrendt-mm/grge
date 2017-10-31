<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Augments_Class1 extends Model_Items_Augments_Augment {

    protected static $static_info = Array(
        'name' => 'Augmentiertes Organ',
        'icon' => 'organ_aug_1',
        'description' => 'Dieses Organ wurde biomechanisch augmentiert. Wenn du es einsetzt, verbessern sich deine Kampfeigenschaften.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $num_aug_plus = 1;
    protected static $num_aug_minus = 0;
    protected static $num_aug_sum_plus = 3;
    protected static $num_aug_sum_minus = 0;

}	