<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Xmas_Stables extends Model_Places_Abstract_Hideout {

    protected static $location_name = 'Weihnachts-Stall';
    protected static $description = 'In diesem Stall wurden die gut behandelten und definitiv nicht mit Drogen ruhig gestellten Ponys gehalten, auf denen die Kinder reiten konnten. Eigentlich sieht es hier ganz gemütlich aus... von den verrottenden Pferdekadavern mal abgesehen, natürlich.';
    protected static $icon = 'home';
    protected static $starts_built = true;
    protected static $alternative_default_hideout = 'xmas_hideout';

    protected static $perpetualDaytime = 'snowynight';

    private $spawned_rudolph = false;

    //Base deco value
    protected static $base_deco_value = 1;

    //Base defense
    protected $defense = 5;

    //Base: 15% per day
    protected static $decay_rate = 0.10;

    //Exp: 8% per day
    protected static $decay_exp = 0.05;

    public function uin($new = null) {
        if ($new !== null) {
            $items = [Model_Items_Xmas_Rubbing::cls() => 1, Model_Items_Xmas_Beer::cls() => 2, Model_Items_Xmas_Drink::cls() => 3];
            foreach ($items as $cls => $count)
                for ($i = 0; $i < $count; $i++) {
                    $it = new $cls;
                    $this->inventory()->add($it);
                }
        }

        return parent::uin($new);
    }

    public function setup_additional_rooms() {
        $this->setup_new_room($this->create_new_room(20,['inside']),
                              ['stables'],
                              ['hay1'],
                              "Stall");
        $this->create_new_room(20,['inside']);
    }

    public function enter($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        if (!$this->spawned_rudolph) {
            $ev = Globals::CurrentGameF()->get_initialized_event(Model_Events_Xmas::get_key());
            /** @var $ev Model_Events_Xmas */

            if ($ev) {
                $this->spawned_rudolph = true;
                $rudolph = new Model_NPC_Event_Rudolph();
                $rudolph->location_class($this->uin());
                Globals::CurrentGameF()->add_npc($rudolph);

                $ev->register_event_npc($rudolph->id());
            }
        }
        parent::enter($pid, $type);
    }

}	