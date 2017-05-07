<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Equipable extends Model_Items_Abstract_Item {

    const MIAE_ARMOR_HELMET = 1;
    const MIAE_ARMOR_BODY = 2;
    const MIAE_ARMOR_SHIELD = 3;
    const MIAE_ARMOR_CAPE = 4;
    const MIAE_WEAPON = 5;

    const MIAE_STAT_INI = 1;
    const MIAE_STAT_ATK = 2;
    const MIAE_STAT_DEF = 3;
    const MIAE_STAT_ACC = 4;

    protected static $allow_multi_equip = false;
    protected static $allow_primary_equip = false;

	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_GEAR;

    protected static $equipment_type = 0;
    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,0,0];
    protected $equipped;
    protected $equipped_primary;
    protected $player_id = null;

    public function equip($player = null) {
        /** @global Model_Player $player */
        if ($player === null)
            $player = Globals::CurrentPlayer();

        if (Tool_Scripts::is_npc($player))
            return;

        if (!static::$allow_multi_equip)
            foreach ($player->get_equipment($this->get_equipment_type()) as $item)
                $item->unequip();

        $this->equipped = true;
        $this->player_id = $player->id();

        if (static::$allow_primary_equip)
            foreach ($player->get_equipment($this->get_equipment_type()) as $item)
                if ($item->equipped_primary)
                    return;

        $this->equip_primary($player);
    }

    public static function convertStringType() {
        switch (static::$equipment_type) {
            case static::MIAE_ARMOR_BODY: return 'Rüstung';
            case static::MIAE_ARMOR_HELMET: return 'Helm';
            case static::MIAE_ARMOR_SHIELD: return 'Schild';
            case static::MIAE_ARMOR_CAPE: return 'Umhang';
            case static::MIAE_WEAPON: return 'Waffe';
            default: return 'Unbekannt';
        }
    }

    public function allows_primary() {
        return static::$allow_primary_equip;
    }

    public function is_equipped_primary() {
        return static::$allow_primary_equip ? $this->equipped_primary : true;
    }

    public function get_equipment_type() {
        return static::$equipment_type;
    }
    
    protected function get_effects() {
        return static::$effects;
    }

    public function get_stats($type = null) {
        if ($type === true)
            return [
                static::MIAE_STAT_INI => $this->get_stats(static::MIAE_STAT_INI),
                static::MIAE_STAT_ATK => $this->get_stats(static::MIAE_STAT_ATK),
                static::MIAE_STAT_DEF => $this->get_stats(static::MIAE_STAT_DEF),
                static::MIAE_STAT_ACC => $this->get_stats(static::MIAE_STAT_ACC),
            ];
        else return $type === null ? $this->get_effects() : $this->get_effects()[$type - 1];
    }

    public function unequip() {
        $rebuild = $this->is_equipped() && $this->allows_primary() && $this->is_equipped_primary();

        $this->equipped = false;
        $this->equipped_primary = false;
        $this->player_id = null;

        if ($rebuild)
            Tool_Scripts::rebuild_primary_equipment($this->get_equipment_type());
    }

    public function consume() {
        $this->unequip();
        parent::consume();
    }

    public function grind() {
        $this->unequip();
        parent::grind();
    }

    public function is_equipped() {
        return $this->equipped;
    }

    public function drop_dead() {
        if ($this->is_equipped())
            $this->unequip();
        return parent::drop_dead();
    }

    public function drop($p = null, $silent = false) {
        $r = parent::drop($p, $silent);
        if ($r && $this->is_equipped())
            $this->unequip();
        return $r;
    }

    public function equip_primary($player = null) {
        if (!static::$allow_primary_equip)
            return;

        /** @global Model_Player $player */
        if ($player === null)
            $player = Globals::CurrentPlayer();

        if (Tool_Scripts::is_npc($player))
            return;

        foreach ($player->get_equipment($this->get_equipment_type(), true) as $item)
            $item->equipped_primary = false;

        $this->equipped_primary = true;
    }
}	