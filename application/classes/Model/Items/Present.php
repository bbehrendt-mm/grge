<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Present extends Model_Items_Abstract_Item implements Interface_Autotaker {

	protected static $static_info = Array(
			'name' => 'Geschenk',
			'icon' => 'present',
			'description' => '~',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);
	
	protected static $weight = 0;

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Auspacken', Model_Action::factory()
                    ->effect(
                        Model_Effect::factory()
                            ->ambiguous_effect()
                            ->consume($this)
                            ->custom(function($p) use ($php53pb) {
                                /** @var Model_Items_Present $php53pb */
                                $php53pb->open($p);
                            })
                    )
            );
    }
	
	private $is_awesome;
	private $p_name;
	private $p_desc;
	
	protected static $content_crummy = Array(
		Array('value' => 'Model_Items_Generic_Wood', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Metal', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Sum', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Tube', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Electro', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Cloth', 'chance' => 1),
		Array('value' => 'Model_Items_Generic_Ducttape', 'chance' => 1),
        Array('value' => 'Model_Items_Cookie', 'chance' => 2),
		Array('value' => 'Model_Items_Battery', 'chance' => 2),
		Array('value' => 'Model_Items_Ammo', 'chance' => 2),
		Array('value' => 'Model_Items_Handgun', 'chance' => 1),
		Array('value' => 'Model_Items_Beer', 'chance' => 2),
		Array('value' => 'Model_Items_Whiskey', 'chance' => 2),
		Array('value' => 'Model_Items_Generic_Teddy', 'chance' => 4),
	);
	
	protected static $content_awesome = Array(
			Array('value' => 'Model_Items_Generic_Bed', 'chance' => 1),
			Array('value' => 'Model_Items_Generic_Boiler', 'chance' => 1),
			Array('value' => 'Model_Items_Generic_Motor', 'chance' => 1),
			Array('value' => 'Model_Items_Generic_Table', 'chance' => 1),
			Array('value' => 'Model_Items_Generic_Jerrycan', 'chance' => 1),
			Array('value' => 'Model_Items_Generic_Supercharger', 'chance' => 1),
			Array('value' => 'Model_Items_Generic_Pressure', 'chance' => 1),
			Array('value' => 'Model_Items_Twinoid', 'chance' => 1),
			Array('value' => 'Model_Items_Vedge', 'chance' => 1),
			Array('value' => 'Model_Items_Vedge5', 'chance' => 1),
			Array('value' => 'Model_Items_Pumpkin', 'chance' => 1)
	);
	
	public function __construct($name = 'Geschenk', $desc ='Wer auch immer dir dieses Geschenk hingestellt hat, er meint es wohl gut mit dir!', $awesome = false) {
		parent::__construct();
	
		$this->is_awesome = $awesome;
		$this->p_name = $name;
		$this->p_desc = $desc;
	}
	
	public function name() {
		return $this->p_name;
	}
	
	public function description() {
		return $this->p_desc;
	}	
	
	public function icon() {	
		if ($this->is_awesome) return  '/application/assets/icons/items/present/big.gif'; 
		else return  '/application/assets/icons/items/present/small.gif'; 
	}	
	
	public function open($player = null) {
		/** @global Model_Player $player */
        if ($player === null)
            global $player;
				
		$classname = Tool_Gambling::roulette(($this->is_awesome) ? static::$content_awesome : static::$content_crummy);
		if (!class_exists($classname)) throw new Exception("Item Class '$classname' is not valid!", 1);

        /** @var Model_Items_Abstract_Item $item */
        $item = new $classname;
		$player->location()->inventory()->add($item);

		$player->log()->add(new Model_Log_Types_Text(null, null, 'Du kannst deine Neugier kaum bremsen und reißt das Geschenk auseinander. Im Inneren befindet sich ein/eine :item! Hurra!', array(), array(':item' => $item->name())));
		return true;
	}

}	