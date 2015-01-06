<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Location extends Controller_Game {
    /**
     * Break a siege
     * @param bool $fight True to fight, false to flee
     */
    private function siege($fight) {
        /**
         * @global $player Model_Player
         */
        global $player;

        if ($player->buff_retr('passout') || $player->buff_retr('fragile') || $player->can_escape()) return;

        if ($player->location()->zombie_pop() > 0)
            $player->location()->break_out($fight);

        $this->japi_data();
    }

    /**
     * Siege API (fight)
     */
    public function japi_fight() {
        $this->siege(true);
    }

    /**
     * Siege API (flee)
     */
    public function japi_flee() {
        $this->siege(false);
    }

    private function compile_builder() {
        /** @global Model_Player $player */
        global $player;

        $ret = Model_Blueprints::factory();
        foreach (Tool_System::get_class_hierarchy($player->location()) as $name) {
            $name = str_replace('Model_Places_','',$name, $n);
            if ($n == 1 && $b = Tool_System::simple_config('blueprints/' . $name))
                /** @var Model_Blueprints $b */
                $ret->merge($b);
        }

        // Translate stuff
        $data = $ret->compile([]);
        foreach ($data as &$blueprint) {
            foreach (['name','message','description'] as $key)
                $blueprint[$key] = __($blueprint[$key]);
            foreach (['material_in','material_out'] as $key)
                foreach ($blueprint[$key] as &$material)
                    $material['name'] = __($material['name']);
        }
        return $data;
    }

    public function japi_builder() {
        /** @global Model_Player $player */
        global $player;

        $this->add_data('blueprints', $this->compile_builder());
        $this->add_data('extensions', $player->location()->get_upgrades());
        $this->render(false);
        return true;
    }
}