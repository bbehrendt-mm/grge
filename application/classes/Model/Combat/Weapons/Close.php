<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Combat_Weapons_Close extends Model_Combat_Weapons_Energy {

    protected static $max_range = 1;

    protected function range(): array {
        return [0, $this->max_range()];
    }

    public function max_range(): float {
        return static::$max_range;
    }
}