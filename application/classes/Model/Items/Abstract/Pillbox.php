<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Pillbox extends Model_Items_Abstract_Stackable {
	
	protected static $weight = 1;
	protected static $max_size = 10;
	protected static $autoappender = Array('Kapsel', 'Kapseln');
    protected static $take_msg = '';
    protected static $singular_name = '';

    protected static $static_info = Array(
        'name' => 'Schachtel mit Pillen',
        'icon' => 'paralaxium',
        'description' => '',
        'category' => Model_Items_Abstract_Item::MIAI_CAT_DRUG,
    );

    protected static $pill_effects = Array();

    protected function hid() {
        return parent::hid()
            ->add_action('Eine ' . static::$singular_name . ' schlucken', Model_Action::factory()
                    ->export('succ')
                    ->decider(function($p) {
                        /**
                         * @var Model_Player $p
                         */
                        return Tool_Gambling::tumble($p) ? 'fail' : 'succ';
                    })
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->message('Die Pille ist dir aus der Hand gerutscht, heruntergefallen und weggekugelt! Vielleicht solltest du deinen Alkoholkonsum zügeln ... ')
                    , 'fail')
                    ->effect($this->create_effect(false), 'succ')
            )
            ->add_action('Ganze Schachtel schlucken', Model_Action::factory()->effect($this->create_effect(true))->allow_auto(false));
    }

    private function create_effect($full = false) {
        $tmp = Model_Effect::factory()
            ->consume($this, $full);
        foreach (static::$pill_effects as $stat => $dif)
            $tmp->effect($stat, $dif * ($full ? $this->count : 1) * (Globals::CurrentPlayer()->get_status()->retrieve('tr_dealer') ? 1.2 : 1));

        if ($full)
            $tmp->message('Wozu lange mit Kleinigkeiten aufhalten? Beipackzettel lesen und Medikamente dosieren kosten doch nur Zeit. Viel hilft viel, also runter mit der ganzen Schachtel!');
        else $tmp->message(static::$take_msg . ' ' . (($this->count > 2) ? 'Jetzt sind noch :num Pillen in der Schachtel.' : (($this->count == 2) ? 'In der Schachtel ist nur noch eine Pille. Setze sie mit Bedacht ein!' : 'Die Schachtel ist leer!')), array(':num' => $this->count() - 1));

        return $tmp;
    }
}	