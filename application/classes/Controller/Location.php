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

        if ($player->get_status()->retrieve('passout') || $player->get_status()->retrieve('fragile') || $player->can_escape()) return;

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

        if ($player->get_status()->retrieve('fragile')) return;

        if ($game->config('modules.mapping') && $player->inventory()->get('Model_Items_Maptool') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Xmas') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Hideout') && !Tool_System::instance_of($player->location(), 'Model_Places_Abstract_Node')) {
            /** @var Model_Items_Maptool $mapper */
            $mapper = $player->inventory()->get('Model_Items_Maptool'); $mapper = $mapper[0];

            $mp_lv =  $this->post('speed');
            if ($mp_lv == 'item') $mp_lv = true;
            else {
                $mp_lv = (int)$mp_lv;
                if ($mp_lv < 1 || $mp_lv > 3) return;
            }

            $mapper->start_mapping($mp_lv);
            $this->japi_data();
        }
    }

    public function japi_rooms() {
        /**
         * @global $player Model_Player
         */
        global $player;

        $data = [];

        foreach ($player->location()->rooms() as $id => $room) {

            $hid = [];
            foreach ($room->inventory()->get('Model_Items_Abstract_Virtual') as $a_item)
                /** @var  Model_Items_Abstract_Virtual $a_item */
                $hid = array_merge($hid,$this->prepare_actionlist($a_item->auto_actions(), $a_item));

            $data[$id] = [
                'id' => $id,
                'name' => $room->name(),
                'size' => $room->get_space() == PHP_INT_MAX ? null : ($room->get_space() + count($room->inventory()->get())),
                'free' => $room->get_space() == PHP_INT_MAX ? null : $room->get_space(true),
                'type' => $room->get_usage(),
                'outside' => $room->is_outside(),
                'options' => [
                    'rename' => !$room->name_is_fixed(),
                    'add' => $room->get_usage() != null,
                    'construct' => $id != 0,
                    'actions' => $hid,
                    'enabled' => $room->enabled()
                ]
            ];
        }
        $this->render(['rooms' => $data]);
    }
    public function japi_rename_room() {
        /** @global Model_Player $player */
        global $player;

        $room_id = $this->post('r');
        $name = $this->post('n');

        if ($room_id === null)
            return $this->render(['success' => 0]);

        $room = $player->location()->room((int)$room_id);
        if ($room === null || $room->name_is_fixed())
            return $this->render(['success' => 0]);

        $room->name(trim($name));

        return $this->render(['success' => 1, 'result' => $room->name()]);
    }

    /**
     * @param Model_Blueprints $blueprints
     * @param Model_Room $room
     * @return mixed
     */
    private function compile_builder($blueprints, $room) {
        /** @global Model_Player $player */
        global $player;

        // Translate stuff
        $data = $blueprints->compile($player->location()->rooms_contain(), $room, $player);
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
     * @param Model_Room $room
     * @return bool
     */
    private function exec_build($blueprints, $bid, $room) {
        /** @global Model_Player $player */
        global $player;

        $tmp = $blueprints->execute($bid, $player, $player->location()->rooms_contain(), $room);
        $this->add_data('result', $tmp);
        $this->render_notifications();

        return (bool)$tmp;
    }

    public function japi_tine() {
        /** @global Model_Player $player */
        global $player;

        $room_id = (int)$this->post('r');
        $room = $player->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory($player->location(), 'rooms');

        if ($build = $this->post('build'))
            $player->achievements()->achieve(Model_Achievement::MA_ROOM_BUILDER, $this->exec_build($blueprints, $build, $room) ? 1 : 0);

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', $player->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_builder() {
        /** @global Model_Player $player */
        global $player;

        $room_id = (int)$this->post('r');
        $room = $player->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory($player->location(), 'upgrades');
        $externals = Model_Blueprints::factory($player->location(), 'rooms')->externalize();

        if ($build = $this->post('build'))
            $player->achievements()->achieve(Model_Achievement::MA_CONSTRUCTIONS, $this->exec_build($blueprints, $build, $room) ? 1 : 0);

        $blueprints->merge($externals)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', $player->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_maker() {
        /**
         * @global Model_Player $player
         */
        global $player;

        $room_id = (int)$this->post('r');
        $room = $player->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory($player->location(), 'items');
        $externals_1 = Model_Blueprints::factory($player->location(), 'upgrades')->externalize();
        $externals_2 = Model_Blueprints::factory($player->location(), 'rooms')->externalize();

        if ($build = $this->post('build'))
            $this->exec_build($blueprints, $build, $room);

        $blueprints->merge($externals_1)->merge($externals_2)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', $player->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', $player->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_fighter() {
        /** @global Model_Player $player */
        global $player;

        $room_id = (int)$this->post('r');
        $room = $player->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory($player->location(), 'attack');
        $externals = Model_Blueprints::factory($player->location(), 'upgrades')->externalize();

        if ($build = $this->post('build'))
            $this->exec_build($blueprints, $build, $room);

        $blueprints->merge($externals)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', $player->get_status()->get(Model_Status::MS_STAT_ENERGY));
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

            $action = $this->post('do');
            switch ($action) {
                case 'repair':
                    $count = (int)$this->post('count');
                    $addr = $this->post('addr');
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

    public function japi_legacy() {
        /**
         * @global $player Model_Player
         */
        global $player;

        $action = $this->post('do');
        $arg = $this->post('arg');

        $player->location()->interact($action, $arg);
        $this->japi_data();
    }
}