<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Woman extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => ceil($level/3),
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid() {
        $rnd_n = array(
            'die Umwelt',
            'die Kinder',
            'die Gesundheit',
            'die Freiheit',
            'alternative Lebensentwürfe',
            'deine Frisur',
            'süße Kätzchen'
        );
        return parent::hid()
            ->add_action('Oben-Ohne-Protest', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Kostet 20 Energie. Jeder andere Spieler in der Nähe regeneriert 20 Energie und Müdigkeit und erhält den Buff "Aufgeregt" für 15 Minuten. Hat keinen Effekt auf Kinder und Spieler, die gerade beschäftigt sind.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            foreach (Tool_Scripts::at_location($p->location_class(), true, false) as $ps) if ($ps->id() != $p->id()) {
                                if ($ps->job(1080))
                                    $ps->log()->add('Diese Erwachsenen werden auch immer wunderlicher... Gerade hat sich :p das T-Shirt ausgezogen und irgendwas auf ihre Brüste geschrieben, jetzt läuft sie schreiend und wild gestikulierend durch die Gegend. Ob sie von einem Skorpion gestochen wurde...?', array(':p' => $p->name()));
                                elseif (!$ps->get_status()->retrieve('fragile')) {
                                    $ps->get_status()->modify(Model_Status::MS_STAT_ENERGY, 20, Model_Status::MS_STAT_SLEEPY, 20);
                                    new Model_Buffs_Exited($ps->id(), 3);
                                    $ps->log()->add('Oh geil! Anscheinend protestiert :p mal wieder für oder gegen irgendwas. Im Prinzip ist das auch egal, solange sie dabei das T-Shirt nicht wieder anzieht...', array(':p' => $p->name()));
                                }
                            }
                        })
                        ->effect(Model_Status::MS_STAT_ENERGY, -20)
                        ->message('Diese Zombieapokalypse ist schlecht für :subject. Die beste Art gegen sowas zu protestieren, ist sich einen dämlichen Spruch auf die Titten zu schreiben und damit in der Öffentlichkeit herumzurennen! ... naja, zumindest die Aufmerksamkeit deiner Mitspieler hast du damit...', array(), array(':subject' => $rnd_n[random_int(0,count($rnd_n) - 1)]))
                )
            , 'hero_job_0');
    }
}	