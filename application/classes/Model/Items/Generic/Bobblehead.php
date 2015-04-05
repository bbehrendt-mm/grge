<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bobblehead extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Han Solo Wackelkopf-Figur',
        'icon' => 'bobblehead',
        'description' => 'Dies ist die originale Han Solo Wackelkopf-Figur, die im Film als Stund-Double von Harrison Ford eingesetzt wurde. Hätte es die nicht gegeben, dann hätte Han ja zuerst schießen müssen!',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
        'deco' => 2,
    );

	protected static $weight = 3;
}	