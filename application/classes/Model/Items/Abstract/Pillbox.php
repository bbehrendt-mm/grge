<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Items_Abstract_Pillbox extends Model_Items_Abstract_Stackable {
	
	protected static $weight = 1;
	protected static $max_size = 10;
	protected static $autoappender = Array('Kapsel', 'Kapseln');
    protected static $take_msg = '';
    protected static $singular_name = '';

    protected static $pill_effects = Array();

    protected function hid() {
        $php53pb = $this;
        return parent::hid()
            ->add_action('Eine ' . static::$singular_name . ' schlucken', Model_Action::factory()
                    ->export('succ')
                    ->decider(function($p) {
                        /**
                         * @var Model_Player $p
                         * @global Model_Game $game
                         */
                        global $game;
                        return $game->tumble($p->id()) ? 'fail' : 'succ';
                    })
                    ->effect(
                        Model_Effect::factory()
                            ->consume($this)
                            ->message('Die Pille ist dir aus der Hand gerutscht, heruntergefallen und weggekugelt! Vielleicht solltest du deinen Alkoholkonsum zügeln ... ')
                    , 'fail')
                    ->effect($this->create_effect(false), 'succ')
            )
            ->add_action('Ganze Schachtel schlucken', Model_Action::factory()->effect($this->create_effect(true)));
    }

    private function create_effect($full = false) {
        /** @global Model_Player $player */
        global $player;
        $tmp = Model_Effect::factory()
            ->consume($this, $full);
        foreach (static::$pill_effects as $stat => $dif)
            $tmp->effect($stat, $dif * ($full ? $this->count : 1) * ($player->buff_retr('tr_dealer') ? 1.2 : 1));

        if ($full)
            $tmp->message('Wozu lange mit Kleinigkeiten aufhalten? Beipackzettel lesen und Medikamente dosieren kosten doch nur Zeit. Viel hilft viel, also runter mit der ganzen Schachtel!');
        else $tmp->message(static::$take_msg . ' ' . (($this->count > 2) ? 'Jetzt sind noch :num Pillen in der Schachtel.' : (($this->count == 2) ? 'In der Schachtel ist nur noch eine Pille. Setze sie mit Bedacht ein!' : 'Die Schachtel ist leer!')), array(':num' => $this->count() - 1));

        return $tmp;
    }
}	