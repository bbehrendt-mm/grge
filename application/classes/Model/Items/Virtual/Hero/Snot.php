<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Snot extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => 1,
            'hero_job_1' => $level >= 6 ? 1 : 0,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Spontane Deflation', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Verdoppelt den Wert deines Geldes.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $belt = $p->inventory()->get(Model_Items_Ammobelt::cls());
                            if (!$belt) return;
                            $belt = $belt[0];
                            /** @var $belt Model_Items_Ammobelt */
                            $belt->add(new Model_Items_Money($belt->has(Model_Items_Money::cls())));
                        })
                        ->message('Na sowas! Laut aktuellen Analysen verursacht die Zombieapokalypse eine spontane Deflation an den Finanzmärkten. Damit hat sich der Wert deines Ersparten verdoppelt!')
                )
            , 'hero_job_0')
            ->add_action('Rettungspaket', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Generiert ein Set aus nützlichen Gegenständen.')
                ->effect(
                    Model_Effect::factory()
                        ->spawn(Model_Items_Lunchbox::cls(), 2)
                        ->spawn(Model_Items_Sportsdrink::cls(), 2)
                        ->spawn(new Model_Items_Twinoid(10))
                        ->message('Tja, eigentlich waren diese Waren für hungernde Waisenkinder in Afrika gedacht - aber du brauchst das selbstverständlich sehr viel dringender.')
                )
            , 'hero_job_1');
    }
}	