<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Augments_Cclass2 extends Model_Items_Augments_Augment {

    protected static $static_info = Array(
        'name' => 'Mutiertes Organ (Infiziert)',
        'icon' => 'organ_aug_c2',
        'description' => 'Durch zusätzliche DNA wurde dieses Organ mutiert. Wenn du es einsetzt, verbessern sich deine Kampfeigenschaften - allerdings sieht es nicht ganz gesund aus...',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $num_aug_plus = 2;
    protected static $num_aug_minus = 2;
    protected static $num_aug_sum_plus = 10;
    protected static $num_aug_sum_minus = 4;

}	