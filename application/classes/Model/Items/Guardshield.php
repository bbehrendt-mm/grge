<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Guardshield extends Model_Items_Abstract_Armor {
	
	protected static $static_info = Array(
			'name' => 'Wächterschild',
			'icon' => 'shieldg',
			'description' => 'Dieser Schild ist eine Spezialanfertigung für Wächter und praktisch unzerstörbar. Er kann alternativ auch für ein Captain America Cosplay verwendet werden.',
            'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
    );

    protected static $essential = true;
	protected static $weight = 5;

    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_SHIELD;

    protected static $protection = PHP_INT_MAX;
    protected $level = 1;

    public function __construct($stat_level) {
        $this->level = $stat_level;
        parent::__construct(null);
    }

    protected function get_effects(): array
    {
        return [
            min(0, -10 + 2 * $this->level), 0, 2 * $this->level, 0
        ];
    }

    public function can_drop(&$message, $p = null): bool {
        $message = 'Ohne deinen Schild fühlst du dich ziemlich nackt; hauptsächlich, weil du hinter dem Schild tatsächlich keine Kleidung trägst. Du solltest ihn also besser nicht ablegen...';
        return false;
    }

    public function drop_dead() {
        return null;
    }

    /**
     * @param number $damage
     */
    public function take_damage($damage): void
    {}
}	