<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Benjamin
 * Date: 12.08.13
 * Time: 18:02
 * To change this template use File | Settings | File Templates.
 */

class Model_Action {

    private $decider = null;
    private $export = null;
    private $effects = array();
    private $condition = null;
    private $failmsg = null;
    private $requirements = array();
    private $show_as = null;
    private $argument = null;
    private $description = null;
    private $skin = null;

    private $consume_by_grind = true;

    private $has_se = false;
    private $popup = null;

    /**
     * @return Model_Action
     */
    public static function factory() {
        return new Model_Action();
    }

    /**
     * @param callable $cond
     * @return Model_Action
     */
    public function condition($cond) {
        $this->condition = $cond;
        return $this;
    }

    public function grind_requirements($set) {
        $this->consume_by_grind = $set;
        return $this;
    }

    /**
     * @param $txt
     * @return Model_Action|String
     */
    public function description($txt = null) {
        if ($txt !== null) {
            $this->description = $txt;
            return $this;
        } else return $this->description;
    }

    /**
     * @param $skin
     * @return Model_Action|String
     */
    public function buttonskin($skin = null) {
        if ($skin !== null) {
            $this->skin = $skin;
            return $this;
        } else return $this->skin;
    }


    /**
     * @param string|number $reference
     * @param number $value
     * @return Model_Action
     */
    public function requirement($reference, $value) {
        $this->requirements[$reference] = $value;
        return $this;
    }

    /**
     * @param string|null $s
     * @return Model_Action
     */
    public function argument($s = null) {
        if ($s === null) return $this->argument;
        $this->argument = $s;
        return $this;
    }

    /**
     * @param string $message
     * @param null|string $id
     * @return Model_Action
     */
    public function fail_message($message, $id = null) {
        if ($id === null) $this->failmsg = $message;
        else {
            if (!is_array($this->failmsg))
                $this->failmsg = array();
            $this->failmsg[$id] = $message;
        }

        return $this;
    }

    /**
     * @param Model_Effect $effect
     * @param null|string $id
     * @param null|callable $condition
     * @param null|Model_Effect $side_effect
     * @return Model_Action
     */
    public function effect($effect, $id = null, $condition = null, $side_effect = null) {
        if ($side_effect !== null)
            $this->has_se = true;
        if ($id === null)
            $this->effects[] = array('effect' => $effect, 'condition' => $condition, 'side_effect' => $side_effect, 'id' => $id);
        else $this->effects[$id] = array('effect' => $effect, 'condition' => $condition, 'side_effect' => $side_effect, 'id' => $id);

        return $this;
    }

    /**
     * @return bool
     */
    public function has_side_effect() {
        return $this->has_se;
    }

    /**
     * @param Model_Effect $effect
     * @param null|Model_Effect $side_effect
     * @param bool $execute_always
     * @return Model_Action
     */
    public function show_as($effect, $side_effect = null, $execute_always = false) {
        $this->show_as = array('e' => $effect, 's' => $side_effect, 'b' => $execute_always);
        return $this;
    }

    /**
     * @param callable $newval
     * @return Model_Action
     */
    public function decider($newval) {
        $this->decider = $newval;
        return $this;
    }

    /**
     * @param string|null $pp
     * @return string|Model_Action
     */
    public function popup($pp = null) {
        if ($pp === null) return $this->popup;
        else {
            $this->popup = $pp;
            return $this;
        }
    }

    /**
     * @return array
     */
    private function get_item_requirements() {
        $tmp = array();
        foreach ($this->requirements as $class => $count)
            if (!is_numeric($class))
                $tmp[$class] = $count;
        return $tmp;
    }

    /**
     * @return array
     */
    private function get_stat_requirements() {
        $tmp = array();
        foreach ($this->requirements as $class => $count)
            if (is_numeric($class))
                $tmp[$class] = $count;
        return $tmp;
    }

