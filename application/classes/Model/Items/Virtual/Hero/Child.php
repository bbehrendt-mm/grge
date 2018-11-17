<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Child extends Model_Items_Abstract_Virtual {

    public function __construct($level = 1) {
        parent::__construct();
        $this->remaining = array(
            'hero_job_0' => 4,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        return parent::hid()
            ->add_action('Quengeln', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Entzieht jedem anwesenden Spieler 10% seiner Durst- und Hungerleiste und fügt diese den eigenen Leisten hinzu.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $ha = $ta = 0;
                            foreach (Tool_Scripts::at_location($p->location_class(), true, false) as $ps) if ($ps->id() != $p->id()) {
                                $h = floor($ps->get_status()->get(Model_Status::MS_STAT_HUNGER)/10);
                                $t = floor($ps->get_status()->get(Model_Status::MS_STAT_THIRST)/10);
                                $ha+=$h; $ta+=$t;
                                $ps->get_status()->modify(Model_Status::MS_STAT_HUNGER, -$h, Model_Status::MS_STAT_THIRST, -$t);
                                $ps->log()->add(':p ist schon wieder am quengeln... du hast ihm etwas von deiner Nahrungs- und Wasserration gegeben, damit er endlich die Klappe hält.', array(':p' => $p->name()));
                            }

                            $p->get_status()->modify(Model_Status::MS_STAT_HUNGER, $ha, Model_Status::MS_STAT_THIRST, $ta);
                            if ($ha+$ta > 0)
                                $p->log()->add('Und es hat wieder geklappt! Du hast dir etwas zu essen und zu trinken ergaunert.');
                            else $p->log()->add('Obwohl du dir die Seele aus dem leib geschriehen hast, bekommst du nichts. Mist...');
                        })


                )
            , 'hero_job_0');
    }
}	