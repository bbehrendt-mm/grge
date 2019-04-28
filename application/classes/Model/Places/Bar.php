<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Bar extends Model_Places_Abstract_Place {
	
	protected static $namelist = Array('Schäbige Bar', 'Dunkle Kaschemme', 'Verfallene Kneipe');
	protected static $description = 'Eigentlich hat sich an diesem Ort mit der Zombieapokalypse nicht allzu viel geändert... an der Bar wird billiges, lauwarmes Bier getrunken und überall tummeln sich torkelnde, übelriechende Gestalten. Eigentlich ist der einzige Unterschied zu früher, dass einem nicht mehr nur die Brieftasche, sondern auch diverse innere Organe bei einem Besuch hier abhanden kommen könnten.';
    protected static $icon = 'bar';
    protected static $outside = false;

    /**
     * @param Model_Combat_Actor[]|null $data
     *
     * @return Model_Combat_Actor[]
     * @throws Exception
     */
    public function modify_spawned_zombies(?array $data): array {
        $data = parent::modify_spawned_zombies($data);
        foreach ($data as &$entry)
            $entry->add_modifier('drunk', random_int(20,100) / 100.0);
        return $data;
    }

    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER): bool
    {
        if (Globals::CurrentPlayerF()->can(
                Interface_Plentity::IC_TRIGGER_SUPPLIES
            )
            && Globals::CurrentGameF()->config('places.bar.spawn_winchester')
            && !Globals::CurrentGameF()->get_npc('winchester')
        ) {
            $winchester = new Model_NPC_Special_Winchester();
            $winchester->location_class($this->uin());
            Globals::CurrentGameF()->add_npc($winchester, 'winchester');
            $this->log()->add(new Model_Log_Types_Movement(Model_Log_Types_Movement::MOVEMENT_TYPE_ENTER, $winchester->id(), true));
        }

        return parent::tick($type);
    }

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room( 5,['inside'])->set_default_state();
        $this->create_new_room(10,['inside'])->set_default_state();
    }
}	