    /**
     * @param Model_Player $player
     * @param null|Model_Player $side_player
     * @param null|mixed $argument
     * @return boolean
     */
    public function execute($player, $side_player = null, $argument = null) {

        if ($this->popup) return false;

        if ($this->condition !== null) {
            /** @var callable $cf */
            $cf = $this->condition;
            if (($r = $cf($player, $side_player, $argument)) !== true) {
                if ($this->failmsg)
                    $player->log()->add(is_array($this->failmsg) ? $this->failmsg[$r] : $this->failmsg);
                return false;
            }
        }

        foreach ($this->get_stat_requirements() as $stat => $value)
            if ($player->stats_get($stat) < $value) {
                $player->log()->add('Du bist derzeit nicht in der Lage diese Aktion durchzuführen.');
                return false;
            }

        if (!Tool_Scripts::consume_available_items($this->get_item_requirements(), true, true, false, $player, $this->consume_by_grind)) {
            $player->log()->add('Dir fehlen Gegenstände, um diese Aktion durchzuführen.');
            return false;
        }

        foreach ($this->get_stat_requirements() as $stat => $value)
            $player->stats_modify($stat, -$value);

        $tmp = array();
        foreach ($this->effects as $id => $effect)
            if ( !$effect['condition'] || $effect['condition']($player, $side_player, $argument))
                $tmp[$id] = $effect;

        if (!count($tmp))
            return false;

        if ($this->decider !== null) {
            /** @var callable $func */
            $func = $this->decider;
            /** @noinspection PhpIllegalArrayKeyTypeInspection */
            $tmp = $tmp[$func($player, $side_player, array_keys($this->effects))];
        } else {
            $tmp2 = array_keys($tmp);
            $tmp = $tmp[$tmp2[mt_rand(0, count($tmp) - 1)]];
        }

        /**
         * @var $effect Model_Effect
         * @var $side_effect Model_Effect
         */
        $effect = $tmp['effect'];
        $side_effect = $tmp['side_effect'];

        if (!$effect || ($side_player && !$side_effect) || (!$side_player && $side_effect))
            return false;

        if ($this->show_as !== null && $this->show_as['b']) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->show_as['e']->execute($player, $argument);
            if ($this->show_as['s']) /** @noinspection PhpUndefinedMethodInspection */
                $this->show_as['s']->execute($side_player, $argument);
        }

        $effect->execute($player, $argument);
        if ($side_effect)
            $side_effect->execute($side_player, $argument);

        return true;
    }

    /**
     * @param string|callable $exp
     * @return Model_Action
     */
    public function export($exp) {
        $this->export = $exp;
        return $this;
    }

    /**
     * @param Model_Player $player
     * @return array
     */
    public function convert_effects($player = null) {
        if ($this->show_as !== null)
            /** @noinspection PhpUndefinedMethodInspection */
        return array('effects' => $this->show_as['e']->convert());

        if (!$this->effects)
            return array();

        if ($this->export && is_string($this->export))
            $r = $this->export;
        elseif ($this->export) {
            /** @var callable $tmp */
            $tmp = $this->export;
            $r = $tmp($player);
        } elseif (count($this->effects) == 1)
            $r = 0;
        else $r = null;

        /** @noinspection PhpUndefinedMethodInspection */
        return ($r !== null && isset($this->effects[$r])) ? array('effects' => $this->effects[$r]['effect']->convert(), 'sides' => $this->effects[$r]['side_effect'] ? $this->effects[$r]['side_effect']->convert() : null) : array('effect' => array(array('value' => '???')));
    }

    public function convert_requires() {
        $t = array();
        foreach ($this->get_stat_requirements() as $stat => $value)
            $t[] = array('icon' => Model_Effect::translate($stat), 'value' => $value);
        foreach ($this->get_item_requirements() as $class => $value)
            /** @var Model_Items_Abstract_Item $class */
        $t[] = array('icon' => $class::static_icon(), 'value' => $value);
        return $t;
    }
}