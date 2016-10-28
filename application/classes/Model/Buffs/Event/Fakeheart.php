<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Buffs_Event_Fakeheart extends Model_Buffs_Heartbeat {
    protected static $immortal = true;
    protected static $remotable = true;
}