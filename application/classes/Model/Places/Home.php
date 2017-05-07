<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Home extends Model_Places_Abstract_Hideout {

    protected static $name = 'Versteck';
    protected static $description = 'In deinem Versteck bist du vor Zombieangriffen geschützt und kannst dich von deinen Aktionen in der Aussenwelt erholen - zumindest, wenn du dich gut verbarrikadiert hast! Unglücklicherweise kannst du nicht für immer hier sitzen bleiben - das wirst du spätestens dann merken, wenn deine gesammelten Vorräte aufgebraucht sind ...';
    protected static $icon = 'home';

    public function setup_additional_rooms() {
        parent::setup_additional_rooms();
        $this->create_new_room(15,false);
        $this->create_new_room(20,true);
    }

    //Base deco value
    protected static $base_deco_value = -20;

    //Base defense
    protected $defense = 5;

    //Base: 15% per day
    protected static $decay_rate = 0.15;

    //Exp: 8% per day
    protected static $decay_exp = 0.08;

    private  $map_points = 0;

    public function get_map_points() {
        return $this->map_points;
    }

    public function set_map_points($new) {
        $this->map_points = $new;
    }


    public function uin($new = null) {
        if ($new !== null) {
            Model_Blueprints::fast_apply($this, 'upgrades', ['hideout', 'outside','outside_space','slot_epic']);

            if (Globals::CurrentGame()->config('modules.mapping'))
                $this->inventory->add(new Model_Items_Virtual_Location_Mapmode());
        }
        return parent::uin($new);
    }
}	