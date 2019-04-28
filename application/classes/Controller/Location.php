<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Location extends Controller_Game {
    /**
     * Break a siege
     *
     * @param bool $fight True to fight, false to flee
     *
     * @throws Kohana_Exception
     */
    private function siege($fight): void
    {
        if (Globals::PrimaryPlayerF()->can_escape() || Globals::PrimaryPlayerF()->get_status()->retrieve('passout') || Globals::PrimaryPlayerF()->get_status()->retrieve('fragile')) return;

        if (Globals::PrimaryPlayerF()->location()->zombie_pop() > 0)
            Globals::PrimaryPlayerF()->location()->break_out($fight);

        $this->japi_data();
    }

    /**
     * Siege API (fight)
     */
    public function japi_fight(): void
    {
        $this->siege(true);
    }

    /**
     * Siege API (flee)
     */
    public function japi_flee(): void
    {
        $this->siege(false);
    }

    public function japi_scout(): void
    {
        if (Globals::PrimaryPlayerF()->get_status()->retrieve('fragile')) return;

        if (Globals::CurrentGameF()->config('modules.mapping')
            && !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Abstract_Node')
            && !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Abstract_Hideout')
            && !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Abstract_Xmas')
            && Globals::PrimaryPlayerF()->inventory()->get(Model_Items_Maptool::cls())
        ) {
            /** @var Model_Items_Maptool $mapper */
            $mapper = Globals::PrimaryPlayerF()->inventory()->get(Model_Items_Maptool::cls()); $mapper = $mapper[0];

            $mp_lv =  self::post('speed');
            if ($mp_lv === 'item') $mp_lv = true;
            else {
                $mp_lv = (int)$mp_lv;
                if ($mp_lv < 1 || $mp_lv > 3) return;
            }

            $mapper->start_mapping($mp_lv);
            $this->japi_data();
        }
    }

    public function japi_rooms(): void
    {
        $data = [];

        foreach (Globals::PrimaryPlayerF()->location()->rooms() as $id => $room) {

            $hid = [];
            foreach ($room->inventory()->get(Model_Items_Abstract_Virtual::cls()) as $a_item)
                /** @var  Model_Items_Abstract_Virtual $a_item */
                $hid = array_merge($hid,$this->prepare_actionlist($a_item->auto_actions(), $a_item));

            $data[$id] = [
                'id' => $id,
                'name' => $room->name_is_custom() ? $room->name() : __($room->name()),
                'size' => $room->get_space() === PHP_INT_MAX ? null : ($room->get_space() + count($room->inventory()->get())),
                'free' => $room->get_space() === PHP_INT_MAX ? null : $room->get_space(true),
                'def'  => $room->defense(),
                'deco' => $room->deco(),
                'type' => $room->get_usage(),
                'tags' => $room->get_friendly_tags(),
                'options' => [
                    'rename' => Globals::PrimaryPlayerF()->location()->is_upgradable() && !$room->name_is_fixed(),
                    'add' => Globals::PrimaryPlayerF()->location()->is_upgradable() && $room->get_usage() !== null,
                    'construct' => Globals::PrimaryPlayerF()->location()->is_upgradable() && ($id !== 0),
                    'revert'  => $room->has_different_default(),
                    'actions' => $hid,
                    'enabled' => $room->enabled()
                ]
            ];
        }
        $this->render(['rooms' => $data]);
    }

    public function japi_rename_room(): bool
    {
        $room_id = self::post('r');
        $name = self::post('n');

        if ($room_id === null)
            return $this->render(['success' => 0]);

        $room = Globals::PrimaryPlayerF()->location()->room((int)$room_id);
        if ($room === null || $room->name_is_fixed())
            return $this->render(['success' => 0]);

        $room->name(trim($name));
        $room->name_is_custom(true);

        return $this->render(['success' => 1, 'result' => $room->name()]);
    }

    public function japi_revert_room(): bool
    {
        $room_id = self::post('r');

        if ($room_id === null)
            return $this->render(['success' => 0]);

        $room = Globals::PrimaryPlayerF()->location()->room((int)$room_id);
        if ($room === null || !$room->has_different_default())
            return $this->render(['success' => 0]);

        $back_items = $room->revert_default_state();

        $valid_items = [];
        foreach ($back_items as $item)
            if (Tool_System::instance_of($item, Model_Items_Abstract_Virtual::cls()))
                $item->grind();
            else $valid_items[] = $item;

        if (!empty($valid_items)) {
            Globals::PrimaryPlayerF()->log()->add('Du hast einen Raum abgerissen und dabei einige Gegenstände retten können.');
            Tool_Scripts::place_new_item($valid_items, true, null, Model_Log_Types_Item::MLTI_ROOM_REVERT);
        } else Globals::PrimaryPlayerF()->log()->add('Du hast einen Raum abgerissen. Allerdings konntest du dabei keine Gegenstände retten...');

        return $this->render(['success' => 1]);
    }


    /**
     * @param Model_Blueprints $blueprints
     * @param Model_Room       $room
     * @return mixed
     * @throws Exception
*/
    private function compile_builder($blueprints, $room) {
        // Translate stuff
        $data = $blueprints->compile(Globals::PrimaryPlayerF()->location()->rooms_contain(), $room, Globals::PrimaryPlayerF());
        foreach ($data as &$blueprint) {
            foreach (['name','description','confirm'] as $key)
                $blueprint[$key] = __($blueprint[$key]);
            foreach ($blueprint['categories'] as &$cat)
                $cat = __($cat);
            unset($cat);
            foreach (['material_in','material_out'] as $key)
                foreach ($blueprint[$key] as &$material)
                    $material['name'] = __($material['name']);
        }
        return $data;
    }

    /**
     * @param Model_Blueprints $blueprints
     * @param string           $bid
     * @param Model_Room       $room
     * @return bool
     * @throws Exception
*/
    private function exec_build($blueprints, $bid, $room): bool
    {
        $tmp = $blueprints->execute($bid, Globals::PrimaryPlayerF(), Globals::PrimaryPlayerF()->location()->rooms_contain(), $room);
        $this->add_data('result', $tmp);
        $this->render_notifications();

        return (bool)$tmp;
    }

    public function japi_tine(): bool
    {
        $room_id = (int)self::post('r');
        $room = Globals::PrimaryPlayerF()->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'rooms', true);
        $externals = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'upgrades', true)->externalize();

        if (($build = self::post('build')) && Globals::PrimaryPlayerF()->location()->is_upgradable())
            Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_ROOM_BUILDER, $this->exec_build($blueprints, $build, $room) ? 1 : 0);

        $blueprints->merge($externals)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', Globals::PrimaryPlayerF()->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', Globals::PrimaryPlayerF()->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_builder(): bool
    {
        $room_id = (int)self::post('r');
        $room = Globals::PrimaryPlayerF()->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'upgrades',true);
        $externals = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'rooms',true)->externalize();

        if (($build = self::post('build')) && Globals::PrimaryPlayerF()->location()->is_upgradable())
            Globals::PrimaryPlayerF()->achievements()->achieve(Model_Achievement::MA_CONSTRUCTIONS, $this->exec_build($blueprints, $build, $room) ? 1 : 0);

        $blueprints->merge($externals)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', Globals::PrimaryPlayerF()->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', Globals::PrimaryPlayerF()->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_maker(): bool
    {
        $room_id = (int)self::post('r');
        $room = Globals::PrimaryPlayerF()->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'items', true);
        $externals_1 = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'upgrades', true)->externalize();
        $externals_2 = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'rooms', true)->externalize();

        if ($build = self::post('build'))
            $this->exec_build($blueprints, $build, $room);

        $blueprints->merge($externals_1)->merge($externals_2)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', Globals::PrimaryPlayerF()->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', Globals::PrimaryPlayerF()->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_fighter(): bool
    {
        $room_id = (int)self::post('r');
        $room = Globals::PrimaryPlayerF()->location()->room($room_id);
        if (!$room) return false;

        $blueprints = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'attack', true);
        $externals = Model_Blueprints::factory(Globals::PrimaryPlayerF()->location(), 'upgrades', true)->externalize();

        if ($build = self::post('build'))
            $this->exec_build($blueprints, $build, $room);

        $blueprints->merge($externals)->validate();

        $this->add_data('room', $room_id);
        $this->add_data('blueprints', $this->compile_builder($blueprints, $room));
        $this->add_data('energy', Globals::PrimaryPlayerF()->get_status()->get(Model_Status::MS_STAT_ENERGY));
        $this->add_data('zombies', Globals::PrimaryPlayerF()->location()->zombie_pop());
        $this->render(false);
        return true;
    }

    public function japi_caravan(): void
    {
        if (Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Motorhome')) {
            /** @var Model_Places_Motorhome $motorhome */
            $motorhome = Globals::PrimaryPlayerF()->location();

            $action = self::post('do');
            switch ($action) {
                case 'repair':
                    $count = (int)self::post('count');
                    $addr = self::post('addr');
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

    public function japi_legacy(): void
    {
        $action = self::post('do');
        $arg = self::post('arg');

        Globals::PrimaryPlayerF()->location()->interact($action, $arg);
        $this->japi_data();
    }
}