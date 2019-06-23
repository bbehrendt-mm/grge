<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Players_Player extends Model_Combat_Actor {

    /** @var Model_Player */
    protected $player;
    protected static $show_weapon_switch = true;

    protected static $escape = true;

    protected static $taunts = [
        'drunk'   => ['*hicks*'],
        'berserk' => ['AAAAAAAAAAAARGH!!!!!!!!!'],
        'begin'   => ['Ihr kriegt mich nicht!','Nicht heute!','Verflucht!','ZOMBIES!']
    ];

    public static function static_sprite(): ?string {
        return parent::static_sprite() ?? 'player.gif';
    }

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
     * @param Interface_Plentity $p
     */
    public function transfer_stats(Interface_Plentity $p) : void {

        $this->mod_damage     = $p->get_status()->get(Model_Status::MS_CHAR_DAMAGE_MULTIPLIER);
        $this->mod_resistance = $p->get_status()->get(Model_Status::MS_CHAR_DAMAGE_RESISTANCE);
        $this->mod_accuracy   = $p->get_status()->get(Model_Status::MS_CHAR_ACCURACY);
        $this->mod_escape     = $p->get_status()->get(Model_Status::MS_CHAR_EVASIVENESS);

        if ($p->get_status()->get(Model_Status::MS_STAT_DRUNK) > 25) $this->add_modifier(
            'drunk', ($p->get_status()->get(Model_Status::MS_STAT_DRUNK)-25)*(4/300));

        if ($p->get_status()->retrieve('spray2')) $this->add_modifier(
            'berserk', 0.75);
    }

    /**
     * @param Model_Player $p
     *
     * @return Model_Combat_Players_Player
     * @throws Exception
     */
    public static function create_linked_actor($p): Model_Combat_Players_Player
    {
        $is_wolfman = $p->job(1061);
        $unarmed = $is_wolfman ? new Model_Items_Dogbite() : new Model_Items_Fist();
        $unarmed->equip($p);
        $unarmed->register($p);

        /** @noinspection PhpUndefinedMethodInspection */
        $ret = static::factory()
            ->player($p)
            ->name($p->name(), Model_Combat_Actor::MCA_TYPE_PLAYER)
            ->strength($p->get_status()->get(Model_Status::MS_STAT_HEALTH), 100, 1)
            ->add_weapon($unarmed);

        $ai = $p->ai();
        /** @var Model_Combat_Players_Player $ret */

        $ret->ai_selfishness    = strpos($ai, '-') === 0
            ? 1.0 : (strpos(
                $ai, '+'
            ) === 0 ? 9.0 : 3.0 );
        $ret->ai_comradely      = $ai[1] === '-' ? 0.1 : ($ai[1] === '+' ? 1.5 : 0.5 );
        $ret->ai_volatile       = $ai[2] === '-' ? 0.5 : ($ai[2] === '+' ? 1.0 : 0.8 );
        $ret->ai_brashness      = $ai[3] === '-' ? 0.4 : ($ai[3] === '+' ? 0.9 : 0.7 );

        [$ret->stat_initiative, $ret->stat_damage, $ret->stat_resistance, $ret->stat_accuracy
            ]
            = $p->battle_stats();

        $ret->transfer_stats($p);
        $ret->register_inventory($p->inventory());

        return $ret;
    }

    protected function damage($damage, $from = null, $armor_damage = null): void {
        parent::damage($this->player->get_status()->scaling(Model_Status::MS_STAT_HEALTH, Model_Status::MS_EFFECT_BATTLE, -$damage) * $damage, $from, $armor_damage);

        $this->player->get_status()->modify(Model_Status::MS_STAT_HEALTH, -$damage, Model_Status::MS_EFFECT_BATTLE);
    }

    public function customSprite($death_sprite = false): ?string {
        if (!$death_sprite && $this->player->job(1080))
            return 'child.gif';
        else return parent::customSprite($death_sprite);
    }

    /**
     * @param Model_Combat_Weapon|Model_Combat_Weapon[] $weapon
     *
     * @return Model_Combat_Actor
     * @throws Exception
     */
    public function add_weapon($weapon): Model_Combat_Actor {
        if (!is_array($weapon))
            $weapon->register($this->player);

        return parent::add_weapon($weapon);
    }

    public function disengage(): bool {
        foreach ($this->weapons as $weapon)
            $weapon->unregister();
        /** @var Model_Buffs_Abstract_Buff $w */
        foreach ($this->wounds as $w)
            new $w($this->player->id());
        return true;
    }

    /**
     * @param                    $damage
     * @param                    $kills
     * @param                    $death
     * @param Model_Combat_Actor $target
     *
     * @throws Exception
     */
    protected function score_kills($damage, $kills, $death, $target): void {
        if (Tool_Scripts::is_npc($this->player)) return;

        if ($kills > 0 && $target->get_type() === static::MCA_TYPE_ZOMBIE)
            $this->player->achievements()->achieve(Model_Achievement::MA_KILLED_ZOMBIES, $kills);

        if ($kills > 0 && Tool_System::instance_of($target, 'Model_Combat_Zombies_Ghul'))
            $this->player->achievements()->achieve(Model_Achievement::MA_MERCYKILL, $kills);
    }

    public function get_avatar(): ?string {
        $s = Model_Euser::avatar_by_id($this->player->id());
        return $s ? ('http:' . $s) : null;
    }

    public function enter(): void {
        parent::enter();
        if (static::$show_weapon_switch) $this->scene->switch_weapon($this, $this->current_weapon);
    }
}