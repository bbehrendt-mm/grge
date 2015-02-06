<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Log_Types_Battle_Atk extends Model_Log_Message {
	
	private $human_attacker;
	private $atk_name, $atk_count;
	private $def_name, $def_count;
	/** @var string|Model_Battle_Weapon $weapon */
	private $weapon;
	/** @var null|string|Model_Items_Abstract_Item $protection  */
    private $protection = null;
    private $cover = array();
    private $prot_value = 0;
	private $damage, $kills;
	private $note;
    private $spa;
    private $spd;

	const MLTBA_ATTACK_MISSED = 1;
	const MLTBA_WEAPON_DESTROYED = 2;
	const MLTBA_COVER_DESTROYED = 3;
	const MLTBA_ARMOR_DESTROYED = 4;

	public function __construct() {}

    /**
     * Creates an attack summary to use in battles
     * @param Model_Battle_Combatant $atk
     * @param Model_Battle_Combatant $def
     * @param Model_Battle_Weapon $weapon
     * @param int $damage
     * @param int $kills
     * @param null|array $note
     * @param Model_Items_Abstract_Armor $protection
     * @param Model_Items_Abstract_Armor[] $cover
     * @param int $prot_value
     */
	public function update($atk, $def, $weapon, $damage, $kills, $note = null, $protection = null, $cover = array(), $prot_value = 0) {
		$this->human_attacker = !$atk->is_zombie();
		$this->atk_name = $atk->name();
		$this->atk_count = $atk->count();
        $this->spa = $atk->special();
		$this->def_name = $def->name();
		$this->def_count = $def->count();
        $this->spd = $def->special();
		
		$this->weapon = is_string($weapon) ? $weapon : get_class($weapon);
		$this->damage = $damage;
		$this->kills = $kills;
		$this->note = $note;

        $this->protection = ($protection !== null) ? [get_class($protection),!$protection->is_destroyed()] : null;
        foreach ($cover as $elem)
			$this->cover[$elem->uin()] = [get_class($elem),!$elem->is_destroyed()];
        $this->prot_value = $prot_value;
	}

	protected function postprocess($data) {
		if (is_string($this->note))
			$this->note = [];

		$weapon = $this->weapon;
		$ammo = [];
		$weapon_destroyed = false;
		foreach ($weapon::ammo() as $a => $c) {
			/** @var Model_Items_Abstract_Item|string $a */
			if ($c === 'self') $weapon_destroyed = true;
			elseif ($c === 'custom') $ammo[] = $weapon::custom_ammo_icon();
			else for ($i = 0; $i < $c; $i++) $ammo[] = $a::static_icon();
		}

		$covers = [];
		$protection = [];
		if ($this->prot_value > 0) {
			foreach ($this->cover as $id => $a) {
				/** @var Model_Items_Abstract_Armor $item */
				list($item, $stable) = $a;
				$covers[] = [
					'name' => __($item::static_name()),
					'icon' => $item::static_icon(),
					'stable' => $stable
				];
			}

			if ($this->protection) {
				/** @var Model_Items_Abstract_Armor $item */
				$item = $this->protection[0];
				$protection[] = [
					'name' => __($item::static_name()),
					'icon' => $item::static_icon(),
					'stable' => $this->protection[1]
				];
			}
		}

		return [
			'type' => Model_Log_Message::MLM_BATTLE_ATTACK,
			'attacker' => [
				'is_zombie' => !$this->human_attacker,
				'count' => $this->human_attacker ? 1 : $this->atk_count,
				'name' => ($this->spa || $this->human_attacker) ? $this->atk_name : __($this->atk_name),
			],
			'defender' => [
				'is_zombie' => $this->human_attacker,
				'count' => !$this->human_attacker ? 1 : ($this->def_count + $this->kills),
				'name' => ($this->spd || !$this->human_attacker) ? $this->def_name : __($this->def_name),
			],
			'weapon' => [
				'name' => __($weapon::static_name()),
				'icon' => $weapon::static_icon(),
				'energy' => $weapon::$energy_cost,
				'destroyed' => $weapon_destroyed || in_array(static::MLTBA_WEAPON_DESTROYED,$this->note),
				'ammo' => $ammo,
			],
			'protection' => [
				'value' => $this->prot_value,
				'covers' => $covers,
				'armor' => $protection,
				'cover_destroyed' => in_array(static::MLTBA_COVER_DESTROYED,$this->note),
				'armor_destroyed' => in_array(static::MLTBA_ARMOR_DESTROYED,$this->note),
			],
			'damage' => $this->damage + $this->prot_value,
			'kills' => $this->kills,
			'missed' => in_array(static::MLTBA_ATTACK_MISSED,$this->note)
		];
	}
}