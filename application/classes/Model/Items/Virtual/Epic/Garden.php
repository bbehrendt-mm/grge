<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Items_Virtual_Epic_Garden extends Model_Items_Abstract_Virtual {

    protected static $manual_ui = true;

    const FERTILIZER_FOOD = 1;
    const FERTILIZER_DRUGS = 2;
    const FERTILIZER_CHEM = 3;
    const FERTILIZER_ALCOHOL = 4;

    private $quality;
    private $fertilizer;
    private $planted = false;
    private $next_watering_begin;
    private $next_watering_end;
    private $harvest_at;

    public function get_planted_state() {
        return $this->planted;
    }

    public function get_fertilizer_status() {
        return $this->get_planted_state() ? min(10,array_sum($this->fertilizer))/10 : 0;
    }

    private function normalize_fertilizer() {
        $a = [];
        if (!$this->fertilizer) $a =  [];
        elseif (array_sum($this->fertilizer) > 10) {
            $mx = 10/array_sum($this->fertilizer);
            $a = array_map(function($v) use ($mx) {return $v*$mx;}, $this->fertilizer);
        } else $a = $this->fertilizer;

        foreach ([static::FERTILIZER_DRUGS, static::FERTILIZER_ALCOHOL, static::FERTILIZER_FOOD, static::FERTILIZER_CHEM] as $t)
            if (!isset($a[$t])) $a[$t] = 0;

        return $a;
    }

    public function get_harvest_state() {
        /** @global Model_Game $game */
        global $game;
        return $this->planted && ($game->duration() > $this->harvest_at);
    }

    public function get_time_to_harvest() {
        /** @global Model_Game $game */
        global $game;
        return $this->get_harvest_state() ? 0 : ($this->get_planted_state() ? $this->harvest_at - $game->duration() : -1);
    }

    public function get_harvest_prc() {
        return 1 - ($this->get_harvest_state() ? 1 : ($this->get_planted_state() ? ($this->get_time_to_harvest()/288) : 0));
    }

    public function get_water_prc() {
        /** @global Model_Game $game */
        global $game;

        return $this->get_planted_state() ? max(0,$this->next_watering_end - $game->duration())/42 : 0;
    }

    public function get_time_to_water($begin = true) {
        /** @global Model_Game $game */
        global $game;
        return $this->get_harvest_state() ? -1 : (($begin ? $this->next_watering_begin : $this->next_watering_end) - $game->duration());
    }

    private function dryout() {
        /** @global Model_Game $game */
        global $game;

        return max(0, 0.01 * ($game->duration() - $this->next_watering_end));
    }

    public function get_quality() {
        /** @global Model_Game $game */
        global $game;

        return max(0, $this->quality - $this->dryout());
    }

    /**
     * @param Model_Hid $hid
     * @param $id
     * @param $items
     * @param $effect
     */
    private function register_fertilizer(&$hid, $id, $items, $effect) {
        $desc = '[nt]';

        $action = Model_Action::factory()
            ->buttonskin('epic')
            ->flag('as','fertilize')
            ->effect(Model_Effect::factory()
                ->message('Du hast die Pflanzen gedüngt. Mal sehen, was hier jetzt wachsen wird...')
                ->custom(function() use ($effect) {
                    /** @global Model_Game $game */
                    global $game;

                    foreach ($effect as $eid => $ecount)
                        if (!isset($this->fertilizer[$eid])) $this->fertilizer[$eid] = $ecount;
                        else $this->fertilizer[$eid] += $ecount;
                })
            );

        $desc_tmp = [];
        foreach ($items as $class => $count) {
            /** @var Model_Items_Abstract_Item $class */
            $desc_tmp[] = "$count x " . __($class::static_name());
            $action->requirement($class, $count);
        }

        $action->description('[nt]' . __('Benutze folgende Gegenstände, um deine Pflanzen zu düngen: :items', [':items' => implode(', ', $desc_tmp)]));

        $hid->add_action('Düngen', $action, $id);
    }

    protected function hid() {
        /** @global Model_Game $game */
        global $game;

        $hid = parent::hid();

        if (!$this->get_planted_state())
            $hid->add_action('Beet bepflanzen', Model_Action::factory()
                ->buttonskin('epic')
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

            if ($this->get_harvest_state())

                $hid->add_action('Ernten', Model_Action::factory()
                    ->buttonskin('epic')
                    ->description('Endlich ist es zeit, die Früchte deiner Arbeit zu ernten. Beeil dich lieber, sonst verdorren sie.')
                    ->effect(Model_Effect::factory()
                        ->custom(function() {
                            /** @global Model_Player $player */
                            global $player;
                            $count = floor($this->get_quality() * 8);

                            $this->planted = false;

                            if ($count <= 0) {
                                $player->log()->add('Das war wohl nichts... Deine Pflanzen sind total vertrocknet und absolut nutzlos. Da musst du wohl nochmal von vorne beginnen.');
                                return;
                            } else $player->log()->add('Na, da hat sich das warten doch gelohnt. Du hast soeben :num Pflanzen ernten können.', [':num' => $count]);

                            $fertilize = $this->normalize_fertilizer();
                            $level = $this->get_fertilizer_status();

                            $effects = [
                                Model_Player::MP_STAT_HUNGER => round(5 + 5 * $level),
                                Model_Player::MP_STAT_THIRST => round(10 + 4 * $fertilize[static::FERTILIZER_FOOD] * $level),
                                Model_Player::MP_STAT_HEALTH => round(2 * $fertilize[static::FERTILIZER_DRUGS] * $level),
                                Model_Player::MP_STAT_ENERGY => round(3 * $fertilize[static::FERTILIZER_CHEM] * $level),
                                Model_Player::MP_STAT_SLEEPY => round(5 * ($fertilize[static::FERTILIZER_CHEM] + $fertilize[static::FERTILIZER_ALCOHOL]) * $level)
                            ];

                            $player->location()->inventory()->add(new Model_Items_Fruit($effects,$count));
                        })
                    )
                    , 'harvest');

            elseif ($game->duration() >= $this->next_watering_begin)
                $hid
                    ->add_action('Gießen', Model_Action::factory()
                        ->buttonskin('epic')
                        ->requirement('Model_Items_Generic_Waterv',1)
                        ->description('Wenn du deine Pflanzen nicht rechtzeitig und regelmäßig gießt, sinkt ihre Qualität oder die vertrocknen ganz.')
                        ->effect(Model_Effect::factory()
                            ->custom(function() {
                                /** @global Model_Game $game */
                                global $game;

                                $this->quality = max(0, $this->quality - $this->dryout());
                                if ($game->duration() < $this->next_watering_end)
                                    $this->quality = min(1,$this->quality + 0.08);

                                $this->next_watering_begin = $game->duration() + 30;
                                $this->next_watering_end = $this->next_watering_begin + 12;
                            })
                        )
                    , 'water');

            $this->register_fertilizer($hid,'fertilize_bfood', ['Model_Items_Basefood' => 1],       [static::FERTILIZER_FOOD => 1]);
            $this->register_fertilizer($hid,'fertilize_nom',   ['Model_Items_Nom' => 1],            [static::FERTILIZER_FOOD => 3]);
            $this->register_fertilizer($hid,'fertilize_flesh',   ['Model_Items_Fleshfood' => 1],    [static::FERTILIZER_FOOD => 1, static::FERTILIZER_DRUGS => 1]);

            $this->register_fertilizer($hid,'fertilize_pills', ['Model_Items_Pill' => 1],           [static::FERTILIZER_DRUGS => 1]);
            $this->register_fertilizer($hid,'fertilize_box', ['Model_Items_Abstract_Pillbox' => 1], [static::FERTILIZER_DRUGS => 3]);

            $this->register_fertilizer($hid,'fertilize_chem',  ['Model_Items_Chem' => 1],           [static::FERTILIZER_CHEM => 1]);
            $this->register_fertilizer($hid,'fertilize_nutrient',  ['Model_Items_Nutrient2' => 1],  [static::FERTILIZER_CHEM => 1, static::FERTILIZER_FOOD => 1]);

            $this->register_fertilizer($hid,'fertilize_beer',  ['Model_Items_Beer' => 1],           [static::FERTILIZER_ALCOHOL => 1]);
            $this->register_fertilizer($hid,'fertilize_whiskey',  ['Model_Items_Whiskey' => 1],     [static::FERTILIZER_ALCOHOL => 3]);
        }

        return $hid;
    }
}	