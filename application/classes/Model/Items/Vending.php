<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vending extends Model_Items_Abstract_Item {

	protected $factory;
	
	protected static $static_info = Array(
			'name' => 'Verkaufsautomat',
			'icon' => 'vending',
			'description' => 'Dieser Verkaufsautomat sieht ziemlich heruntergekommen aus. Die Scheibe ist verdreckt und die Beschriftungen der einzelnen Knöpfe sind nicht mehr lesbar. Vielleicht wirfst du einfach mal Geld ein und schaust, ob etwas heraus kommt?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
	);
	
	protected $basetype;
	protected $basename;
	
	protected static $weight = 120;

	public function __construct($basecfg, $name) {
		global $game;
        $this->basetype = $basecfg;
		$this->basename = $name;
		
		$this->factory = new Model_Factory_Items($basecfg, 1, $game->config('game.config.itemset'));
		parent::__construct();
	}
	
	public function name() {
		$c = $this->basetype;
		return parent::name() . " (" . $this->basename . ")";
	}

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Geld einwerfen', Model_Action::factory()
                    ->requirement('Model_Items_Money', 4)
                    ->effect(
                        Model_Effect::factory()
                            ->ambiguous_effect()
                            ->achieve(Model_Achievement::MA_CAPITALISM)
                            ->custom(function($p) use ($php53pb) {
                                /** @var Model_Items_Vending $php53pb */
                                $php53pb->vend($p);
                            })
                    )
            );
    }
	
	public function vend($player = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game;
        if ($player === null)
            global $player;
		
		$item = $this->factory->spawn(true);
		Tool_Scripts::place_new_item($item, false);
		$player->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_VENDING, $item));
        return true;
	}
}	