<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Weaponshop extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Waffenladen';
	protected static $description = 'Dieser Waffenladen war schon so oft das Ziel von Plünderern, dass hier kaum noch etwas zu holen ist. Glücklicherweise gibt es dafür aber auch kaum Zombies hier.';
    protected static $icon = 'gun';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);

		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'ApocaliCorp. Hunting Supply'
        ));

		return $t;
	}

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room( 5,['inside']);

        $this->setup_new_room($this->create_new_room(10,['inside']),
                              Globals::CurrentGameF()->config('modules.armory') ? ['workshop','workshop_weapons_1','workshop_weapons_2'] : ['workshop','workshop_weapons_1'],
                              [],
            'Hinterraum'
        );
    }
}
