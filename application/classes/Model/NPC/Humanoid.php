<?php

abstract class Model_NPC_Humanoid extends Model_NPC_Nano
{
    protected static $entity_type = Interface_Plentity::IC_NPC_HUMANOID;
    protected $last_hideout = null;

    protected static $inventory_size = 100;
    protected static $namelist = [];

    protected static $buffs_heartbeat_name = 'Model_Buffs_Heartbeat';
    protected static $buffs_metabolism_name = 'Model_Buffs_Metabolism';
    protected static $buffs_place_various = true;

    public function __construct($name = null) {
        /** @global Model_Game $game */
        global $game;

        if (static::$namelist && $name === null) {
            $list = array();
            for ($i = 0; $i < count(static::$namelist); $i++)
                if ($game->ndp_check(get_called_class(), $i))
                    $list[] = $i;

            if (!$list) {
                $game->ndp_purge(get_called_class());
                $type = mt_rand(0, count(static::$namelist) - 1);
            } else $type = $list[mt_rand(0, count($list) - 1)];

            $name = static::$namelist[$type];
            $game->ndp_register(get_called_class(), $type);
        }

        if (!$name) $name = $this->entity_species();

        parent::__construct($name);

        $this->get_status()->set(
            Model_Status::MS_STAT_HEALTH, 100,
            Model_Status::MS_STAT_ENERGY, 100,
            Model_Status::MS_STAT_HUNGER, 100,
            Model_Status::MS_STAT_THIRST, 100,
            Model_Status::MS_STAT_SLEEPY, 100
        );

        $this->inventory()->limit(static::$inventory_size);

        if (static::$buffs_heartbeat_name) new static::$buffs_heartbeat_name($this, -1);
        if (static::$buffs_metabolism_name) new static::$buffs_metabolism_name($this);

        if (static::$buffs_place_various) {
            new Model_Buffs_Alcohol($this);
            new Model_Buffs_Zombify($this);
            new Model_Buffs_Nuclear($this);
            new Model_Buffs_Backpack($this);
            new Model_Buffs_Transport($this);
            new Model_Buffs_Daytime($this);
            new Model_Buffs_Freeze($this);
        }
    }
}