<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Augments_Class2 extends Model_Items_Augments_Augment {

    protected static $static_info = Array(
        'name' => 'Mutiertes Organ',
        'icon' => 'organ_aug_2',
        'description' => 'Durch zusätzliche DNA wurde dieses Organ mutiert. Wenn du es einsetzt, verbessern sich deine Kampfeigenschaften.',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $num_aug_plus = 2;
    protected static $num_aug_sum_plus = 6;
}	