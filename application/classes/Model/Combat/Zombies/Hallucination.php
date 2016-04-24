<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Combat_Zombies_Hallucination extends Model_Combat_Zombies_Zombie {

    public static $custom_sprite = 'zombie_lsd.gif';
    public static $custom_death_sprite = 'zombie_lsd_dead.gif';

    protected $name;
    protected $max_health = 15;

    protected $stat_initiative = 0;
    protected $stat_damage = 0;
    protected $stat_resistance = 0;
    protected $stat_accuracy = 0;

    protected static $num_str = 1;

    protected $movement_range = 10;

    public function __construct() {
        parent::__construct();

        $this->name = Tool_Gambling::select(['Schnupfophanten', 'Jigsaw-Nudisten', 'Seehofer', 'Genitalmonster', 'Pokémon', 'Schwiegermütter']);
    }

    protected function get_attack_priority($friends, $foes, $weapon = null, $ignore_range = false) {
        return null;
    }

    protected function get_weapon_priority($friends, $foes) {
        return null;
    }

    public function get_avatar() {
        return 'media/icons/battle/avatar/lsd.jpg';
    }

    /**
     * @param Model_Combat_Actor[] $friends
     * @param Model_Combat_Actor[] $foes
     * @param bool $second_act
     */
    public function act($friends, $foes, $second_act = false) {
        $this->reset_steps();
        if (!$foes) $this->idle();

        $target = $foes[0];
        $d = max(0.5, $this->distance_from($target));

        $old_x = $this->pos_x;
        $old_y = $this->pos_y;

        $dx = ($target->pos_x - $this->pos_x)/$d * min($this->movement_range, $d);
        $dy = ($target->pos_y - $this->pos_y)/$d * min($this->movement_range, $d);

        $this->pos_x += $dx + ($d < 10 ? mt_rand(-3,3) : 0);
        $this->pos_y += $dy + ($d < 10 ? mt_rand(-3,3) : 0);

        $this->pos_x = max(0,min($this->field[0], $this->pos_x));
        $this->pos_y = max(0,min($this->field[1], $this->pos_y));

        $dist = sqrt(pow($old_x - $this->pos_x, 2) + pow($old_y - $this->pos_y, 2));
        $this->scene->move($this, [$this->pos_x, $this->pos_y], $dist, $target);
    }

}