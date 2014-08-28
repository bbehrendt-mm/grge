<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Chem extends Model_Items_Abstract_Item implements Interface_Static {

	protected static $static_info = Array(
			'name' => 'Eine Chemikalie',
			'icon' => 'chem/chem1',
			'description' => 'Dieses kleine Fläschchen enthält eine hochkomplizierte chemische Verbindung, die so cool ist, dass du ihren Namen nicht mal bei Wikipedia findest. Teste doch mal was passiert, wenn du das Zeug über irgendeinen deiner Gegenstände kippst, oder mit einer anderen Substanz mischt!',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $instances_info = Array(
			Array(	'name' => 'Farbige Substanz (Milosat)',			'icon' => 'chem/chem1'),
			Array(	'name' => 'Farbige Substanz (Karmigol)',		'icon' => 'chem/chem2'),
			Array(	'name' => 'Farbige Substanz (Neotrigin)',		'icon' => 'chem/chem3'),
			Array(	'name' => 'Farbige Substanz (Limosuptin)',		'icon' => 'chem/chem4'),
			Array(	'name' => 'Farbige Substanz (Gatonoptium)',		'icon' => 'chem/chem5'),
			Array(	'name' => 'Farbige Substanz (Betakosimtat)',	'icon' => 'chem/chem6'),
	);

    protected function hid() {
        return parent::hid()
            ->add_action('Experimentieren ...', Model_Action::factory()
                    ->javascript(
                        Model_Javascript::factory()
                            ->close_qtip()
                            ->versa('chemlab')
                    )
            );
    }
	
	protected static $weight = 0.2;
	
	public function __construct($target = null) {
		parent::__construct();
		
		if ($target === NULL) $this->type = mt_rand(0, 1);
		else $this->type = $target - 1;
	}
	
	
	public function chem_value() {
		return $this->type + 1;
	}

	public function mixchem($chemval) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
		global $game, $player;

		$this->type += $chemval;
		
		if ($this->type > 6) {
			$damage = -20 - ($this->type - 6) * 9;
			$drunk = ($this->type - 6) * 20;
			
			$game->stats(Model_Game::MGLS_Health, $damage);
			$game->stats(Model_Game::MGLS_Drunk, $drunk);
			
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du mischt beide Chemikalien zusammen. Mit einem Schlag gibt es einen lauten Knall, das Reagenzglas zerspringt und du findest dich in einer bestialisch stinkenden Wolke wieder. Diese beiden Stoffe zu mischen scheint keine allzu gute Idee gewesen zu sein...'));
			$this->consume();
			return false;
		} else {
			$player->log()->add(new Model_Log_Types_Text(null, null, 'Du mischt beide Chemikalien zusammen. Es blubbert ein wenig, aber nachdem sich die Blasen gelegt haben stellst du fest, dass du soeben ein Fläschchen mit :result hergestellt hast! Herzlichen Glückwunsch!', array(), array(':result' => $this->name())));		
			return true;
		}
	}
}	