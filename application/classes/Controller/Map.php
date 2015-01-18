<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Map extends Controller_Game {

    private function internal_go($sub, $did, $follow, $support, $companion) {

        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $current Model_Player
         */
        global $game, $player;

        //Get all params
        $lid = $player->location_class();
        if ($lid < 0) $lid = $game->map()->resolve_fixed_id(-$lid);

        if ($did < 0) $lid = $game->map()->resolve_fixed_id(-$did);

        if ($did === null || $lid === null)
            return false;

        if ($follow)
            $companion[$player->id()] = $player;
        $support = ($follow && $support);

        //Check if any player is passed out or performs a fragile action
        foreach ($companion as $current)
            if ($current->buff_retr('passout') || $current->buff_retr('fragile')) {
                $player->log()->add(new Model_Log_Types_Text(null, null, ($current == $player) ? 'Du kannst dich zur Zeit nicht bewegen...' : ':name kann sich zur Zeit nicht bewegen...', array(':name' => $current->name())));
                return false;
            }

        //Get locations
        if (!($location = $game->location($lid))) return false;
        if (!($destination = $game->location($did))) return false;
        if ($sub && !in_array($did, $location->get_doorways())) return false;

        if (!$sub) {
            //Check route
            if (!($route = $game->map($lid)->get_route($lid, $did))) {
                $player->log()->add(new Model_Log_Types_Text(null, null, 'Diesen Ort kannst du von hier aus nicht erreichen ...'));
                return false;
            }

            //Trail route to find if all nodes are passable
            $last_pass = $location;
            foreach ($route['tail'] as $pass) if ($pass != $lid) {
                if (!($lp = $game->location($pass)))
                    break;
                foreach ($companion as $current)
                    if (!$last_pass->can_leave($current->id()) || !$lp->can_enter($current->id()))
                        break 2;
                $last_pass = $lp;
                foreach ($companion as $current)
                    $current->disable_escape();
            }

            //Check if we've reached the end
            if ($last_pass->uin() == $location->uin()) {
                $player->log()->add(new Model_Log_Types_Text(null, null, ((count($companion) == 1) ? 'Du kannst diese Reise nicht antreten.' : 'Ihr könnt diese Reise nicht antreten.')));
                return false;
            } elseif ($last_pass->uin() != $destination->uin()) {
                $player->log()->add(new Model_Log_Types_Text(null, null, (count($companion) == 1) ? 'Hier kommst du nicht weiter... du musst deine Reise nach :od unterbrechen und bei :ad eine Pause machen.' : 'Hier kommt ihr nicht weiter... ihr müsst eure Reise nach :od unterbrechen und bei :ad eine Pause machen..', array(':od' => $destination->name(), ':ad' => $last_pass->name())));
                $destination = $last_pass;
                $did = $last_pass->uin();
                if (!($route = $game->map($lid)->get_route($lid, $last_pass->uin())))
                    return false;
            }

            //Get distance
            $distance = $route['distance'];
        } else {
            $distance = 0;
            $route = [];
        }

        //Check if everyone has sufficient energy
        $overhead = 0;
        $modifier = $game->map($lid)->movement_modifier();
        foreach ($companion as $current) {
            $energy = floor($distance * $current->stats_get(Model_Player::MP_CHAR_DISTANCING) * $modifier);
            if ($current->stats_get(Model_Player::MP_STAT_ENERGY) < $energy) {
                if ($support) $overhead += ($energy - $current->stats_get(Model_Player::MP_STAT_ENERGY));
                else {
                    $player->log()->add(new Model_Log_Types_Text(null, null, ($current == $player) ? 'Du hast nicht genug Energie, um diesen Ort zu erreichen ...' : ':name hat nicht genug Energie, um diesen Ort zu erreichen ...', array(':name' => $current->name())));
                    return false;
                }
            }
        }

        $energy = floor($distance * $player->stats_get(Model_Player::MP_CHAR_DISTANCING) * $modifier);
        if ($player->stats_get(Model_Player::MP_STAT_ENERGY) < ($energy + $overhead * 1.2)) {
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Du hast nicht genug Energie um diesen Weg zu bewältigen während du jemand anderem hilfst.'));
            return false;
        }

        //Actually move
        foreach ($companion as $current) {
            $energy = floor($distance * $current->stats_get(Model_Player::MP_CHAR_DISTANCING) * $modifier);
            if ($transport = Tool_Scripts::get_active_transport($current))
                $transport->trigger_before($current, $distance);

            $location->leave($current->id());
            $current->stats_modify(Model_Player::MP_STAT_ENERGY, -$energy);
            if (Tool_Scripts::get_timeofday() == "day")
                $current->stats_modify(Model_Player::MP_STAT_THIRST, -$energy * 0.2);
            $destination->enter($current->id());
            $current->location_class($did);

            //Passes
            if (!$sub)
                foreach ($route['tail'] as $pass) if ($pass != $lid && $pass != $did)
                    $game->location($pass)->pass($current->id());

            //Tumbles
            if ($game->tumble($current->id())) {
                $current->log()->add(new Model_Log_Types_Text(null, null, 'Du bist gestolpert und hast dir das Knie aufgeschlagen! Vielleicht solltest du deinen Alkoholkonsum zügeln ...'));
                $current->stats_modify(Model_Player::MP_STAT_HEALTH, -mt_rand(3, 10));
            }

            //Remove movement buffs
            $current->buff_remove('move');

            //Messages
            if (count($companion) == 1 && $current == $player)
                $current->log()->add(new Model_Log_Types_Text(null, null, 'Du machst dich auf den Weg zu/zur/zum :location.', array(), array(':location' => $destination->name())));
            elseif (count($companion) > 1 && $current == $player)
                $current->log()->add(new Model_Log_Types_Text(null, null, 'Ihr macht euch auf den Weg zu/zur/zum :location.', array(), array(':location' => $destination->name())));
            elseif (count($companion) > 1 && $current != $player)
                $current->log()->add(new Model_Log_Types_Text(null, null, ':name hat dich gebeten, ihn nach :location zu begleiten.', array(':name' => $player->name()), array(':location' => $destination->name())));
            else {
                $current->log()->add(new Model_Log_Types_Text(null, null, ':name hat dich angewiesen, bei :location nach dem Rechten zu sehen.', array(':name' => $player->name()), array(':location' => $destination->name())));
                $player->log()->add(new Model_Log_Types_Text(null, null, 'Du entsendest :name nach :location, um dort nach dem Rechten zu sehen.', array(':name' => $current->name()), array(':location' => $destination->name())));
            }

            if ($transport = Tool_Scripts::get_active_transport($current))
                $transport->trigger_after($current, $distance);
        }
        $player->stats_modify(Model_Player::MP_STAT_ENERGY, -$overhead * 1.2);

        //Battle
        if ($destination->zombie_pop() > 0)
            $destination->break_out(true);

        return true;
    }

    public function japi_go() {

        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @var $current Model_Player
         */
        global $game, $player;

        $did = (int)$this->request->post('to');
        $companion = [];
        if ($this->request->post('companion'))
            foreach (explode(',', $this->request->post('companion')) as $comid)
                if ($tmp = Tool_Scripts::check_comrade((int)$comid))
                    $companion[$tmp->id()] = $tmp;

        $follow = (int)$this->request->post('follow') != 0;
        $support = (int)$this->request->post('support') != 0;

        $ret = $this->internal_go(in_array($did, $player->location()->get_doorways()), $did,$follow,$support,$companion);

        //redirect
        $this->render_notifications();
        return $this->render([
            'success' => $ret,
        ]);
    }

    public function japi_data() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        $read_only = false;

        if ($player->buff_retr('passout') || $player->buff_retr('fragile'))
            $read_only = true;

        $rmp = Tool_Scripts::check_comrade((int)$this->request->param('id'));

        $lid = $player->location_class();
        if ($lid < 0) $lid = $game->map()->resolve_fixed_id(-$lid);

        if ($lid === null) return false;

        $locations = $game->map($lid)->build_route_array($lid);
        $nodes = $game->map($lid)->get_nodes();
        if ($player->location()->zombie_pop() > 0 && !$player->can_escape())
            $read_only = true;

        $pass = array();

        $modifier = $game->map($lid)->movement_modifier();
        foreach ($locations as $id => $data)
        {
            if (!($pos = $game->map($lid)->get_position($id)))
                continue;

            $location = $game->location($id);

            $pass[$id] = $pos;
            $pass[$id]['id'] = $id;
            $pass[$id]['route'] = $data['tail'];
            $pass[$id]['nodes'] = $data['nodes'];
            $pass[$id]['distance'] = $data['distance'];
            $pass[$id]['energy'] = floor($data['distance'] * $player->stats_get(Model_Player::MP_CHAR_DISTANCING) * $modifier);
            $pass[$id]['weight'] = $location->weight_limit();
            $pass[$id]['zombies'] = $location->zombie_pop();
            $pass[$id]['name'] = __($location->name());
            $pass[$id]['icon'] = $location->icon();

            $pass[$id]['classes'] = array();
            if (Tool_System::instance_of($location, 'Model_Places_Abstract_Hideout')) $pass[$id]['classes'][] = 'hideout';
            if ($game->map($id)->get_discovery_rate($id) < 1) $pass[$id]['classes'][] = 'viewpoint';
            $pass[$id]['classes'][] = $location->is_outside() ? 'open' : 'closed';
            if ($location->get_doorways()) $pass[$id]['classes'][] = 'doorway';
            if ($pass[$id]['zombies']) $pass[$id]['classes'][] = 'siege';
        }

        $doorways = array();
        foreach ($game->location($lid)->get_doorways() as $did) {
            $doorways[$did]['location'] = __($game->location($did)->name());
            $doorways[$did]['name'] = __(Tool_Scripts::get_map_description($game->map($did)->get_sublocation()));
        }

        uasort($pass, function($a, $b) {
            if ($a['id'] < 0 && $b['id'] >= 0) return -1;
            elseif ($a['id'] >= 0 && $b['id'] < 0) return 1;
            else return strcmp($a['name'], $b['name']);
        });

        if ($game->config('modules.multiplayer')) {
            $companions = array();
            foreach (Tool_Scripts::comrades() as $comp)
                $companions[$comp->id()] = $comp->name();
        } else $companions = null;

        return $this->render([
            'read_only' => $read_only,
            'mapname' => __(Tool_Scripts::get_map_description($game->map($lid)->get_sublocation())),
            'nodes' => $nodes,
            'network' => $game->map($lid)->get_network(),
            'locations' => $pass,
            'doorways' => $doorways,
            'current' => $lid,
            'escort' => $rmp,
            'companions' => $companions,
            'radius' => $player->stats_get(Model_Player::MP_STAT_ENERGY)
        ]);
    }
}