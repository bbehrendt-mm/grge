<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Armor extends Model_Items_Abstract_Item {
	
	const MIAA_HELMET = 1;
    const MIAA_BODY = 2;

    const MIAA_SHIELD = 4;
    const MIAA_CAPE = 8;

    const MIAA_FULL = 3;

	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_GEAR;
    protected $active = false;
    protected static $atype = 0;
    protected static $acover = 0;
    protected $protection = 1;
    protected static $damage_reduction = 0;
    protected static $damage_blocking = 0;
    protected static $destroyed = null;

    public static function convertStringType() {
        $ret = array();
        if (static::$atype & static::MIAA_BODY) $ret[] = __('Rüstung');
        if (static::$atype & static::MIAA_HELMET) $ret[] = __('Helm');
        if (static::$atype & static::MIAA_SHIELD) $ret[] = __('Schild');
        if (static::$atype & static::MIAA_CAPE) $ret[] = __('Umhang');

        return $ret ? ('[nt]' . implode(', ', $ret)) : 'Unbekannt';
    }

    public static function convertStringCover() {
        $ret = array();
        if ((static::$atype & static::MIAA_BODY) || (static::$acover & static::MIAA_BODY)) $ret[] = __('Körper');
        if ((static::$atype & static::MIAA_HELMET) || (static::$acover & static::MIAA_HELMET)) $ret[] = __('Kopf');

        return $ret ? ('[nt]' . implode(', ', $ret)) : 'Nichts';
    }

    public function convertStringProtection() {
        if ($this->protection > 100) return "Sehr stabil";
        elseif ($this->protection > 75) return "Stabil";
        elseif ($this->protection > 50) return "Durchschnittlich";
        elseif ($this->protection > 25) return "Wackelig";
        elseif ($this->protection > 10) return "Instabil";
        elseif ($this->protection > 5) return "Sehr Instabil";
        else return "Desolat";
    }

    public static function get_random_slot() {
        $select = array(Model_Items_Abstract_Armor::MIAA_HELMET, Model_Items_Abstract_Armor::MIAA_BODY);
        return $select[mt_rand(0, count($select) - 1)];
    }

    public function is_active() {
        return $this->active && !$this->is_destroyed();
    }

    public function is_destroyed() {
        return ($this->protection <= 0);
    }

    public function get_destroyed_class() {
        return static::$destroyed;
    }

    public function get_protection() {
        return $this->protection;
    }

    public function drop($silent = false) {
        $r = parent::drop();
        if ($r)
            $this->unequip();
        return $r;
    }

    public function drop_dead() {
        if (static::$destroyed)
            return new static::$destroyed;
        else return null;
    }

    /**
     * @param number $damage
     * @return number
     */
    public function take_damage($damage) {
        global $game;
        $this->protection -= $damage;

        $damage = max(0,$damage - static::$damage_blocking);
        if ($this->protection <= 0)
            $this->unequip();
        return max(0,$damage * (1 - static::$damage_reduction));
    }

    public function blocks($slot, $assume_equip = false) {
        if (!$assume_equip && !$this->active)
            return false;
        return static::$atype & $slot;
    }

    public function covers($slot, $assume_equip = false) {
        if (!$assume_equip && !$this->active)
            return false;
        return static::$acover & $slot;
    }

    public function equip($player = null) {
        /** @global Model_Player $player */
        if ($player === null)
            global $player;

        foreach ($player->inventory()->get('Model_Items_Abstract_Armor') as $item)
            /** @var Model_Items_Abstract_Armor $item */
            if ($item->blocks(static::$atype))
                $item->unequip();

        $this->active = true;
    }

    public function unequip() {
        $this->active = false;
    }

    protected function hid() {
        $php53pb = $this;
        if (!$this->is_active())
            return parent::hid()
                ->add_action('Anlegen',
                    Model_Action::factory()
                        ->condition(function($p) use ($php53pb) {
                            return $p->inventory()->has($php53pb->uin());
                        })
                        ->fail_message('Du musst diesen Gegenstand aufheben, bevor du ihn anlegen kannst.')
                        ->effect(
                            Model_Effect::factory()
                                ->message('Du hast eine neue Rüstung angelegt.')
                                ->custom(function($p) use ($php53pb) {
                                    $php53pb->equip($p);
                                })
                        )
                );
        else
            return parent::hid()
                ->add_action('Ablegen',
                    Model_Action::factory()
                        ->effect(
                            Model_Effect::factory()
                                ->message('Du hast eine Rüstung abgelegt.')
                                ->custom(function($p) use ($php53pb) {
                                    $php53pb->unequip();
                                })
                        )
                );
    }
	
}	