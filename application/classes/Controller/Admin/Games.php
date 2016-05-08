<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Games extends Controller_Admin_Admin {

    protected static $auto_require = ['GAMELIST'];

    public function japi_game_update() {
        $game_id = (int)$this->post('gid');

        if (!$game_id) return $this->render(['success' => -1]);

        $local_game_obj = new Model_Game();
        if (!$local_game_obj->read($game_id, true)) return $this->render(['success' => -500]);
        $local_game_obj->write();

        return $this->render(['success' => 0]);
    }

    public function japi_game_retire() {
        $game_id = (int)$this->post('gid');
        $auto = (int)$this->post('auto');

        if (!$game_id) return $this->render(['success' => -1]);

        $local_game_obj = new Model_Game();
        if (!$local_game_obj->read($game_id, true)) return $this->render(['success' => -500]);
        foreach ($local_game_obj->players(true) as $pn) {
            $pn->get_status()->set_cause_of_death('Kopfschuss');
            $pn->get_status()->retrieve('heartbeat')->unbuff();
        }

        if ($auto) {
            foreach ($local_game_obj->players(false) as $pn) {
                $local_game_obj->retire($pn->id(), true);
                DB::update('users')->set(array('session' => '#'))->where('uid', '=', $pn->id())->execute();
            }

            $local_game_obj->check_players();
        }

        $local_game_obj->write();

        return $this->render(['success' => 0]);
    }

    public function japi_game_delete() {
        $game_id = (int)$this->post('gid');

        if (!$game_id) return $this->render(['success' => -1]);

        $users = DB::select('uid')->from('xref_game_player')->where('gameid', '=', $game_id)->execute()->as_array();
        DB::delete('games')->where('gameid', '=', $game_id)->execute();
        DB::delete('xref_game_player')->where('gameid', '=', $game_id)->execute();
        DB::delete('multiplayer_lobby')->where('gameid', '=', $game_id)->execute();
        DB::delete('games_cloud')->where('gameid', '=', $game_id)->execute();
        if ($users)
            DB::update('users')->set(array('session' => '#'))->where('uid', 'IN', $users)->execute();

        return $this->render(['success' => 0]);
    }

    public function japi_kill() {
        $game_id = (int)$this->post('gid');
        $entity_id = $this->post('pid');
        
        if (!$game_id || !$entity_id) return $this->render(['success' => -1]);
        
        $local_game_obj = new Model_Game();
        if (!$local_game_obj->read($game_id, true)) return $this->render(['success' => -500]);
        if (!($pn = $local_game_obj->get_player($entity_id))) return $this->render(['success' => -2]);
        if (!$pn->get_status()->alive()) return $this->render(['success' => -400]);

        $pn->get_status()->set_cause_of_death('Kopfschuss');
        $pn->get_status()->retrieve('heartbeat')->unbuff();

        $local_game_obj->write();

        return $this->render(['success' => 0]);
    }

    public function japi_retire() {
        $game_id = (int)$this->post('gid');
        $entity_id = (int)$this->post('pid');

        if (!$game_id || !$entity_id) return $this->render(['success' => -1]);

        $local_game_obj = new Model_Game();
        if (!$local_game_obj->read($game_id, true)) return $this->render(['success' => -500]);
        if (!($pn = $local_game_obj->get_player($entity_id))) return $this->render(['success' => -2]);
        if ($pn->get_status()->alive() || $local_game_obj->is_retired($entity_id)) return $this->render(['success' => -400]);

        if ($local_game_obj->retire($entity_id))
            DB::update('users')->set(array('session' => '#'))->where('uid', '=', $pn->id())->execute();
        $local_game_obj->write();

        return $this->render(['success' => 0]);
    }
    
    public function action_main() {

        $result = DB::select('games.gameid','games.timestamp','xref_game_player.uid','multiplayer_lobby.lang','multiplayer_lobby.slots','multiplayer_lobby.password','users.name')->from('games')
            ->join('xref_game_player','LEFT')->on('games.gameid','=','xref_game_player.gameid')
            ->join('multiplayer_lobby','LEFT')->on('games.gameid','=','multiplayer_lobby.gameid')
            ->join('users','LEFT')->on('xref_game_player.uid','=','users.uid')->execute()->as_array();

        $games = [];
        foreach ($result as $line) {
            if (!isset($games[$line['gameid']])) {


                $tmp = [
                    'id' => (int)$line['gameid'],
                    'players' => [],
                    'timestamp' => (int)$line['timestamp'],
                    'lang' => $line['lang'],
                    'slots' => (int)$line['slots'],
                    'password' => $line['password'],
                    'loadable' => false,
                    'error' => null,
                ];

                // Load game to get more info
                $local_game_obj = new Model_Game();
                // If we can't load the game, lock it
                try {
                    if ($local_game_obj->read((int)$line['gameid'], false)) {
                        $tmp['loadable'] = true;
                        $tmp['name'] = $local_game_obj->name();
                        $tmp['mode'] = Tool_Gamemodes::get_board_by_id($local_game_obj->setting_mode());
                        $tmp['players'] = [];
                        $tmp['npcs'] = [];
                        foreach ($local_game_obj->players(false) as $p) 
                            $tmp['players'][$p->id()] = [
                                'name' => $p->name(),
                                'job' => Tool_Gamemodes::get_job_by_id($p->job()),
                                'level' => $p->job(false),
                                'alive' => $p->get_status()->alive(),
                                'confirmed' => $p->get_status()->alive() ? false : $local_game_obj->is_retired($p->id())
                            ];
                        foreach ($local_game_obj->npcs(false) as $n)
                            $tmp['npcs'][$n->id()] = [
                                'name' => $n->name(),
                                'species' => $n->entity_species(),
                                'cls' => str_replace('Model_NPC_', '', get_class($n)),
                                'alive' => $n->get_status()->alive()
                            ];
                    }
                } catch (Exception $e) {
                    $tmp['error'] = $e->getMessage();
                }


                $games[$line['gameid']] = $tmp;
            }
        }

        $this->add_widget(View::factory('admin/games')
            ->set('games',$games)
            ->render());

        $this->render();
    }
}