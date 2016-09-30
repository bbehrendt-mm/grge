<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Events_Event {

    protected static $event_key = null;
    protected $active = false;

    protected static function get_game_time() {
        /** @global Model_Game $game */
        global $game;
        return $game ? $game->next_tick() : time();
    }

    public static function is_current() {
        return (static::$event_key ? (Tool_Events::current(static::get_game_time()) == static::$event_key) : false);
    }

    public static function get_key() {
        return static::$event_key;
    }

    public function is_active() {
        return $this->active;
    }

    public function trigger() {
        /** @global  Model_Game $game */
        global $game;
        if (static::is_current() && !$this->is_active()) {
            $this->active = $this->trigger_activation();
            if ($this->active) $game->set_event_index($this);
        }

        elseif (!static::is_current() && $this->is_active()) {
            $this->active = !$this->trigger_deactivation();
            if (!$this->active) $game->unset_event_index($this);
        }
    }

    abstract protected function trigger_activation();
    abstract protected function trigger_deactivation();
    abstract public function tick();

}