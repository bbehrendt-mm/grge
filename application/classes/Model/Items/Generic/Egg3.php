<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Egg3 extends Model_Items_Abstract_Easteregg implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Designer-Osterei',
			'icon' => 'eggs/ge',
			'description' => 'Du hast ein Designer-Osterei gefunden! Es ist aus schwarz glänzendem Marmor gefertigt und sieht sehr edel aus. Für so ein Teil müsste man auf einer Auktion Millionen hinblättern, dir hingegen fällt es einfach so vor die Füße.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);

    protected static $value = 5;

    public function take($silent = false) {
        global $player;

        if ($this->new)
            $player->achievements()->achieve(Model_Achievement::MA_EASTER);

        parent::take($silent);
    }
}	