<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Survivalist extends Model_Items_Abstract_Virtual {

    protected $wunderkind;

    public function __construct($level = 1, bool $as_wunderkind = false) {
        parent::__construct();
        $this->wunderkind = $as_wunderkind;
        $actions = $level >= 10 ? 5 : 1;
        $this->remaining = array(
            'hero_job_0' => $actions,
            'hero_job_1' => $level >= 6 ? $actions : 0,
        );
    }

    protected static $static_info = Array(
        'name' => 'Heldentaten',
    );

    protected function hid(): Model_Hid {
        $str = $this->wunderkind && Globals::CurrentPlayerActual()->get_status()->get(Model_Status::MS_STAT_DRUNK) >= 15 ? 'hero action-drunk hja' : 'hero hja';

        $hid = parent::hid();
        $hid->add_action('Glücksfund', Model_Action::factory()
                ->buttonskin($str)
                ->description('Du findest auf der Stelle zwischen 1 und 3 Gegenstände.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $c = random_int(1,10);
                            $items = [];
                            if ($item = $p->location()->find_item(true, true)) $items[] = $item;
                            if ($c > 6) if ($item = $p->location()->find_item(true, true)) $items[] = $item;
                            if ($c > 9) if ($item = $p->location()->find_item(true, true)) $items[] = $item;

                            Tool_Scripts::place_new_item($items);

                            if (count($items) === 0) $p->log()->add('Pech gehabt... hier scheinst du nichts finden zu können. Da hast du deine Heldentag wohl verschenkt.');
                            elseif (count($items) === 1) $p->log()->add('Na, so ein Glück! Du hast plötzlich und unerwartet einen Gegenstand gefunden! Wo kam der nur her?');
                            else $p->log()->add(new Model_Log_Types_String(null,'Na, so ein Glück! Da drehst du dich nur mal kurz um, schon liegen vor dir :num Gegenstände! Wo die wohl hergekommen sind?', array(':num' => count($items))));

                            if (count($items) > 0 && $this->wunderkind && Tool_Gambling::tumble($p)) {

                                $use = false;

                                /** @var Model_Items_Abstract_Item $current_item */
                                foreach ($items as $current_item) {

                                    $actions = $current_item->simple_effects($p,true);
                                    if (count($actions) > 0) {

                                        $action = Tool_Gambling::select(array_keys($actions));
                                        if ($current_item->test_interaction($action,$p)) {
                                            Controller_Act::code_item($current_item->uin(), $action);
                                            $use = true;
                                        }

                                    }

                                }

                                if ($use)
                                    $p->log()->add(count($items) > 1 ?
                                                       '... und wenn du gerade klar denken könntest, hättest du die frisch gefundenen Gegenstände vermutlich nicht direkt verwendet.' :
                                                       '... und wenn du gerade klar denken könntest, hättest du den frisch gefundenen Gegenstand vermutlich nicht direkt verwendet.' );

                            }
                        })
                )
            , 'hero_job_0');

        if (!$this->wunderkind)
            $hid->add_action('Instinkt', Model_Action::factory()
                ->buttonskin($str)
                ->description('Dein Instinkt hilft dir, bereits leergesuchte Ruinen teilweise wieder aufzufüllen.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $p->location()->hero_replensish();
                        })
                        ->message('Nur, weil du an einem Ort bereits einen Gegenstand gefunden hast, bedeutet das nicht, dass dort nicht vielleicht noch ein Zweiter liegt. Dank dieser genialen Erkenntnis kannst du hier nun wieder Items finden.')
                )
            , 'hero_job_1');

        return $hid;
    }
}	