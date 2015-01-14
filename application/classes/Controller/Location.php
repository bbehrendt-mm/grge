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

    /**
     * @param Model_Blueprints $blueprints
     * @return mixed
     */
    private function compile_builder($blueprints) {
        /** @global Model_Player $player */
        global $player;

        // Translate stuff
        $data = $blueprints->compile($player->location()->get_upgrades(), $player);
        foreach ($data as &$blueprint) {
            foreach (['name','description'] as $key)
                $blueprint[$key] = __($blueprint[$key]);
            foreach (['material_in','material_out'] as $key)
                foreach ($blueprint[$key] as &$material)
                    $material['name'] = __($material['name']);
        }
        return $data;
    }

    private function combine_blueprints($sub) {
        /** @global Model_Player $player */
        global $player;

        $ret = Model_Blueprints::factory();
        foreach (Tool_System::get_class_hierarchy($player->location()) as $name) {
            $name = str_replace('Model_Places_','',$name, $n);
            if ($n == 1 && $b = Tool_System::simple_config("blueprints/{$sub}/" . $name))
                /** @var Model_Blueprints $b */
                $ret->merge($b,true);
        }

        return $ret;
    }

    /**
     * @param Model_Blueprints $blueprints
     * @param string $bid
     */
    private function exec_build($blueprints, $bid) {
        /** @global Model_Player $player */
        global $player;

        $r = $blueprints->execute($bid, $player, $player->location()->get_upgrades());
        $this->add_data('result', $r);
        if (is_array($r))
            $player->location()->add_upgrades($r);
        $this->render_notifications();
    }

    public function japi_builder() {
        /** @global Model_Player $player */
        global $player;

        $blueprints = $this->combine_blueprints('upgrades');

        if ($build = $this->request->post('build'))
            $this->exec_build($blueprints, $build);

        $this->add_data('blueprints', $this->compile_builder($blueprints));
        $this->add_data('energy', $player->stats_get(Model_Player::MP_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_maker() {
        /** @global Model_Player $player */
        global $player;

        $blueprints = $this->combine_blueprints('items');
        $externals = $this->combine_blueprints('upgrades')->externalize();

        if ($build = $this->request->post('build'))
            $this->exec_build($blueprints, $build);

        $blueprints->merge($externals)->validate();

        $this->add_data('blueprints', $this->compile_builder($blueprints));
        $this->add_data('energy', $player->stats_get(Model_Player::MP_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_fighter() {
        /** @global Model_Player $player */
        global $player;

        $blueprints = $this->combine_blueprints('attack');
        $externals = $this->combine_blueprints('upgrades')->externalize();

        if ($build = $this->request->post('build'))
            $this->exec_build($blueprints, $build);

        $blueprints->merge($externals)->validate();

        $this->add_data('blueprints', $this->compile_builder($blueprints));
        $this->add_data('energy', $player->stats_get(Model_Player::MP_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }
}