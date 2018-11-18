<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Student extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => ceil($level/3),
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Sprechstunde', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Jeder mit der Zombiekrankheit infizierte Spieler in der Nähe verliert 25 Infektionspunkte oder 15, wenn du selbst infiziert bist. Die Infektionsrate kann nicht unter 5 fallen. Falls du selbst gesund bist beshteht für jeden infizierten Spieler eine 15% Chance, dich mit der Zombiekrankheit anzustecken.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $b = false;
                            $i = false;

                            $b_heal = ($p->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) <= 0) ? 25 : 15;
                            foreach (Tool_Scripts::at_location($p->location_class(), true, true) as $ps) if ($ps->id() !== $p->id()) if ($ps->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) > 0) {
                                if ($ps->get_status()->has(Model_Status::MS_STAT_ZOMBIFY, $b_heal + 5, Model_Status::MS_EFFECT_ITEM))
                                    $ps->get_status()->modify(Model_Status::MS_STAT_ZOMBIFY, -$b_heal, Model_Status::MS_EFFECT_ITEM);
                                else $ps->get_status()->set(Model_Status::MS_STAT_ZOMBIFY, 5);

                                if (random_int(0,100) < 15 && !$i && $p->get_status()->get(Model_Status::MS_STAT_ZOMBIFY) <= 0) {
                                    $i = true;
                                    $p->get_status()->set(Model_Status::MS_STAT_ZOMBIFY, 5);
                                }
                                $b = true;
                            }

                            if (!$b) $p->log()->add('Pflichtbewusst hälst du deine Sprechstunde ab... aber keiner kommt. Kann es sein, das hier niemand krank ist?');
                            elseif (!$i) $p->log()->add('Es ist immer gut, mit den Patienten zu sprechen. Das macht deren Krankheit direkt weniger schlimm.');
                            else $p->log()->add('Du hättest eventuell bei den Vorlesungen zum Thema "Infektionsgefahr" und "Hygiene" besser zuhören sollen... oder vielleicht einfach deinen Patienten keinen Abschiedskuss geben. Jetzt hast du dir nämlich auch eine Infektion eingefangen. Glückwunsch!');
                        })
                )
            , 'hero_job_0');
    }
}	