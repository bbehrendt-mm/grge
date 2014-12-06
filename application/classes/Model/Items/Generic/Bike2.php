<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Generic_Bike2 extends Model_Items_Abstract_Transport implements Interface_Static {
	
	protected static $static_info = Array(
			'name' => 'Fahrrad',
			'icon' => 'bike2',
			'description' => 'Ein simpler Drahtesel, mit dem du jederzeit überall hin kommst! Achtung: Kann nicht verwendet werden, um Zombieschädel zu spalten. Erstens würdest du es damit kaputt machen, zweitens ist es eh viel zu schwer dafür.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_GEAR,
	);

    protected static $weight = 60;
    protected static $icon_ext = 'png';
    protected static $idea_contest_player = 'ExoticAsAlways';
    protected static $speedup = 0.25;

    /**
     * @param Model_Player $p
     * @return bool
     */
    public function trigger_after($p, $d) {
        if (mt_rand(0,100) < round($d/4)) {
            $this->consume();
            $p->location()->inventory()->add(new Model_Items_Generic_Bike);
            $p->log()->add('So ein Mist... dein Fahrrad ist auf dem Weg hierher kaputt gegangen...');
        }
    }
}	