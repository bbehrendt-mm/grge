<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Meds extends Model_Items_Abstract_Stackable {

	protected static $static_info = Array(
			'name' => 'Glas mit bunten Pillen',
			'icon' => 'meds',
			'description' => 'Der Apotheker, der dieses Glas gefüllt hat, scheint kein Freund von Ordnung gewesen zu sein. Es ist unmöglich zu wissen, was diese Pillen machen - es sei denn, du bist Mediziner.',
			'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
	);
	
	protected static $weight = 6;
    protected static $max_size = 15;
    protected static $autospawn = Array(2,15);
    protected static $autoappender = Array('Pille', 'Pillen');


    private $effects = array();
    private $label;

    public function __construct($type = null) {
        parent::__construct($type = null);

        while (count($this->effects) < 3) {
            $next = mt_rand(1,6);
            if (!isset($this->effects[$next]))
                $this->effects[$next] = mt_rand(-20,35);
        }
    }

    private function create_action() {
        global $player;
        $ret = Model_Action::factory();
        $eff = Model_Effect::factory()
            ->consume($this)
            ->message('Auf gut Glück ganze Gläser voll mit Pillen schlucken hat noch nie jemandem geschadet - zumindest niemandem, der noch am Leben ist...')
            ->achieve(Model_Achievement::MA_PILL_EATER)
            ->buff('Model_Buffs_Drug1', false, 48);
        $sha = Model_Effect::factory()
            ->buff('Model_Buffs_Drug1', false, 48);
        $c = 0;
        foreach ($this->effects as $stat => $dif) {
            $c++;
            $eff->effect($stat, $dif);
            if ($player->job(10030)) {
                if ($player->job(10030, $c*3, false))
                    $sha->effect($stat, $dif);
                else $sha->ambiguous_effect($stat);
            } else $sha->ambiguous_effect();
        }

        $ret->show_as($sha)->effect($eff);
        return $ret;
    }

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Schlucken', $this->create_action())
            ->add_action('Beschriften...',
                Model_Action::factory()
                    ->argument('Was möchtest du auf das Glas schreiben?')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function($p, $a) use ($php53pb) {
                                /**
                                 * @var Model_Items_Meds $php53pb
                                 * @var Model_Player $p
                                 */

                                $php53pb->interaction_label($a);
                            })
                    )
            );
    }

    public function interaction_label($arg) {
        global $game, $player;

        $arg = substr(preg_replace('/[^0-9a-zA-ZäöüÄÖÜ\+\-&.,:% _]+/i', ' ', $arg), 0, 24);
        $this->label = $arg;

        if ($this->label == '') $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast die alte Beschriftung weggewischt.'));
        else $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast die alte Beschriftung weggewischt und ":label" auf die Flasche geschrieben.', array(':label' => $this->label)));

        return true;
    }

    public function label() {
        return $this->label;
    }
}	