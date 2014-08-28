<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Mistletoe extends Model_Items_Abstract_Item implements Interface_Static {

    protected static $static_info = Array(
        'name' => 'Mistelzweig',
        'icon' => 'mistletoe',
        'description' => 'Willst du wirklich hier einen Mistelzweig aufhängen? Schau dir doch mal an, wer hier alles rumläuft... willst du wirklich einen von denen küssen müssen?',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_RES,
    );

	protected static $weight = 15;
}	