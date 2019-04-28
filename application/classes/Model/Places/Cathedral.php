<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Cathedral extends Model_Places_Abstract_Place {
	
	protected static $location_name = 'Kathedrale';
	protected static $description = 'Ein Haus Gottes - Zuflucht für die Erschöpften, die Verfolgten und auch für Geistliche, die etwas zu innige Beziehungen mit ihren Ministranten pflegen. Nicht, dass du soetwas je gemacht hättest...<br />Selbst nach der Apokalypse versammeln sich hier die Gläubigen auf der Suche nach Hoffnung. Wobei die Gläubigen in diesem speziellen Fall leider relativ untot sind, und "Hoffnung" die Hoffnung auf etwas zu fressen meint.';
    protected static $icon = 'cathedral';
    protected static $outside = false;

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
		
		$count = random_int(5,20);
		for ($i = 0; $i < $count; $i++) $this->inventory->add(new Model_Items_Body('Zerfetztes Gemeindemitglied', 'Es gibt Momente, da kann der Glaube Berge versetzen und selbst die größten Probleme klein erscheinen lassen. Und dann gibt es Momente, in denen sollte man seine Gebete lieber beim Laufen sprechen, anstatt starr auf einer Kirchenbank zu verharren!'));
	    return $t;
    }

    public function setup_additional_rooms(): void
    {
        parent::setup_additional_rooms();
        $this->create_new_room(10,['inside'])->set_default_state();;
        $this->create_new_room(50,['inside'])->set_default_state();;
    }
}	