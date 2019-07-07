<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_StudentPremium extends Model_Items_Abstract_Virtual {

    public function __construct($level = 2) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_premium_0' => $level,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected static $graceful_fail = true;

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Not-OP', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Deine Mitstreiter sind befinden sich and er Schwelle zum Tod? Das ist nichts, was man durch die Entfernung von ein paar unnötigen Organen nicht beheben könnte!')
                ->condition(function(Model_Player $p) {
                    $b = 0;
                    foreach (Tool_Scripts::at_location($p->location_class(), true, true) as $ps)
                        if ($ps->id() !== $p->id() && $ps->get_status()->get(Model_Status::MS_STAT_HEALTH) < 20)
                            $b++;
                    return $b > 0;
                })
                ->fail_message('Hier ist niemand, der deine Hilfe benötigt...')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function(Model_Player $p) {

                            $items = [];
                            $patients = 0;
                            foreach (Tool_Scripts::at_location($p->location_class(), true, true) as $ps)
                                if ($ps->id() !== $p->id() && $ps->get_status()->get(Model_Status::MS_STAT_HEALTH) < 20) {
                                    $items[] = new Model_Items_Organ();
                                    if ($ps->get_status()->get(Model_Status::MS_STAT_HEALTH) < 10) $items[] = new Model_Items_Organ();

                                    $ps->get_status()->set(Model_Status::MS_STAT_HEALTH, 100);
                                    $patients++;
                                }

                            Tool_Scripts::place_new_item( $items );
                            $p->log()->add('Mit deinen geschickten Händen hast du :patients Patienten gerettet und nebenbei noch :organs Organe geerntet!', [':patients' => $patients, ':organs' => count($items)]);
                        })
                )
            , 'hero_job_0');
    }
}	