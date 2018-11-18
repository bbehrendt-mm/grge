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
    private $allow_remote_execution = true;
    private $allow_autonomous_execution = true;
    private $prevent_user_type = [];
    private $allowed_user_type = [];

    private $additional_flags = [];

    private $consume_by_grind = true;

    private $has_se = false;
    private $popup = null;

    private $parent = null;

    /**
     * @return Model_Action
     */
    public static function factory(): \Model_Action
    {
        return new self();
    }

    public function setParent(Model_Items_Abstract_Item $parent): void
    {
        $this->parent = $parent;
        foreach ($this->effects as $e) {
            $e['effect']->setParent($parent);
            if ($e['side_effect']) $e['side_effect']->setParent($parent);
        }
    }

    /**
     * @param null|bool $v
     * @return Model_Action|bool
     */
    public function allow_remote($v = null) {
        if ($v === null) return $this->allow_remote_execution;
        else $this->allow_remote_execution = $v;
        return $this;
    }

    public function allow_auto($v = null) {
        if ($v === null) return $this->allow_autonomous_execution;
        else $this->allow_autonomous_execution = $v;
        return $this;
    }

    /**
     * @param number|array $args,...
     * @return Model_Action
     */
    public function deny_for($args): \Model_Action
    {
        if (!is_array($args))
            $args = func_get_args();

        $this->prevent_user_type = array_merge($this->prevent_user_type, $args);
        return $this;
    }

    /**
     * @param number|array $args,...
     * @return Model_Action
     */
    public function allow_for($args): \Model_Action
    {
        if (!is_array($args))
            $args = func_get_args();

        $this->allowed_user_type = array_merge($this->allowed_user_type, $args);
        return $this;
    }

    /**
     * @param $type
     * @return bool
     */
    public function denied_for($type): bool
    {
        return in_array($type, $this->prevent_user_type, true)
            || (count($this->allowed_user_type) && !in_array(
                    $type, $this->allowed_user_type, true
                ));
    }

    /**
     * @param callable $cond
     * @return Model_Action
     */
    public function condition($cond): \Model_Action
    {
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
     * @param string $flag
     * @param mixed $value
     * @return Model_Action
     */
    public function flag($flag, $value): Model_Action {
        $this->additional_flags[$flag] = $value;
        return $this;
    }

    public function get_flag($flag = null) {
        if ($flag === null) return $this->additional_flags;
        else return $this->additional_flags[$flag] ?? null;
    }


    /**
     * @param string|number $reference
     * @param number $value
     * @return Model_Action
     */
    public function requirement($reference, $value): \Model_Action
    {
        $this->requirements[$reference] = $value;
        return $this;
    }

    /**
     * @param string|null $s
     * @return Model_Action
     */
    public function argument($s = null): \Model_Action
    {
        if ($s === null) return $this->argument;
        $this->argument = $s;
        return $this;
    }

    /**
     * @param string $message
     * @param null|string $id
     * @return Model_Action
     */
    public function fail_message($message, $id = null): \Model_Action
    {
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
    public function effect(Model_Effect $effect, $id = null, $condition = null, $side_effect = null): \Model_Action
    {
        if ($this->parent) $effect->setParent($this->parent);
        if ($side_effect !== null) {
            $this->has_se = true;
            if ($this->parent) $side_effect->setParent($this->parent);
        }
        if ($id === null)
            $this->effects[] = array('effect' => $effect, 'condition' => $condition, 'side_effect' => $side_effect, 'id' => $id);
        else $this->effects[$id] = array('effect' => $effect, 'condition' => $condition, 'side_effect' => $side_effect, 'id' => $id);

        return $this;
    }

    /**
     * @param string $id
     * @return Model_Effect|null
     */
    public function &get_effect($id): ?\Model_Effect
    {
        global $null;
        $null = null;
        if (isset($this->effects[$id]))
            return $this->effects[$id]['effect'];
        else return $null;
    }

    /**
     * @return bool
     */
    public function has_side_effect(): bool
    {
        return $this->has_se;
    }

    /**
     * @param Model_Effect $effect
     * @param null|Model_Effect $side_effect
     * @param bool $execute_always
     * @return Model_Action
     */
    public function show_as($effect, $side_effect = null, $execute_always = false): \Model_Action
    {
        $this->show_as = array('e' => $effect, 's' => $side_effect, 'b' => $execute_always);
        return $this;
    }

    /**
     * @param callable $newval
     * @return Model_Action
     */
    public function decider($newval): \Model_Action
    {
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
     * @return Struct_ItemEntry[]
     */
    private function get_item_requirements(): array
    {
        $tmp = array();
        foreach ($this->requirements as $class => $count)
            if (!is_numeric($class)) {
                $inst = new Struct_ItemEntry();
                $inst->class = $class;
                $inst->count = $count;
                $tmp[] = $inst;
            }

        return $tmp;
    }

    /**
     * @return array
     */
    private function get_stat_requirements(): array
    {
        $tmp = array();
        foreach ($this->requirements as $class => $count)
            if (is_numeric($class))
                $tmp[$class] = $count;
        return $tmp;
    }

    /**
     * @param Model_Player|Interface_Plentity $player
     * @param null|Model_Player               $side_player
     * @param null|mixed                      $argument
     *
     * @return boolean
     * @throws Exception
     */
    public function test($player, $side_player = null, $argument = null): bool
    {
        if ($this->popup) return false;

        if ($this->condition !== null) {
            /** @var callable $cf */
            $cf = $this->condition;
            if (($r = $cf($player, $side_player, $argument)) !== true)
                return false;
        }

        foreach ($this->get_stat_requirements() as $stat => $value)
            if (!$player->get_status()->has($stat, $value, Model_Status::MS_EFFECT_REQUIREMENT))
                return false;

        if (!Tool_Scripts::has_available_items($this->get_item_requirements(), true, true, false, $player, $this->consume_by_grind))
            return false;

        return true;
    }

    /**
     * @param Model_Player|Interface_Plentity $player
     * @param null|Model_Player               $side_player
     * @param null|mixed                      $argument
     *
     * @return boolean
     * @throws Exception
     */
    public function execute($player, $side_player = null, $argument = null): bool
    {
        if ($this->popup) return false;
        $no_player = Tool_Scripts::is_npc($player);

        if ($this->condition !== null) {
            /** @var callable $cf */
            $cf = $this->condition;
            if (($r = $cf($player, $side_player, $argument)) !== true) {
                if ($this->failmsg && !$no_player)
                    $player->log()->add(is_array($this->failmsg) ? $this->failmsg[$r] : $this->failmsg);
                return false;
            }
        }

        foreach ($this->get_stat_requirements() as $stat => $value)
            if (!$player->get_status()->has($stat, $value, Model_Status::MS_EFFECT_REQUIREMENT)) {
                if (!$no_player) $player->log()->add('Du bist derzeit nicht in der Lage diese Aktion durchzuführen.');
                return false;
            }

        if (!Tool_Scripts::consume_available_item_structs($this->get_item_requirements(), true, true, false, $player, $this->consume_by_grind)) {
            if (!$no_player) $player->log()->add('Dir fehlen Gegenstände, um diese Aktion durchzuführen.');
            return false;
        }

        foreach ($this->get_stat_requirements() as $stat => $value)
            $player->get_status()->modify($stat, -$value, Model_Status::MS_EFFECT_REQUIREMENT);

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
            $tmp = $tmp[$tmp2[random_int(0, count($tmp) - 1)]];
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
    public function export($exp): \Model_Action
    {
        $this->export = $exp;
        return $this;
    }

    /**
     * @param Model_Player $player
     * @return array
     */
    public function convert_effects($player = null): array
    {
        if ($this->show_as !== null)
            /** @noinspection PhpUndefinedMethodInspection */
        return array('effects' => $this->show_as['e']->convert($player));

        if (!$this->effects)
            return array();

        if ($this->export && is_string($this->export))
            $r = $this->export;
        elseif ($this->export) {
            /** @var callable $tmp */
            $tmp = $this->export;
            $r = $tmp($player);
        } elseif (count($this->effects) === 1)
            $r = 0;
        else $r = null;

        /** @noinspection PhpUndefinedMethodInspection */
        return ($r !== null && isset($this->effects[$r])) ? array('effects' => $this->effects[$r]['effect']->convert($player), 'sides' => $this->effects[$r]['side_effect'] ? $this->effects[$r]['side_effect']->convert() : null) : array('effect' => array(array('value' => '???')));
    }

    public function list_effects($player = null): array
    {
        if ($this->show_as !== null)
            /** @noinspection PhpUndefinedMethodInspection */
            return $this->show_as['e']->stat_list($player);

        if (!$this->effects)
            return [];

        if ($this->export && is_string($this->export))
            $r = $this->export;
        elseif ($this->export) {
            /** @var callable $tmp */
            $tmp = $this->export;
            $r = $tmp($player);
        } elseif (count($this->effects) === 1)
            $r = 0;
        else $r = null;

        /** @noinspection PhpUndefinedMethodInspection */
        return ($r !== null && isset($this->effects[$r])) ? $this->effects[$r]['effect']->stat_list($player) : [];
    }

    public function has_requirements(): bool
    {
        return count($this->get_stat_requirements()) || count($this->get_item_requirements());
    }

    /**
     * @param null|Interface_Plentity $player
     * @return array
     */
    public function convert_requires($player = null): array
    {
        $t = array();
        foreach ($this->get_stat_requirements() as $stat => $value)
            $t[] = array('icon' => Model_Effect::translate($stat), 'value' => $value * ($player ? $player->get_status()->scaling($stat, Model_Status::MS_EFFECT_REQUIREMENT) : 1));
        foreach ($this->get_item_requirements() as $entry) {
            $class = $entry->class;
            /** @var Model_Items_Abstract_Item $class */
            if (!Tool_System::instance_of($class,Model_Items_Abstract_Virtual::cls()))
                $t[] = array('icon' => $class::static_icon(), 'value' => $entry->count);
        }

        return $t;
    }
}