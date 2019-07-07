<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Cathedral2 extends Model_Places_Abstract_Hideout {
	
	protected static $location_name = 'Sixtinische Kapelle';
	protected static $description = 'Diese gigantische Kathedrale eignet sich großartig als Versteck vor Zombies sowie der Strafverfolgung. Bei Gelegenheit solltest du allerdings die Leichen entfernen ...';
    protected static $icon = 'cathedral';
    protected static $outside = false;

    //Base deco value
    protected static $base_deco_value = 200;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
		
		$count = random_int(20,30);
		for ($i = 0; $i < $count; $i++)  {
		    $item = new Model_Items_Body('Zerfetztes Gemeindemitglied', 'Es gibt Momente, da kann der Glaube Berge versetzen und selbst die größten Probleme klein erscheinen lassen. Und dann gibt es Momente, in denen sollte man seine Gebete lieber beim Laufen sprechen, anstatt starr auf einer Kirchenbank zu verharren!');
		    $item->set_enable_achievement( false );
		    $this->inventory->add( $item );
        }
	    return $t;
    }

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside'])->set_default_state();;
        $this->create_new_room(50,['inside'])->set_default_state();;
        $this->create_new_room(50,['inside'])->set_default_state();;
        $this->create_new_room(50,['inside'])->set_default_state();;
        $this->create_new_room(50,['inside'])->set_default_state();;
        $this->create_new_room(50,['inside'])->set_default_state();;
    }
}	