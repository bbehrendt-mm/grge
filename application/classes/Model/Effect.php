<?php

class Model_Effect {

    private $message;
    private $m_variables = array();
    private $m_translateables = array();

    private $cod = null;

    private $effects = array();
    private $buffs = array();
    private $custom = array();

    private static $translation_effects = array(
        Model_Status::MS_STAT_HEALTH => 'status_health',
        Model_Status::MS_STAT_ENERGY => 'status_energy',
        Model_Status::MS_STAT_DRUNK => 'status_drunk',
        Model_Status::MS_STAT_HUNGER => 'status_hunger',
        Model_Status::MS_STAT_SLEEPY => 'status_sleepy',
        Model_Status::MS_STAT_RADIATION => 'status_rad',
        Model_Status::MS_STAT_THIRST => 'status_thirst',
        Model_Status::MS_STAT_ZOMBIFY => 'status_zmb',
        Model_Status::MS_STAT_FREEZE => 'status_freeze',
    );

    private static $reversed_colors = array(
        Model_Status::MS_STAT_RADIATION, Model_Status::MS_STAT_DRUNK, Model_Status::MS_STAT_ZOMBIFY, Model_Status::MS_STAT_FREEZE
    );

    const CFUNC_PROCESS_POST = 1;
    const CFUNC_PROCESS_PRE = 2;

    /**
     * Creates an instance of this class
     * @return Model_Effect
     */
    public static function factory() {
        return new Model_Effect();
    }

    /**
     * @param string|null $msg New message, optional
     * @param array $variables
     * @param array $translateables
     * @return Model_Effect|string
     */
    public function message($msg = null, $variables = array(), $translateables = array()) {
        if ($msg === null) return $this->message;
        else {
            $this->message = $msg;
            $this->m_variables = $variables;
            $this->m_translateables = $translateables;
            return $this;
        }
    }

    /**
     * @param string|null $msg New message, optional
     * @return Model_Effect|string
     */
    public function causeofdeath($msg = null) {
        if ($msg === null) return $this->cod;
        else {
            $this->cod = $msg;
            return $this;
        }
    }

    /**
     * @param Model_Items_Abstract_Item $item
     * @param bool $grind
     * @return Model_Effect
     */
    public function consume($item, $grind = false) {
        return $this->custom(function() use ($item, $grind) {
            if ($grind)
                $item->grind();
            else $item->consume();
        }, static::CFUNC_PROCESS_POST);
    }

    /**
     * @param int $achievement
     * @param int $num
     * @param bool $all
     * @return Model_Effect
     */
    public function achieve($achievement, $num = 1, $all = false) {
        if ($achievement < 0) return $this;

        if ($all)
            return $this->custom(function() use ($achievement, $num) {
                /** @global Model_Game $game */
                global $game;

                foreach ($game->players() as $p)
                    $p->achievements()->achieve($achievement, $num);
            }, static::CFUNC_PROCESS_POST);
        else
            return $this->custom(function($p) use ($achievement, $num) {
                /** @var Model_Player|Interface_Plentity $p */
                if (!Tool_Scripts::is_npc($p))
                    $p->achievements()->achieve($achievement, $num);
            }, static::CFUNC_PROCESS_POST);
    }

    /**
     * @param int $from
     * @param int $to
     * @param int $num
     * @param bool $block
     * @param bool $all
     * @return Model_Effect
     */
    public function upgrade_achieve($from, $to, $num = 1, $block = true, $all = false) {
        if ($from < 0) return $this;

        if ($all)
            return $this->custom(function() use ($from, $to, $block, $num) {
                /** @global Model_Game $game */
                global $game;

                foreach ($game->players() as $p)
                    $p->achievements()->upgrade_achieve($from, $to, $num, $block);
            }, static::CFUNC_PROCESS_POST);
        else
            return $this->custom(function($p) use ($from, $to, $block, $num) {
                /** @var Model_Player $p */
                $p->achievements()->upgrade_achieve($from, $to, $num, $block);
            }, static::CFUNC_PROCESS_POST);
    }

    /**
     * @param string|null $buff Buff class (when remove is false) or buff identifier (when remove is true)
     * @param bool $remove
     * @param int|number $lifetime
     * @return Model_Effect
     */
    public function buff($buff = null, $remove = false, $lifetime = -1) {
        if ($buff === null)
            return $this;

        if (!$remove && !Tool_System::instance_of($buff, 'Model_Buffs_Abstract_Buff'))
            return $this;

        /** @var Model_Buffs_Abstract_Buff $buff */
        if ($buff::static_visible())
            $this->buffs[$buff::static_icon()] = $remove ? '-' : ($lifetime > 0 ? "+" . $lifetime * 5 . "M" : '+');

        return $this->custom(function($p) use ($buff, $remove, $lifetime) {
            /** @var Model_Player $p */
            if ($remove)
                $p->get_status()->remove($buff::static_bid());
            else new $buff($p->id(), $lifetime);
        }, static::CFUNC_PROCESS_POST);
    }

    /**
     * @param string|Model_Items_Abstract_Item $item
     * @param int $count
     * @return Model_Effect
     */
    public function spawn($item, $count = 1, $find = false) {
        if (!Tool_System::instance_of($item, 'Model_Items_Abstract_Item'))
            return $this;

        return $this->custom(function($p) use ($item, $count, $find) {
            /** @var Model_Player $p */
            $tmp = [];
            if (is_string($item))
                for ($i = 0; $i < $count; $i++)
                    $tmp[] = new $item;
            else $tmp[] = $item;

            if ($find)
                Tool_Scripts::place_new_item($tmp);
            else foreach ($tmp as $item)
                $p->location()->inventory()->add($item);
        }, static::CFUNC_PROCESS_POST);
    }

