<?php defined('SYSPATH') OR die('No direct access allowed.');

//This is a generic building class that has multiple names; on is randomly selected when this class is constructed
class Model_Places_Weaponshop extends Model_Places_Abstract_Place {
	
	protected static $name = 'Waffenladen';
	protected static $description = 'Dieser Waffenladen war schon so oft das Ziel von Plünderern, dass hier kaum noch etwas zu holen ist. Glücklicherweise gibt es dafür aber auch kaum Zombies hier.';
    protected static $icon = 'gun';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);

		$this->inventory->add(new Model_Items_Vending(get_class($this), "ApocaliCorp. Hunting Supply"));
        $this->inventory->add(new Model_Items_Virtual_Location_Ffgunsmith());
        Model_Blueprints::fast_apply($this, 'items', 'manu_wpn1');
		if (Globals::CurrentGame()->config('modules.armory'))
            Model_Blueprints::fast_apply($this, 'items', 'manu_wpn2');

        return $t;
	}
}	