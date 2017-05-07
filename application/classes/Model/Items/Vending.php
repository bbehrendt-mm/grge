<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Vending extends Model_Items_Abstract_Item {

    /** @var  Model_Factory_Items $factory */
	protected $factory;
    private $chem_rand_type = null;
	
	protected static $static_info = Array(
			'name' => 'Verkaufsautomat',
			'icon' => 'vending',
			'description' => 'Dieser Verkaufsautomat sieht ziemlich heruntergekommen aus. Die Scheibe ist verdreckt und die Beschriftungen der einzelnen Knöpfe sind nicht mehr lesbar. Vielleicht wirfst du einfach mal Geld ein und schaust, ob etwas heraus kommt?',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_MISC,
            'deco' => 5,
	);
	
	protected $basetype;
	protected $basename;
	
	protected static $weight = 120;

	public function __construct($basecfg, $name) {
        $this->basetype = $basecfg;
		$this->basename = $name;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->factory = Model_Factory_Items::read($basecfg, Globals::CurrentGame()->config('game.config.itemset'))->set_decay_factor(0);
		parent::__construct();
	}
	
	public function name() {
		return parent::name() . " (" . $this->basename . ")";
	}

    protected function hid() {
        return parent::hid()
            ->add_action('Geld einwerfen', Model_Action::factory()
                ->deny_for(Interface_Plentity::IC_NPC_ANIMAL)
                ->requirement('Model_Items_Money', 4)
                ->effect(
                    Model_Effect::factory()
                        ->ambiguous_effect()
                        ->achieve(Model_Achievement::MA_CAPITALISM)
                        ->custom(function($p) {
                            $this->vend($p);
                        })
                )
            );
    }
	
	public function vend($player = null) {
        if ($player === null)
            $player = Globals::CurrentPlayer();
		
		$item = $this->factory->nd_spawn();
		if (!$item) $item = new Model_Items_Money(4);
        Tool_Scripts::place_new_item($item, false);
		$player->location()->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_VENDING, $item));
        return true;
	}

    public function mixchem($chemval) {
        if ($this->chem_rand_type === null)
            $this->chem_rand_type = mt_rand(8,12);

        switch ($chemval)
        {
            case 7:
                Globals::CurrentPlayer()->get_status()->modify(Model_Status::MS_STAT_HEALTH, -35);
                Tool_Scripts::chem_reaction(
                    'Du gießt etwas von der Chemikalie in den Münzschlitz... es gibt einen Knall, und der Automat fliegt in die Luft! Du wurdest durch die Explosion verletzt, aber wenigstens hast du ein paar neue gegenstände erhalten...',
                    $chemval,$this, [$this->factory->nd_spawn(),$this->factory->nd_spawn(),$this->factory->nd_spawn(),$this->factory->nd_spawn(),$this->factory->nd_spawn()]);
                $this->consume();
                return true;
            case $this->chem_rand_type:
                Tool_Scripts::chem_reaction(
                    'Du gießt etwas von der Chemikalie in den Münzschlitz... es klickt, und ein Gegenstand fällt aus dem Automaten!',
                    $chemval,$this, $this->factory->nd_spawn());
                return true;
            default:
                Tool_Scripts::chem_reaction(
                    'Du gießt etwas von der Chemikalie in den Münzschlitz... doch nichts geschieht',
                    $chemval,$this);
                return false;
        }
    }
}	