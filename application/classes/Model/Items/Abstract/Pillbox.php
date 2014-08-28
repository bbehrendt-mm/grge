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
            ->add_action('Ganze Schachtel schlucken', Model_Action::factory()->effect($this->create_effect(true)))
            ->add_action(static::$singular_name . ' zusammenführen',
                Model_Action::factory()
                    ->condition(function() use ($php53pb) {
                            /** @var Model_Items_Abstract_Pillbox $php53pb */
                            return !$php53pb->is_stack_full();
                        })
                    ->fail_message('Es passen keine weiteren Kapseln mehr in diese Schachtel.')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function($p) use ($php53pb) {
                                /**
                                 * @var Model_Items_Abstract_Pillbox $php53pb
                                 * @var Model_Player $p
                                 */
                                $before = $php53pb->count();
                                $php53pb->merge();
                                if ($before == $php53pb->count())
                                    $p->log()->add(new Model_Log_Types_Text(null, null, 'Hier liegen keine weiteren Kapseln, die du in diese Schachtel legen könntest.'));
                                elseif ($php53pb->is_stack_full())
                                    $p->log()->add(new Model_Log_Types_Text(null, null, 'Mit all den anderen Kapseln konntest du diese Schachtel füllen. Sie enthält nun :max Kapseln.', array(':max' => $php53pb->stack_max_size())));
                                else
                                    $p->log()->add(new Model_Log_Types_Text(null, null, 'Du sammelst alle Kapseln die du dabei hast in dieser Schachtel. Sie ist zwar nicht voll, enthält nun aber immerhin :num Kapseln.', array(':num' => $php53pb->count())));
                            })
                        )
            )
            ->add_action(static::$singular_name . ' trennen',
                Model_Action::factory()
                    ->condition(function($p, $s, $a) use ($php53pb) {
                        /** @var Model_Items_Abstract_Pillbox $php53pb */
                        if (!is_numeric($a)) return false;
                        return ($php53pb->count() > $a && $a > 0);
                    })
                    ->fail_message('So viele Kapseln kannst du nicht aus der Box nehmen.')
                    ->argument('Bitte gib an, wie viele Kapseln du aus der Packung nehmen möchtest.')
                    ->effect(
                        Model_Effect::factory()
                            ->custom(function($p, $a) use ($php53pb) {
                                /**
                                 * @var Model_Items_Abstract_Pillbox $php53pb
                                 * @var Model_Player $p
                                 */

                                $php53pb->consume($a);
                                $s = get_class($php53pb);
                                $p->location()->inventory()->add(new $s($a));

                                if ($a == 1) $p->log()->add(new Model_Log_Types_Text(null, null, 'Du hast eine Kapsel aus der Verpackung genommen.'));
                                else $p->log()->add(new Model_Log_Types_Text(null, null, 'Du hast :num Kapseln aus der Verpackung genommen.', array(':num' => $a)));
                            })
                    )
            );
    }

    private function create_effect($full = false) {
        $tmp = Model_Effect::factory()
            ->consume($this, $full);
        foreach (static::$pill_effects as $stat => $dif)
            $tmp->effect($stat, $dif * ($full ? $this->count : 1));

        if ($full)
            $tmp->message('Wozu lange mit Kleinigkeiten aufhalten? Beipackzettel lesen und Medikamente dosieren kosten doch nur Zeit. Viel hilft viel, also runter mit der ganzen Schachtel!');
        else $tmp->message(static::$take_msg . ' ' . (($this->count > 2) ? 'Jetzt sind noch :num Pillen in der Schachtel.' : (($this->count == 2) ? 'In der Schachtel ist nur noch eine Pille. Setze sie mit Bedacht ein!' : 'Die Schachtel ist leer!')), array(':num' => $this->count() - 1));

        return $tmp;
    }
}	