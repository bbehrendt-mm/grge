<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Fruit extends Model_Items_Abstract_Stackable {

	protected static $static_info = Array(
			'name' => 'Geerntete Pflanze',
			'icon' => 'fruit',
			'description' => 'Du hast diese Pflanze selbst angebaut und gerade frisch geerntet. Jetzt musst du nur noch hoffen, dass sie zufällig nicht giftig ist.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_FOOD,
	);
	
	protected static $weight = 2;
    protected static $max_size = 5;
    protected static $autospawn = Array(0,0);
    protected static $autoappender = Array('Portion', 'Portionen');


    private $effects = array();

    /**
     * @param array $effects
     * @param int $size
     */
    public function __construct(Array $effects, $size) {
        parent::__construct();

        $this->effects = $effects;
        $this->count = $size;
    }

    private function create_action() {
        $ret = Model_Action::factory();
        $eff = Model_Effect::factory()
            ->consume($this)
            ->message('Du schlingst die Pflanze mit einem Schluck herunter. Schmeckt eigentlich gar nicht so furchtbar... zumindest im Vergleich mit dieser verfaulten Leiche, die du gestern gegessen hast.');

        foreach ($this->effects as $stat => $dif)
            $eff->effect($stat, $dif);

        $ret->effect($eff);
        return $ret;
    }

    protected function hid() {
        return parent::hid()->add_action('Verschlingen', $this->create_action());
    }
}