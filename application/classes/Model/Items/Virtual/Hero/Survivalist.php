<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Hero_Survivalist extends Model_Items_Abstract_Virtual {

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

    protected function hid() {
        return parent::hid()
            ->add_action('Glücksfund', Model_Action::factory()
                ->buttonskin('hero hja')
                ->description('Du findest auf der Stelle zwischen 1 und 3 Gegenstände.')
                ->effect(
                    Model_Effect::factory()
                        ->custom(function($p) {
                            /** @var Model_Player $p */
                            $c = random_int(1,10);
                            $n = 0;
                            if ($p->location()->find_item(true)) $n++;
                            if ($c > 6) if ($p->location()->find_item(true)) $n++;
                            if ($c > 9) if ($p->location()->find_item(true)) $n++;

                            if ($n == 0) $p->log()->add('Pech gehabt... hier scheinst du nichts finden zu können. Da hast du deine Heldentag wohl verschenkt.');
                            elseif ($n == 1) $p->log()->add('Na, so ein Glück! Du hast plötzlich und unerwartet einen Gegenstand gefunden! Wo kam der nur her?');
                            else $p->log()->add(new Model_Log_Types_String(null,'Na, so ein Glück! Da drehst du dich nur mal kurz um, schon liegen vor dir :num Gegenstände! Wo die wohl hergekommen sind?', array(':num' => $n)));
                        })
                )
            , 'hero_job_0')
            ->add_action('Instinkt', Model_Action::factory()
                ->buttonskin('hero hja')
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
    }
}	