    public function remove($item, $count) {
        if (!Tool_System::instance_of($item, 'Model_Items_Abstract_Item'))
            return $this;

        return $this->custom(function() use ($item, $count) {
            /** @global Model_Game $game */
            global $game;
            $game->mass_consume(Array($item => $count));
        }, static::CFUNC_PROCESS_POST);
    }

    /**
     * @param null|int|array $stat
     * @param null|number|number[]|string $diff
     * @param null|number $diff2
     * @return Model_Effect|null|number|number[]
     */
    public function effect($stat = null, $diff = null, $diff2 = null) {
        if (is_array($stat)) {
            foreach ($stat as $t => $d)
                $this->effect($t,$d);
            return $this;
        }

        if ($diff === 0 && $diff2 === null)
            return $this;

        if ($stat === null)
            return array_keys($this->effects);
        if ($diff === null)
            return isset($this->effects[$stat]) ? $this->effects[$stat] : null;
        else {
            if (is_numeric($diff) && $diff2 !== null)
                $diff = array($diff, $diff2);
            $this->effects[$stat] = $diff;
            return $this;
        }
    }

    /**
     * @param null|int $stat Display a stat icon in addition to the question marks
     * @return Model_Effect|null|number|number[]
     */
    public function ambiguous_effect($stat = null) {
        return $this->effect(($stat === null) ? -PHP_INT_MAX : -$stat, 1);
    }

    /**
     * @param Model_Player $player
     * @param int $pos
     * @param mixed $argument
     */
    private function call_custom_func($player, $pos, $argument) {
        foreach ($this->custom as $elem)
            if ($elem['pos'] == $pos)
                $elem['func']($player, $argument);
    }

    /**
     * @param callable $func
     * @param int $pos
     * @return Model_Effect
     */
    public function custom($func, $pos = 1) {
        $this->custom[] = array('func' => $func, 'pos' => $pos);
        return $this;
    }

    /**
     * @param Model_Player $player
     * @param mixed $argument
     */
    public function execute($player, $argument) {
        $this->call_custom_func($player, static::CFUNC_PROCESS_PRE, $argument);

        if ($this->cod)
            $player->get_status()->set_cause_of_death($this->cod);

        $accum = [];
        foreach ($this->effects as $stat => $dif) if ($stat >= 0) {
            $accum[] = $stat;
            $accum[] = is_array($dif) ? mt_rand($dif[0], $dif[1]) : $dif;
        }
        $player->get_status()->modify($accum, Model_Status::MS_EFFECT_ITEM);

        if ($this->message && !Tool_Scripts::is_npc($player))
            $player->log()->add($this->message, $this->m_variables, $this->m_translateables);

        $this->call_custom_func($player, static::CFUNC_PROCESS_POST, $argument);

        if ($this->cod)
            $player->get_status()->clear_cause_of_death();
    }

    /**
     * @param $stat
     * @return string
     */
    public static function translate($stat) {
        return isset(static::$translation_effects[$stat]) ? static::$translation_effects[$stat] : 'undefined';
    }

    /**
     * @param $stat
     * @param $dif
     * @return string
     */
    private static function color($stat, $dif) {
        if (is_array($dif)) $dif = $dif[0] + $dif[1];
        if (is_string($dif)) {
            if ($dif[0] == '+') $dif = 1;
            if ($dif[0] == '-') $dif = -1;
        }
        if ($dif == 0) return '';
        elseif ($dif > 0) return (array_search($stat, static::$reversed_colors) === false) ? 'green' : 'red';
        else return (array_search($stat, static::$reversed_colors) === false) ? 'red' : 'green';
    }

    /**
     * @param $i
     * @param int $stat
     * @param null|Interface_Plentity $player
     * @return string
     */
    private function convert_val($i, $stat = -1, $player = null) {
        if (is_string($i)) return $i;

        $factor = ($stat >= 0 && $player) ? $player->get_status()->scaling($stat, Model_Status::MS_EFFECT_ITEM) : 1;

        if (is_array($i)) return ($i[0] * $factor) . " - " . ($i[1] * $factor);
        if ($i == PHP_INT_MAX) return '+∞';
        if ($i == -PHP_INT_MAX) return '-∞';
        return $i * $factor;
    }

    /**
     * @param null|Interface_Plentity $player
     * @return array
     */
    public function convert($player = null) {
        $tmp = array();
        foreach ($this->effects as $stat => $dif)
            if ($dif === 0) continue;
            else {
                if ($stat >= 0)
                    $tmp[] = array('icon' => static::translate($stat), 'color' => static::color($stat, $dif), 'value' => $this->convert_val($dif, $stat, $player), 'numeric' => !is_string($dif));
                elseif ($stat == -PHP_INT_MAX)
                    $tmp[] = array('value' => '???');
                else $tmp[] = array('icon' => static::translate(-$stat), 'color' => '', 'value' => '???', 'numeric' => true);
            }


        foreach ($this->buffs as $icon => $act)
            $tmp[] = array('icon' => $icon, 'color' => '', 'value' => $act, 'numeric' => true);

        return $tmp;
    }

    /**
     * @param null|Interface_Plentity $player
     * @return array
     */
    public function stat_list($player = null) {
        $accum = [];
        foreach ($this->effects as $stat => $dif) if ($stat >= 0) {
            $dif = is_array($dif) ? ($dif[0] + $dif[1])/2 : (is_numeric($dif) ? $dif : 0);
            if ($dif == 0) continue;

            if ($player) $dif *= $player->get_status()->scaling($stat, Model_Status::MS_EFFECT_ITEM);
            $accum[$stat] = $dif;
        }

        return $accum;
    }

}