<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Augments_CClass1 extends Model_Items_Augments_Augment {

    protected static $static_info = Array(
        'name' => 'Augmentiertes Organ (Infiziert)',
        'icon' => 'organ_aug_c1',
        'description' => 'Dieses Organ wurde biomechanisch augmentiert. Wenn du es einsetzt, verbessern sich deine Kampfeigenschaften - allerdings sieht es nicht ganz gesund aus...',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $num_aug_minus = 1;
    protected static $num_aug_sum_minus = 2;

}	