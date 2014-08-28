<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Store extends Model_Places_Abstract_Place {

    protected static $namelist = Array('Tante Emma Laden', 'Kleiner Discounter', 'Kleiner Markt', 'Mini-Markt', 'Kleines Geschäft', 'Laden');
	protected static $description = 'Dies ist die Gelegenheit für dich, das absolut billigste Zeug aus einer minimalen Auswahl von Gebrauchsgegenständen und Lebensmitteln zu ergattern! Die vernagelten (und schlecht geputzten) Schaufenster lassen jedoch erahnen, dass dieses Geschäft wohl in nächster Zeit nicht mehr öffnen wird. Vor dem Laden steht ein Marktwagen mit der Aufschrift "Vera Loewenhaupt & Söhne", unter dem eine Leiche liegt...';
    protected static $outside = false;
    protected static $icon = 'store';

	public function uin($uin = NULL) {
		if ($uin === NULL) return parent::uin();
		else $t = parent::uin($uin);
		
		$this->inventory->add(new Model_Items_Vending(get_class($this), "MicroJam Vendor"));
        $this->inventory->add(new Model_Items_Vending2());
        $this->inventory->add(new Model_Items_Virtual_Location_Market());
		$this->inventory->add(new Model_Items_Body('Vera Loewenhaupt', 'Dies muss wohl die Namensgeberin des Wagens sein, unter dem sie liegt... allerdings keine Spur von ihren Söhnen.'));

        return $t;
    }

    protected function create_npcs() {
        global $game;

        $ret = parent::create_npcs();
        if (Tool_Events::current($game->next_tick()) == 'halloween'  && !$game->setting_mode(2000))
            $ret['halloween'] = Model_Npc::factory()->name('Vermodernder Händler')
                ->add_action('Ansprechen', Model_Action::factory()
                        ->effect(Model_Effect::factory()
                                ->message('Guten Abend, werter Kunde! Haben Sie Interesse, GEHIIIIIIIIIIRNE zu erwerben? GEHIIIIRNE sind eine wundervolle Geldanlage und außerdem noch sehr nützlich im täglichen Leben! Nur hier bekommen Sie GEHIIIIIIIRNE zum absoluten Hammerpreis!')
                        )
                )
                ->add_action('Gehirne kaufen', Model_Action::factory()
                        ->requirement('Model_Items_Money', 1)
                        ->show_as(Model_Effect::factory()
                                ->ambiguous_effect()
                        )
                        ->effect(Model_Effect::factory()
                                ->spawn('Model_Items_Brainbox', 1)
                                ->message('Vielen Dank für Ihren Einkauf! Hier sind ihre GEHIIIIRNE. Bitte beehren Sie uns bald wieder!')
                        )
                )
            ;
        return $ret;
    }
}	