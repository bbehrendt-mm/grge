<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Epic_Garden extends Model_Items_Abstract_Virtual {

    protected static $manual_ui = false;

    protected $remaining = array(
        'plant' => PHP_INT_MAX
    );

    const FERTILIZER_FOOD = 1;
    const FERTILIZER_DRUGS = 2;
    const FERTILIZER_CHEM = 3;
    const FERTILIZER_ZOMBIE = 4;

    private $quality;
    private $fertilizer;
    private $planted = false;
    private $next_watering_begin;
    private $next_watering_end;
    private $harvest_at;

    public function get_planted_state() {
        return $this->planted;
    }

    public function get_times() {
        /** @global Model_Game $game */
        global $game;

        return $this->planted ? (($game->duration() > $this->harvest_at || $this->next_watering_begin > $this->harvest_at) ? [false,false,] : []) : [false,false,false];
    }

    protected function hid() {
        /** @global Model_Game $game */
        global $game;

        $hid = parent::hid();

        if (!$this->planted)
            $hid->add_action('Beet bepflanzen', Model_Action::factory()
                ->buttonskin('epic_garden')
                ->description('Bringe die Saat in deinem kleinen Gewächshaus aus, damit du in 24 Stunden ernten kannst. Denke daran, dass du ab dem Aussähen alle 3 Stunden gießen musst, um eine optimale Ernte einfahren zu können.')
                ->effect(Model_Effect::factory()
                    ->custom(function() {
                        /** @global Model_Game $game */
                        global $game;

                        $this->planted = true;
                        $this->quality = 0.5;
                        $this->fertilizer = [];

                        $this->next_watering_begin = $game->duration() + 30;
                        $this->next_watering_end = $this->next_watering_begin + 12;
                        $this->harvest_at = $game->duration() + 288;
                    })
                )
            , 'plant');
        else {

            if ($game->duration() > $this->harvest_at)

                $hid->add_action('Ernten', Model_Action::factory()
                    ->buttonskin('epic_garden')
                    ->description('Endlich ist es zeit, die Früchte deiner Arbeit zu ernten. Beeil dich lieber, sonst verdorren sie.')
                    ->effect(Model_Effect::factory()
                        ->custom(function() {
                            /** @global Model_Game $game */
                            global $game;

                        })
                    )
                    , 'plant');

            elseif ($game->duration() > $this->next_watering_begin)
                $hid->add_action('Gießen', Model_Action::factory()
                    ->buttonskin('epic_garden')
                    ->description('Wenn du deine Pflanzen nicht rechtzeitig und regelmäßig gießt, sinkt ihre Qualität oder die vertrocknen ganz.')
                    ->effect(Model_Effect::factory()
                        ->custom(function() {
                            /** @global Model_Game $game */
                            global $game;

                            if ($game->duration() < $this->next_watering_end)
                                $this->quality += 0.08;

                            $this->next_watering_begin = $game->duration() + 30;
                            $this->next_watering_end = $this->next_watering_begin + 12;
                        })
                    )
                    , 'plant');




        }

        return $hid;
    }
}	