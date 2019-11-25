<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Temperature extends Model_Buffs_Abstract_Buff {

    protected static $name = 'Körpertemperatur';
    protected static $desc = 'Zombies ist Kälte egal - dir leider nicht. Wenn du dich an kalten Orten aufhältst, wird deine Körpertemperatur langsam sinken. Denke daran, dich regelmäßig aufzuwärmen und dir ausreichend dicke Klamotten anzuziehen.';
    protected static $icon = 'temperature';
	protected static $bid = 'temperature';
    protected static $remotable = true;
    protected static $visible = true;

    protected $calculated_temperature = 0.0;

    protected $target_temperature = 36.0;
    protected $temperature_gain   = 21.0;

    public function get_body_temperature(): float {
        return $this->target_temperature;
    }

    public function get_surrounding_temperature(): ?float {
        if (!$this->associated()) return null;
        return $this->assoc_player->location()->get_temperature();
    }

    public function get_fire_temperature(): float {
        if (!$this->associated()) return 0.0;

        if ($this->assoc_player->location()->inventory()->get(Model_Items_Generic_Fire2::cls()))
            return $this->assoc_player->location()->is_outside() ? 30 : 50;
        if ($this->assoc_player->location()->inventory()->get(Model_Items_Generic_Fire::cls()))
            return $this->assoc_player->location()->is_outside() ? 15 : 30;

        return 0.0;
    }

    public function get_temperature_gain(): float {
        $default_gain = $this->temperature_gain;
        if (!$this->associated()) return $default_gain;

        $factor = 1.0;
        $hunger = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_HUNGER);
        $drunk  = $this->assoc_player->get_status()->get(Model_Status::MS_STAT_DRUNK);
        $factor += max(0, $hunger - 90) / 100;
        $factor += min(0, $hunger - 50) / 50;
        $factor += min(0.2, ($drunk / 50.0) * 0.2);

        /** @var Model_Buffs_Sleep|null $sleep_buff */
        $sleep_buff = $this->assoc_player->get_status()->retrieve('sleep_cozy');
        if ($sleep_buff !== null) switch ($sleep_buff->get_level()) {
            case 0: $factor -= 0.75; break;
            case 1: $factor -= 0.25; break;
            case 2: $factor -= 0.00; break;
            case 3: $factor += 0.50; break;
            default: break;
        }
        if ($this->assoc_player->get_status()->retrieve('fragile/sleep_drunk'))
            $factor -= 0.95;

        return max(0, $default_gain * $factor);
    }

    public function get_absolute_isolation(): float {
        if (!$this->associated_to_player()) return 0.0;

        $t = 0.0;
        foreach ($this->assoc_player->get_equipment() as $eq)
            /** @var Model_Items_Abstract_Armor $eq */
            if (Tool_System::instance_of($eq, Model_Items_Abstract_Armor::cls()))
                $t += $eq->getAbsoluteTemperatureIsoloation();
        return $t;
    }

    public function get_relative_isolation(): float {
        if (!$this->associated_to_player()) return 1.0;

        $t = 1.0;
        foreach ($this->assoc_player->get_equipment() as $eq)
            /** @var Model_Items_Abstract_Armor $eq */
            if (Tool_System::instance_of($eq, Model_Items_Abstract_Armor::cls()))
                $t += $eq->getRelativeTemperatureIsoloation();
        return $t;
    }

    public function calc_temperature_gradient(): float {
        $s = $this->get_surrounding_temperature();
        if ($s === null) return 0;
        $s += $this->get_fire_temperature();
        $dif = ($s - $this->target_temperature) + $this->get_temperature_gain() + $this->get_absolute_isolation();
        $iso = $this->get_relative_isolation();

        if ($dif < 0) $dif /= $iso;
        else $dif *= $iso;
        return $dif;
    }

    /**
     * Recalculates the effects of this buff
     *
     * @return bool
     */
    public function rebuild(): bool {
        $this->calculated_temperature = $this->calc_temperature_gradient();
        return parent::rebuild();
    }

    protected function get_effects(): array {
        return Array(
            Model_Status::MS_STAT_FREEZE => Array(
                Model_Buffs_Abstract_Buff::MB_RAISE_ACC => -1 * min(0,$this->calculated_temperature / 10.0),
                Model_Buffs_Abstract_Buff::MB_DROP_ACC => max(0,$this->calculated_temperature / 10.0),
                Model_Buffs_Abstract_Buff::MB_RAISE_PRC => 0,
                Model_Buffs_Abstract_Buff::MB_DROP_PRC => 0,
            )
        );
    }

    /**
     * Creates and applies the buff; if $player_id is not provided, the currently active player will be selected
     * @param Interface_Plentity|number|null $association
     * @param int|number $lifetime Buff lifetime; omit or set smaller than 0 to disable auto-unbuff based on lifetime
     * @throws Exception
     */
    public function __construct($association = NULL, $lifetime = -1) {
        parent::__construct($association, $lifetime);

        if ($association !== null) {
            if (Tool_System::instance_of($association, Model_NPC_Event_Rudolph::cls())) $this->temperature_gain = 35.0;
        }
    }

}
