<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Augments_Augment extends Model_Items_Abstract_Equipable {

	protected static $weight = 5;

    // INI, ATK, DEF, ACC
    protected $custom_effects = [0,0,0,0];
    protected static $equipment_type = Model_Items_Abstract_Equipable::MIAE_ARMOR_ORGAN;

    protected static $num_aug_plus = 1;
    protected static $num_aug_minus = 0;
    protected static $num_aug_sum_plus = 5;
    protected static $num_aug_sum_minus = 0;


    /**
     * Item constructor
     * Will randomly select a subtype if subtypes are defined for this item class
     *
     * @param null $type
     *
     * @throws Exception
     */
    public function __construct($type = null) {
        parent::__construct($type);

        $list = [0,1,2,3];
        shuffle($list);
        $p = 0;

        $na = static::$num_aug_sum_plus;
        $nb = static::$num_aug_sum_minus;
        for ($i = 0; $i < static::$num_aug_plus; $i++) {
            $n = ($i == (static::$num_aug_plus-1)) ? $na : random_int(1,$na - (static::$num_aug_plus - ($i+1)));
            $na -= $n;

            $this->custom_effects[$list[$p++]] = $n;
        }
        for ($i = 0; $i < static::$num_aug_minus; $i++) {
            $n = ($i == (static::$num_aug_minus-1)) ? $nb : random_int(1,$nb - (static::$num_aug_minus - ($i+1)));
            $nb -= $n;

            $this->custom_effects[$list[$p++]] = -$n;
        }
    }

    public function drop_dead() {
        if ($this->is_equipped()) {
            $this->unequip();
            return null;
        } else return parent::drop_dead();
    }

    protected function get_effects() {
        return $this->custom_effects;
    }
}	