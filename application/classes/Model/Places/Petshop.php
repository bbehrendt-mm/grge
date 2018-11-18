<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Petshop extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Zoohandlung';
	protected static $description = 'In dieser Zoohandlung konnte man früher kuschelige Tiere kaufen... inzwischen sind die Käfige jedoch leer. Dafür liegen allerlei menschliche Skelette in der Gegend herum. Wo die wohl herkommen?';
    protected static $icon = 'petshop';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);

        Tool_Gambling::repeat(1, 3, function() {$this->inventory->add(new Model_Items_Bone());});
        Tool_Gambling::repeat(1, 3, function() {$this->inventory->add(new Model_Items_Bone2());});
        Tool_Gambling::repeat(2, 5, function() {$this->inventory->add(new Model_Items_Generic_Bone3());});

		$this->inventory->add(new Model_Items_Vending(get_class($this),
            'Pettington 40K'
        ));
        return $t;
	}

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside']);
        $this->create_new_room(10,['inside']);
    }
}	