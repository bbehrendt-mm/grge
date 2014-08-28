<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Chainsaw extends Model_Battle_Weapon implements Interface_Countable {
	
	protected static $static_info = Array(
			'name' => 'Kettensäge Campbell B81 E-D',
			'icon' => 'chainsaw',
			'description' => 'Diese Kettensäge wurde von der Bundesprüfstelle für jugendgefährdende Schriften indiziert und kurz darauf auf Anordnung des Jugendamtes in allen Baumärkten beschlagnamt. Tja, die hochintelligenten, aufgeschlossenen und weitsichtigen Menschen, die dies beschlossen haben, hatten nun die Gelegenheit zu intensivem Körperkontakt mit Zombies. Wie wärs, wenn du dich über diese wirklich sehr sinnvolle Beschlagnamung hinwegsetzt und ein paar Schnittmengen einer Gruppe Zombies bildest?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FIGHT,
	);

	protected static $weight = 25;
	protected static $essential = true;
	
	protected static $capacity = 2;
	public static $range = Array(0,5);
	protected static $damage = Array(20,20);
	public static $damage_type = Model_Battle_Weapon::MBW_DMG_SCATTER;
	protected static $custom_icon = "gas";
	protected static $ammo = 'custom';
	public static $accuracy = 1;
	public static $accuracy_type = Model_Battle_Weapon::MBW_ACC_STATIC;
	public static $durability = 1;
	public static $bounce = 0;
	public static $reload_time = 0;
	public static $lock_type = Model_Battle_Weapon::MBW_LCK_WEAPON;
	public static $energy_cost = 5;
	
	public $fillrate = 0;

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Tank befüllen', Model_Action::factory()
                    ->requirement('Model_Items_Generic_Jerrycan', 1)
                    ->condition(function() use ($php53pb) {
                        /** @var Model_Items_Chainsaw $php53pb */
                        return $php53pb->fillrate < 10;
                    })
                    ->fail_message('Der Tank ist zu voll, als dass er einen weiteren Kanister Benzin aufnehmen könnte.')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function () use ($php53pb) {
                                /** @var Model_Items_Chainsaw $php53pb */
                                $php53pb->fillrate += 10;
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