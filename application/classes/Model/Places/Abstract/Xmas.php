<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Model_Places_Abstract_Xmas extends Model_Places_Abstract_Place {

    protected static $outside = true;
    protected static $perpetualDaytime = 'snowynight';

    protected static $custom_style = 'xmas';


    public function tick($type = Interface_Tickable::IT_TYPE_PLAYER) {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        parent::tick($type);

        if (Tool_Events::current($game->next_tick()) != 'xmas' ) {
            $this->leave_map($player->id(), $type);
            $player->location_class($game->map_main()->resolve_fixed_id(1));
            $player->location()->enter_map($player->id(), $type);
        }
    }

    public function leave_map($pid = null, $type = Interface_Tickable::IT_TYPE_PLAYER) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @global $user Model_User
         */
        global $game, $user;
        if (!$pid) global $player;
        else $player = $game->get_player($pid);
        if (!parent::leave($pid, $type)) return false;

        foreach ($player->inventory()->get('Interface_Event') as $i)
            $i->grind();

        $deco = $game->location($game->map($this->uin)->resolve_fixed_id(1))->get_decoration_value();
        if ($deco > 0) {
            $user->award_coins($player->id(), $deco);
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Da du den Weihnachtsbaum so hübsch geschmückt hast, erhälst du als Belohnung :num BrainCoins sowie ein paar Geschenke. Herzlichen Glückwunsch und Frohe Weihnachten!', array(':num' => $deco)));

            $n2 = floor($deco/20);
            $n1 = ceil(($deco - $n2 * 16)/4);
            $items = array();
            for ($i = 0; $i < $n1; $i++)
                $items[] = new Model_Items_Present('Geschenk', 'Dieses Geschenk hast du als Dank dafür erhalten, dass du den Weihnachtsbaum so schön geschmückt hast. Hoffentlich gefällt dir der Inhalt.', false);
            for ($i = 0; $i < $n2; $i++)
                $items[] = new Model_Items_Present('Großes Geschenk', 'Dieses Geschenk hast du als Dank dafür erhalten, dass du beim Schmücken des Weihnachtsbaums dein Allerbestes gegeben hast. Der Inhalt wird dir sicherlich gefallen!', true);

            Tool_Scripts::place_new_item($items);
        }

        return true;
    }
}	