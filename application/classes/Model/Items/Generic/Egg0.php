<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Egg0 extends Model_Items_Abstract_Easteregg implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Kaputtes Ei',
			'icon' => 'eggs/e8',
			'description' => 'Nicht nur, dass dieses Ei kaputt ist, es ist auch noch nicht einmal gefärbt. Tja, da hast du wohl eine Niete gezogen...',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_EVENT,
	);

    public function count() {
        return 0;
    }

    public function take(): bool {
        $new = $this->new;
        if (!parent::take()) return false;

        if ($new && !Globals::shadowPlayerExists())
            Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_EASTER_BAD);

        return true;
    }
}	