<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Map extends Controller_Game {

    /**
     * @param bool $sub
     * @param number $did
     * @param bool $follow
     * @param bool $support
     * @param Interface_Plentity[] $companion
     * @return bool
     */
    public static function code_go($sub, $did, $follow, $support, $companion) {
        //Get all params
        $lid = Globals::CurrentPlayer()->location_class();
        if ($lid < 0) $lid = Globals::CurrentGame()->map()->resolve_fixed_id(-$lid);

        if ($did < 0) $lid = Globals::CurrentGame()->map()->resolve_fixed_id(-$did);

        if ($did === null || $lid === null)
            return false;

        if ($follow)
            $companion[Globals::CurrentPlayer()->id()] = Globals::CurrentPlayer();
        $support = ($follow && $support);

        //Check if any player is passed out or performs a fragile action
        foreach ($companion as $current)
            /** @var Interface_Plentity $current */
            if (($current->id() != Globals::CurrentPlayer()->id() && !$current->allow(Interface_Plentity::IC_ALLOW_MOVE)) || $current->get_status()->retrieve('passout') || $current->get_status()->retrieve('fragile')) {
                if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add(($current->id() == Globals::CurrentPlayer()->id()) ? 'Du kannst dich zur Zeit nicht bewegen...' : ':name kann sich zur Zeit nicht bewegen...', array(':name' => $current->name()));
                return false;
            }

        //Get locations
        if (!($location = Globals::CurrentGame()->location($lid))) return false;
        if (!($destination = Globals::CurrentGame()->location($did))) return false;
        if ($sub && !in_array($did, $location->get_doorways())) return false;

        //Get map type
        $map_type = Globals::CurrentGame()->map($lid)->get_map_type();

        if (!$sub) {
            if ($map_type == Model_Map_Abstract::MMA_TYPE_LABYRINTH) {
                $tmp = [(Globals::CurrentPlayer()->id()) => Globals::CurrentPlayer()];
                foreach ($companion as $current)
                    if ($current->id() != Globals::CurrentPlayer()->id() && Tool_Scripts::is_npc($current))
                        $tmp[$current->id()] = $current;
                $companion = $tmp;
            }

            //Check route
            if (!($route = Globals::CurrentGame()->map($lid)->get_route($lid, $did, $map_type == Model_Map_Abstract::MMA_TYPE_LABYRINTH ? 2 : null))) {
                if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add('Diesen Ort kannst du von hier aus nicht erreichen ...');
                return false;
            }

            if ($map_type == Model_Map_Abstract::MMA_TYPE_LABYRINTH) $route['tail'] = [array_pop($route['tail'])];

            //Trail route to find if all nodes are passable
            $last_pass = $location;
            foreach ($route['tail'] as $pass) if ($pass != $lid) {
                if (!($lp = Globals::CurrentGame()->location($pass)))
                    break;
                foreach ($companion as $current)
                    if (
                        !$last_pass->can_leave($current->id(), Tool_System::instance_of($lp,'Model_Places_Tentkit'), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC) ||
                        !$lp->can_enter($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC))
                        break 2;
                $last_pass = $lp;
                foreach ($companion as $current)
                    if (!Tool_Scripts::is_npc($current))
                        $current->disable_escape();
            }

            //Check if we've reached the end
            if ($last_pass->uin() == $location->uin()) {
                if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add(((count($companion) == 1) ? 'Du kannst diese Reise nicht antreten.' : 'Ihr könnt diese Reise nicht antreten.'));
                return false;
            } elseif ($last_pass->uin() != $destination->uin()) {
                if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add((count($companion) == 1) ? 'Hier kommst du nicht weiter... du musst deine Reise nach :od unterbrechen und bei :ad eine Pause machen.' : 'Hier kommt ihr nicht weiter... ihr müsst eure Reise nach :od unterbrechen und bei :ad eine Pause machen..', [], array(':od' => $destination->name(), ':ad' => $last_pass->name()));
                $destination = $last_pass;
                $did = $last_pass->uin();
                if (!($route = Globals::CurrentGame()->map($lid)->get_route($lid, $last_pass->uin())))
                    return false;
            }

            //Get distance
            $distance = $route['distance'];
        } else {
            foreach ($companion as $current)
                if (!$location->can_leave_map($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC) || !$destination->can_enter_map($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC)) {
                    if (!Globals::shadowPlayerExists()) {
                        if ($current->id() == Globals::CurrentPlayer()->id())
                            Globals::PrimaryPlayer()->log()->add(((count($companion) == 1) ? 'Du kannst diese Reise nicht antreten.' : 'Ihr könnt diese Reise nicht antreten.'));
                        else Globals::PrimaryPlayer()->log()->add(':name kann diese Reise nicht antreten.', [':name' => $current->name()]);
                    }
                    return false;
                }

            // If we're switching between labyrinth and other map types, update escape ID
            if (Globals::CurrentGame()->map($did)->get_map_type() == Model_Map_Abstract::MMA_TYPE_LABYRINTH && Globals::CurrentGame()->map($lid)->get_map_type() != Model_Map_Abstract::MMA_TYPE_LABYRINTH)
                foreach ($companion as $current) {
                    if (!Tool_Scripts::is_npc($current))
                        $current->set_escape_target($did);
                }
            elseif (Globals::CurrentGame()->map($did)->get_map_type() != Model_Map_Abstract::MMA_TYPE_LABYRINTH && Globals::CurrentGame()->map($lid)->get_map_type() == Model_Map_Abstract::MMA_TYPE_LABYRINTH)
                foreach ($companion as $current)
                    if (!Tool_Scripts::is_npc($current)) $current->set_escape_target(null);

            $distance = 0;
            $route = [];
        }

        //Check if everyone has sufficient energy
        $overhead = 0;
        $modifier = Globals::CurrentGame()->map($lid)->movement_modifier();
        foreach ($companion as $current) {
            $energy = floor($distance * $current->get_status()->get(Model_Status::MS_CHAR_DISTANCING) * $modifier);
            if (!$current->get_status()->has(Model_Status::MS_STAT_ENERGY, $energy, Model_Status::MS_EFFECT_MOVEMENT)) {
                if ($support) $overhead += $current->get_status()->miss(Model_Status::MS_STAT_ENERGY, $energy, Model_Status::MS_EFFECT_MOVEMENT);
                else {
                    if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add(($current->id() == Globals::CurrentPlayer()->id()) ? 'Du hast nicht genug Energie, um diesen Ort zu erreichen ...' : ':name hat nicht genug Energie, um diesen Ort zu erreichen ...', array(':name' => $current->name()));
                    return false;
                }
            }
        }
        if ($support) {
            $energy = floor($distance * Globals::CurrentPlayer()->get_status()->get(Model_Status::MS_CHAR_DISTANCING) * $modifier);
            if (!Globals::CurrentPlayer()->get_status()->has(Model_Status::MS_STAT_ENERGY, $energy + $overhead * 1.2, Model_Status::MS_EFFECT_MOVEMENT)) {
                if (!Globals::shadowPlayerExists()) Globals::PrimaryPlayer()->log()->add('Du hast nicht genug Energie um diesen Weg zu bewältigen während du jemand anderem hilfst.');
                return false;
            }
        }

        //Actually move
        foreach ($companion as $current) {
            $energy = floor($distance * $current->get_status()->get(Model_Status::MS_CHAR_DISTANCING) * $modifier);
            if ($transport = Tool_Scripts::get_active_transport($current))
                $transport->trigger_before($current, $distance);

            $location->leave($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);
            $current->get_status()->modify(Model_Status::MS_STAT_ENERGY, -$energy, Model_Status::MS_EFFECT_MOVEMENT);
            if (Tool_Scripts::get_timeofday() == "day")
                $current->get_status()->modify(Model_Status::MS_STAT_THIRST, -$energy * 0.2, Model_Status::MS_EFFECT_MOVEMENT);
            $destination->enter($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);
            $current->location_class($did);

            if ($sub) {
                $location->leave_map($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);
                $destination->enter_map($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);
            }

            //Passes
            if (!$sub)
                foreach ($route['tail'] as $pass) if ($pass != $lid && $pass != $did)
                    Globals::CurrentGame()->location($pass)->pass($current->id(), !Tool_Scripts::is_npc($current) ? Interface_Tickable::IT_TYPE_PLAYER : Interface_Tickable::IT_TYPE_NPC);

            //Tumbles
            if (($sub || $map_type != Model_Map_Abstract::MMA_TYPE_LABYRINTH) && Tool_Gambling::tumble($current)) {
                $current->get_status()->set_cause_of_death('Tödlicher Sturz');
                if (!Tool_Scripts::is_npc($current))
                    /** @noinspection PhpUndefinedMethodInspection */
                    $current->log()->add('Du bist gestolpert und hast dir das Knie aufgeschlagen! Vielleicht solltest du deinen Alkoholkonsum zügeln ...');
                $current->get_status()->modify(Model_Status::MS_STAT_HEALTH, -mt_rand(3, 10), Model_Status::MS_EFFECT_MOVEMENT);
                $current->get_status()->clear_cause_of_death();
            }

            //Remove movement buffs
            $current->get_status()->remove('move');

            //Messages

            if (!Tool_Scripts::is_npc($current)) {
                /** @var Model_Player $current */
                if (!$sub && $map_type == Model_Map_Abstract::MMA_TYPE_LABYRINTH) {
                    if (!Tool_System::instance_of($destination, 'Interface_Corridor'))
                        $current->log()->add('Du tastest dich ein Stück vorran und und befindest dich jetzt in/im :location.', array(), array(':location' => $destination->name()));
                } elseif (count($companion) == 1 && $current->id() == Globals::CurrentPlayer()->id())
                    $current->log()->add('Du machst dich auf den Weg zu/zur/zum :location.', array(), array(':location' => $destination->name()));
                elseif (count($companion) > 1 && $current->id() == Globals::CurrentPlayer()->id())
                    $current->log()->add('Ihr macht euch auf den Weg zu/zur/zum :location.', array(), array(':location' => $destination->name()));
                elseif (count($companion) > 1 && $current->id() != Globals::CurrentPlayer()->id())
                    $current->log()->add(':name hat dich gebeten, ihn nach :location zu begleiten.', array(':name' => Globals::CurrentPlayer()->name()), array(':location' => $destination->name()));
                else {
                    $current->log()->add(':name hat dich angewiesen, bei :location nach dem Rechten zu sehen.', array(':name' => Globals::CurrentPlayer()->name()), array(':location' => $destination->name()));
                    Globals::PrimaryPlayer()->log()->add('Du entsendest :name nach :location, um dort nach dem Rechten zu sehen.', array(':name' => $current->name()), array(':location' => $destination->name()));
                }
            }

            if ($transport = Tool_Scripts::get_active_transport($current))
                $transport->trigger_after($current, $distance);
        }
        Globals::CurrentPlayer()->get_status()->modify(Model_Status::MS_STAT_ENERGY, -$overhead * 1.2, Model_Status::MS_EFFECT_MOVEMENT);

        //Battle
        if ($destination->zombie_pop() > 0 && !Tool_System::instance_of($destination, 'Model_Places_Abstract_Trap'))
            $destination->break_out(true);

        return true;
    }

    public function japi_tag() {
        $lid = (int)$this->post('id');
        $tag = (int)$this->post('tag');
        $txt = mb_substr($this->post('text'), 0, 32);

        $r = ($tag >= 0 && $tag <= Model_Map_Abstract::MMA_NUMBER_OF_TAGS)
            ? Globals::CurrentGame()->map(Globals::PrimaryPlayer()->location_class())->set_location_notes($lid, $tag, $txt)
            : false;

        return $this->render(['success' => $r]);
    }

    public function japi_go() {
        $did = (int)$this->post('to');
        $companion = [];

        $cc = $this->post('co');
        if (is_array($cc))
            foreach ($cc as $comid)
                if ($tmp = Tool_Scripts::check_comrade($comid))
                    $companion[$tmp->id()] = $tmp;

        $follow = (int)$this->post('follow') != 0;
        $support = (int)$this->post('support') != 0;

        $ret = static::code_go(in_array($did, Globals::PrimaryPlayer()->location()->get_doorways()), $did,$follow,$support,$companion);

        //redirect
        $this->render_notifications();

        $lomap = $this->get_labyrinth();
        return $this->render([
            'success' => $ret,
            'preview' => $lomap
        ]);
    }

    private function mapdata_classic($limit_view = null) {
        $read_only = false;

        if (Globals::PrimaryPlayer()->get_status()->retrieve('passout') || Globals::PrimaryPlayer()->get_status()->retrieve('fragile'))
            $read_only = true;

        $lid = Globals::PrimaryPlayer()->location_class();
        if ($lid < 0) $lid = Globals::CurrentGame()->map()->resolve_fixed_id(-$lid);

        if ($lid === null) return false;


        $locations = Globals::CurrentGame()->map($lid)->build_route_array($lid, $limit_view);
        $nodes = Globals::CurrentGame()->map($lid)->get_nodes();
        if (Globals::PrimaryPlayer()->location()->zombie_pop() > 0 && !Globals::PrimaryPlayer()->can_escape())
            $read_only = true;

        $pass = array();

        $modifier = Globals::CurrentGame()->map($lid)->movement_modifier();
        foreach ($locations as $id => $data)
        {
            if (!($pos = Globals::CurrentGame()->map($lid)->get_position($id)))
                continue;

            $location = Globals::CurrentGame()->location($id);

            $pass[$id] = $pos;
            $pass[$id]['id'] = $id;
            $pass[$id]['route'] = $data['tail'];
            $pass[$id]['nodes'] = $data['nodes'];
            $pass[$id]['distance'] = $data['distance'];
            $pass[$id]['energy'] = floor($data['distance'] * Globals::PrimaryPlayer()->get_status()->get(Model_Status::MS_CHAR_DISTANCING) * $modifier);
            $pass[$id]['weight'] = $location->weight_limit();
            $pass[$id]['zombies'] = $location->zombie_pop();
            $pass[$id]['name'] = __($location->name());
            $pass[$id]['icon'] = $location->icon();
            $pass[$id]['note'] = Globals::CurrentGame()->map($lid)->get_location_notes($id);
            $pass[$id]['skip_ro'] = (count($data['tail']) == 2) && Tool_System::instance_of($location,'Model_Places_Tentkit');

            $pass[$id]['classes'] = array();
            if (Tool_System::instance_of($location, 'Model_Places_Abstract_Hideout')) $pass[$id]['classes'][] = 'hideout';
            if (Globals::CurrentGame()->map($id)->get_discovery_rate($id) < 1) $pass[$id]['classes'][] = 'viewpoint';
            $pass[$id]['classes'][] = $location->is_outside() ? 'open' : 'closed';
            if ($location->get_doorways()) $pass[$id]['classes'][] = 'doorway';
            if ($pass[$id]['zombies']) $pass[$id]['classes'][] = 'siege';
        }

        $doorways = array();
        foreach (Globals::CurrentGame()->location($lid)->get_doorways() as $did) {
            $doorways[$did]['location'] = __(Globals::CurrentGame()->location($did)->name());
            $doorways[$did]['name'] = __(Globals::CurrentGame()->map($did)->get_sublocation_description());
        }

        uasort($pass, function($a, $b) {
            if ($a['id'] < 0 && $b['id'] >= 0) return -1;
            elseif ($a['id'] >= 0 && $b['id'] < 0) return 1;
            else return strcmp($a['name'], $b['name']);
        });

        if (Globals::CurrentGame()->config('modules.multiplayer')) {
            $companions = array();
            foreach (Tool_Scripts::comrades() as $comp)
                $companions[$comp->id()] = $comp->name();
        } else $companions = null;

        $this->render_mp(true);

        return $this->render([
            'read_only' => $read_only,
            'mapname' => __(Globals::CurrentGame()->map($lid)->get_sublocation_description()),
            'nodes' => $nodes,
            'network' => Globals::CurrentGame()->map($lid)->get_network(),
            'locations' => $pass,
            'doorways' => $doorways,
            'current' => $lid,
            'companions' => $companions,
            'radius' => Globals::PrimaryPlayer()->get_status()->get(Model_Status::MS_STAT_ENERGY) * Globals::PrimaryPlayer()->get_status()->scaling(Model_Status::MS_STAT_ENERGY, Model_Status::MS_EFFECT_MOVEMENT)
        ]);
    }

    public function japi_data() {
        switch (Globals::CurrentGame()->map(Globals::PrimaryPlayer()->location_class())->get_map_type()) {
            case Model_Map_Abstract::MMA_TYPE_OVERVIEW: return $this->mapdata_classic();
            case Model_Map_Abstract::MMA_TYPE_LABYRINTH: return null;
            default: throw new Exception('UNKNOWN_MAP_TYPE');
        }
    }
}