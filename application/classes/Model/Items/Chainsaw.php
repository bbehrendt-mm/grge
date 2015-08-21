<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Chainsaw extends Model_Combat_Weapons_Fillable implements Interface_Countable {
	
	protected static $static_info = Array(
			'name' => 'Kettensäge Campbell B81 E-D',
			'icon' => 'chainsaw',
			'description' => 'Diese Kettensäge wurde von der Bundesprüfstelle für jugendgefährdende Schriften indiziert und kurz darauf auf Anordnung des Jugendamtes in allen Baumärkten beschlagnamt. Tja, die hochintelligenten, aufgeschlossenen und weitsichtigen Menschen, die dies beschlossen haben, hatten nun die Gelegenheit zu intensivem Körperkontakt mit Zombies. Wie wärs, wenn du dich über diese wirklich sehr sinnvolle Beschlagnamung hinwegsetzt und ein paar Schnittmengen einer Gruppe Zombies bildest?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 25;
	protected static $essential = true;

	protected static $animation = Model_Combat_Weapon::MCW_ANIMATION_CHAINSAW;

	protected static $capacity = 2;

	protected static $damage = [20,20];
	protected static $range = [0,5];
	protected static $accuracy = 1;
	protected static $use_fixed_accuracy = true;
	protected static $aoe = true;
	protected static $friendly_fire = false;
	public static $ammo_icon = 'items/gas';

	//public static $energy_cost = 5;

    protected function hid() {
        return parent::hid()
            ->add_action('Tank befüllen', Model_Action::factory()
                    ->requirement('Model_Items_Generic_Jerrycan', 1)
                    ->condition(function() {
                        return $this->fillrate < 10;
                    })
                    ->fail_message('Der Tank ist zu voll, als dass er einen weiteren Kanister Benzin aufnehmen könnte.')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function () {
                                $this->fillrate += 10;
                            })
                            ->message('Du hast die Kettensäge mit Benzin aufgefüllt, jetzt schnurrt sie wie ein (tödliches) Kätzchen. Zombies und böse Dämonen haben keine Chance mehr - Groovy!')
                    )
            );
    }
	
	public function has_ammo() {
		return ($this->fillrate > 0);
	}
	
	public function consume_ammo() {
		$this->fillrate--;
	}
	
	public function count() {
		return $this->fillrate;
	}
}	