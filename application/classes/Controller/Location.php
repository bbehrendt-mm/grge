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

    public function japi_scout() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if ($player->buff_retr('fragile')) return;

        if ($game->config('modules.mapping') && $player->inventory()->get('Model_Items_Maptool') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Xmas') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Hideout') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Node')) {
            /** @var Model_Items_Maptool $mapper */
            $mapper = $player->inventory()->get('Model_Items_Maptool'); $mapper = $mapper[0];

            $mp_lv =  $this->request->post('speed');
            if ($mp_lv == 'item') $mp_lv = true;
            else {
                $mp_lv = (int)$mp_lv;
                if ($mp_lv < 1 || $mp_lv > 3) return;
            }

            $mapper->start_mapping($mp_lv);
            $this->japi_data();
        }
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
            foreach (['name','description','confirm'] as $key)
                $blueprint[$key] = __($blueprint[$key]);
            foreach ($blueprint['categories'] as &$cat)
                $cat = __($cat);
            foreach (['material_in','material_out'] as $key)
                foreach ($blueprint[$key] as &$material)
                    $material['name'] = __($material['name']);
        }
        return $data;
    }

    /**
     * @param Model_Blueprints $blueprints
     * @param string $bid
     * @return bool
     */
    private function exec_build($blueprints, $bid) {
        /** @global Model_Player $player */
        global $player;

        $tmp = $blueprints->execute($bid, $player, $player->location()->get_upgrades());
        $this->add_data('result', $tmp);
        $this->render_notifications();

        return (bool)$tmp;
    }

    public function japi_builder() {
        /** @global Model_Player $player */
        global $player;

        $blueprints = Model_Blueprints::factory($player->location(), 'upgrades');

        if ($build = $this->request->post('build'))
            $player->achievements()->achieve(Model_Achievement::MA_CONSTRUCTIONS, $this->exec_build($blueprints, $build) ? 1 : 0);

        $this->add_data('blueprints', $this->compile_builder($blueprints));
        $this->add_data('energy', $player->stats_get(Model_Player::MP_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_maker() {
        /** @global Model_Player $player */
        global $player;


        $blueprints = Model_Blueprints::factory($player->location(), 'items');
        $externals = Model_Blueprints::factory($player->location(), 'upgrades')->externalize();

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

        $blueprints = Model_Blueprints::factory($player->location(), 'attack');
        $externals = Model_Blueprints::factory($player->location(), 'upgrades')->externalize();

        if ($build = $this->request->post('build'))
            $this->exec_build($blueprints, $build);

        $blueprints->merge($externals)->validate();

        $this->add_data('blueprints', $this->compile_builder($blueprints));
        $this->add_data('energy', $player->stats_get(Model_Player::MP_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_caravan() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        if (Tool_System::instance_of($player->location(), 'Model_Places_Motorhome')) {
            /** @var Model_Places_Motorhome $motorhome */
            $motorhome = $player->location();

            $action = $this->request->post('do');
            switch ($action) {
                case 'repair':
                    $count = (int)$this->request->post('count');
                    $addr = $this->request->post('addr');
                    if (!$count || !$addr || $count <= 0) return;
                    $motorhome->repair($addr,$count);
                    break;
                case 'go':
                    $motorhome->start();
                    break;
                case 'stop':
                    $motorhome->stop();
                    break;
                case 'break':
                    $motorhome->stop_break();
                    break;
            }

        }


        $this->japi_data();
    }
}