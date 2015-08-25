<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Equipable extends Model_Items_Abstract_Item {

    const MIAE_ARMOR_HELMET = 1;
    const MIAE_ARMOR_BODY = 2;
    const MIAE_ARMOR_SHIELD = 3;
    const MIAE_ARMOR_CAPE = 4;
    const MIAE_WEAPON = 5;

    protected static $allow_multi_equip = false;
    protected static $allow_primary_equip = false;

	protected static $cat = Model_Items_Abstract_Item::MIAI_CAT_GEAR;

    protected static $equipment_type = 0;
    // INI, ATK, DEF, ACC
    protected static $effects = [0,0,0,0];
    protected $equipped;
    protected $equipped_primary;

    public function equip($player = null) {
        /** @global Model_Player $player */
        if ($player === null)
            global $player;

        if (!static::$allow_multi_equip)
            foreach ($player->get_equipment($this->get_equipment_type()) as $item)
                $item->unequip();

        $this->equipped = true;

        if (static::$allow_primary_equip)
            foreach ($player->get_equipment($this->get_equipment_type()) as $item)
                if ($item->equipped_primary)
                    return;

        $this->equip_primary($player);
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

    public function get_stats($type = null) {
        return $type === null ? static::$effects : static::$effects[$type - 1];
    }

    public function unequip() {
        $this->equipped = false;
        $this->equipped_primary = false;
    }

    public function is_equipped() {
        return $this->equipped;
    }

    public function drop($silent = false) {
        $r = parent::drop();
        if ($r)
            $this->unequip();
        return $r;
    }

    public function equip_primary($player = null) {
        if (!static::$allow_primary_equip)
            return;

        /** @global Model_Player $player */
        if ($player === null)
            global $player;

        foreach ($player->get_equipment($this->get_equipment_type(), true) as $item)
            $item->equipped_primary = false;

        $this->equipped_primary = true;
    }

    protected function hid() {
        if (!$this->is_equipped())
            return parent::hid()
                ->add_action('Ausrüsten',
                    Model_Action::factory()
                        ->condition(function($p) {
                            /** @var Model_Player $p */
                            return $p->inventory()->has($this->uin());
                        })
                        ->fail_message('Du musst diesen Gegenstand aufheben, bevor du ihn ausrüsten kannst.')
                        ->effect(
                            Model_Effect::factory()
                                ->message('Du hast dich mit :item ausgerüstet.', [], [':item' => $this->name()])
                                ->custom(function($p) {
                                    $this->equip($p);
                                })
                        )
                );
        else {
            $tmp = parent::hid();

            if (static::$allow_primary_equip && !$this->is_equipped_primary())
                $tmp->add_action('Als Standart setzen',
                    Model_Action::factory()
                        ->effect(
                            Model_Effect::factory()
                                ->message('Du hast :item als Standart ausgewählt.', [], [':item' => $this->name()])
                                ->custom(function($p) {
                                    $this->equip_primary($p);
                                })
                        )
                );

            $tmp->add_action('Ablegen',
                Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->message('Du hast diesen Gegenstand abgelegt.')
                            ->custom(function() {
                                $this->unequip();
                            })
                    )
            );

            return $tmp;

        }

    }
	
}	