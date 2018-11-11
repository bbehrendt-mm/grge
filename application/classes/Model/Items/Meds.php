<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Meds extends Model_Items_Abstract_Stackable implements Interface_Label {

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
            $next = random_int(1,6);
            if (!isset($this->effects[$next]) && ($v = random_int(-20,35)))
                $this->effects[$next] = $v;
        }
    }

    private function create_action() {
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
            $eff->effect($stat, $dif * (Globals::PrimaryPlayerF()->get_status()->retrieve('tr_dealer') ? 1.2 : 1));
            if (!Globals::shadowPlayerExists() && Globals::PrimaryPlayerF()->job(10030)) {
                if (Globals::PrimaryPlayerF()->job(10030, $c*3, false))
                    $sha->effect($stat, $dif * (Globals::PrimaryPlayerF()->get_status()->retrieve('tr_dealer') ? 1.2 : 1));
                else $sha->ambiguous_effect($stat);
            } else $sha->ambiguous_effect();
        }

        $ret->show_as($sha)->effect($eff);
        return $ret;
    }

    protected function hid() {
        return Tool_Scripts::is_npc() ? parent::hid() : parent::hid()
            ->add_action('Schlucken', $this->create_action());
    }

    public function label() {
        return $this->label;
    }

    public function set_label($new_text) {
        $new = (bool)$this->label;
        $this->label = mb_substr($new_text, 0, 20);

        if ($this->label == '') Globals::PrimaryPlayerF()->log()->add('Du hast die Beschriftung auf diesem Gegenstand weggewischt.');
        elseif (!$new) Globals::PrimaryPlayerF()->log()->add('Du hast diesen Gegenstand mit einer Beschriftung versehen.');
        else Globals::PrimaryPlayerF()->log()->add('Du hast die Beschriftung dieses Gegenstands geändert.');
    }
}