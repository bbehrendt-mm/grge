<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Phone extends Model_Combat_Weapons_Throwable implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Windows Phone',
			'icon' => 'wphone',
			'description' => 'Wow, du hast ein Windows Phone gefunden - DAS Smartphone für Menschen, die sich auch einen Trabbi zum Preis eines Porsches andrehen lassen. Naja, mit diesem Teil kannst du zwar nicht wirklich angeben (oder ZombVival spielen), aber du kannst es zumindest auf einen Zombie werfen. Das ist doch auch was!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 4;

	protected static $damage = [5,15];
	protected static $range = [1,20];
	protected static $accuracy = 0.85;
	protected static $energy = 3;

    /**
     * @param Model_Combat_Actor $me
     * @param Model_Combat_Actor $opponent
     * @param number             $damage
     * @param Model_Combat_Scene $scene
     *
     * @return bool
     * @throws Exception
     */
	public function trigger_usage(Model_Combat_Actor $me, Model_Combat_Actor $opponent, $damage, Model_Combat_Scene $scene): bool {
		if ($this->registered_user)
			$this->registered_user->location()->inventory()->add( Tool_Gambling::random(0.1) ? new Model_Items_Generic_Electro2 : new Model_Items_Generic_Electro);
		return parent::trigger_usage($me, $opponent, $damage, $scene);
	}
}	