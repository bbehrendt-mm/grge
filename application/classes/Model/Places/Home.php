<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Home extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Versteck';
    protected static $description = 'In deinem Versteck bist du vor Zombieangriffen geschützt und kannst dich von deinen Aktionen in der Aussenwelt erholen - zumindest, wenn du dich gut verbarrikadiert hast! Unglücklicherweise kannst du nicht für immer hier sitzen bleiben - das wirst du spätestens dann merken, wenn deine gesammelten Vorräte aufgebraucht sind ...';
    protected static $icon = 'home';

    public function setup_primary_rooms(): Model_Room
    {
        $room = parent::setup_primary_rooms();
        Model_Blueprints::fast_apply($this, 'upgrades', ['hideout'], $room);

        return $room;
    }

    public function setup_additional_rooms(): void
    {
        if (Globals::CurrentGameF()->config('modules.mapping'))
            $this->setup_new_room($this->create_new_room(5,['inside']),
                ['radiotower'],
                []
            );
        else $this->create_new_room(10,['inside']);
        $this->create_new_room(15,['inside']);
        $this->create_new_room(20,['outside']);
        $this->create_new_room(20,['outside']);
    }

    //Base deco value
    protected static $base_deco_value = -20;

    private  $map_points = 0;

    public function get_map_points(): int
    {
        return $this->map_points;
    }

    public function set_map_points($new): void {
        $this->map_points = $new;
    }
}	