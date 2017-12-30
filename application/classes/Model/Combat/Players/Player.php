<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Player extends Model_Combat_Actor {

    /** @var Model_Player */
    protected $player;
    protected static $show_weapon_switch = true;

    protected static $escape = true;

    protected static $taunts = [
        'drunk' => ['*hicks*'],
        'begin' => ['Ihr kriegt mich nicht!','Nicht heute!','Verflucht!','ZOMBIES!']
    ];

    /**
     * @param null|Model_Player $p
     * @return Model_Combat_Players_Player|Model_Player
     */
    public function player($p = null) {
        if ($p === null) return $this->player;
        else $this->player = $p;
        return $this;
    }

    /**
     * @param Model_Player $p
     * @return Model_Combat_Players_Player
     */
    public static function create_linked_actor($p) {

        $unarmed = new Model_Items_Fist();
        $unarmed->equip($p);
        $unarmed->register($p);

        /** @noinspection PhpUndefinedMethodInspection */
        $ret = static::factory()
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->get_status()->get(Model_Status::MS_STAT_HEALTH), 100, 1)
            ->register_inventory($p->inventory())
            ->add_weapon($unarmed);

        $ai = $p->ai();
        /** @var Model_Combat_Players_Player $ret */

        $ret->ai_selfishness    = $ai[0] == '-' ? 1.0 : ($ai[0] == '+' ? 9.0 : 3.0 );
        $ret->ai_comradely      = $ai[1] == '-' ? 0.1 : ($ai[1] == '+' ? 1.5 : 0.5 );
        $ret->ai_volatile       = $ai[2] == '-' ? 0.5 : ($ai[2] == '+' ? 1.0 : 0.8 );
        $ret->ai_brashness      = $ai[3] == '-' ? 0.4 : ($ai[3] == '+' ? 0.9 : 0.7 );

        list($ret->stat_initiative, $ret->stat_damage, $ret->stat_resistance, $ret->stat_accuracy) = $p->battle_stats();
        $ret->escape_modifier = $p->get_status()->get(Model_Status::MS_CHAR_EVASIVENESS);

        if ($p->get_status()->get(Model_Status::MS_STAT_DRUNK) > 25) $ret->add_modifier("drunk", ($p->get_status()->get(Model_Status::MS_STAT_DRUNK)-25)*(4/300));

        return $ret;
    }

    protected function damage($damage, $from = null, $armor_damage = null) {
        parent::damage($this->player->get_status()->scaling(Model_Status::MS_STAT_HEALTH, Model_Status::MS_EFFECT_BATTLE) * $damage, $from, $armor_damage);

        $this->player->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage, Model_Status::MS_EFFECT_BATTLE);
    }

    public function customSprite($death_sprite = false) {
        if (!$death_sprite && $this->player->job(1080))
            return 'child.gif';
        else return parent::customSprite($death_sprite);
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     * @return Model_Combat_Actor
     */
    public function add_weapon($weapon) {
        if (!is_array($weapon))
            $weapon->register($this->player);

        return parent::add_weapon($weapon);
    }

    public function disengage() {
        foreach ($this->weapons as $weapon)
            $weapon->unregister();
        /** @var Model_Buffs_Abstract_Buff $w */
        foreach ($this->wounds as $w)
            new $w($this->player->id());
    }

    /**
     * @param $damage
     * @param $kills
     * @param $death
     * @param $target
     */
    protected function score_kills($damage, $kills, $death, $target) {
        if ($kills > 0 && $target->get_type() == static::MCA_TYPE_ZOMBIE)
            $this->player->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $kills);

        if ($kills > 0 && Tool_System::instance_of($target, 'Model_Combat_Zombies_Ghul'))
            $this->player->achievements()->achieve(Model_Achievement::MA_MERCYKILL, $kills);
    }

    public function get_avatar() {
        $s = Model_Euser::avatar_by_id($this->player->id());
        return $s ? ('http:' . $s) : null;
    }

    public function enter() {
        parent::enter();
        if (static::$show_weapon_switch) $this->scene->switch_weapon($this, $this->current_weapon);
    }
}