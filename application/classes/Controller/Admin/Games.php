<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Games extends Controller_Admin_Admin {

    protected static $auto_require = ['GAMELIST'];

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
                        $tmp['alive'] = [];
                        foreach ($local_game_obj->players(false) as $p)
                            $tmp['playerlib'][$p->uin()] = $p->alive();
                    }
                } catch (Exception $e) {
                    $tmp['error'] = $e->getMessage();
                }


                $games[$line['gameid']] = $tmp;
            }

            $games[$line['gameid']]['players'][$line['uid']] = $line['name'];
        }

        $this->add_widget(View::factory('admin/games')
            ->set('games',$games)
            ->render());

        $this->render();
    }
}