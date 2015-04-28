<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Places_Abstract_Xmas extends Model_Places_Abstract_Place {

    protected static $outside = true;
    protected static $perpetualDaytime = 'snowynight';

    public function uin($uin = NULL) {
        if ($uin === NULL) return parent::uin();
        else $t = parent::uin($uin);

        $this->item_factory->updateConfigBase('xmas');
        $this->zombie_factory->updateConfigBase('xmas');
        return $t;
    }

    public function tick() {
        global $game, $player;

        parent::tick();

        if (Tool_Events::current($game->next_tick()) != 'xmas' ) {
            $this->leave_map($player->id());
            $player->location_class($game->map_main()->resolve_fixed_id(1));
            $player->location()->enter_map($player->id());
        }
    }

    public function leave_map($pid = null) {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         * @global $user Model_User
         */
        global $game, $user;
        if (!$pid) global $player;
        else $player = $game->get_player($pid);
        if (!parent::leave($pid)) return false;

        foreach ($player->inventory()->get('Interface_Event') as $i)
            $i->grind();

        $deco = $game->location($game->map($this->uin)->resolve_fixed_id(1))->get_decoration_value();
        if ($deco > 0) {
            $user->award_coins($player->id(), $deco);
            $player->log()->add(new Model_Log_Types_Text(null, null, 'Da du den Weihnachtsbaum so hübsch geschmückt hast, erhälst du als Belohnung :num universelle Seelenpunkte sowie ein paar Geschenke. Herzlichen Glückwunsch und Frohe Weihnachten!', array(':num' => $deco)));

            $n2 = floor($deco/5);
            $n1 = $deco - $n2 * 4;
            $items = array();
            for ($i = 0; $i < $n1; $i++)
                $items[] = new Model_Items_Present('Geschenk', 'Dieses Geschenk hast du als Dank dafür erhalten, dass du den Weihnachtsbaum so schön geschmückt hast. Hoffentlich gefällt dir der Inhalt.', false);
            for ($i = 0; $i < $n2; $i++)
                $items[] = new Model_Items_Present('Großes Geschenk', 'Dieses Geschenk hast du als Dank dafür erhalten, dass du beim Schmücken des Weihnachtsbaums dein Allerbestes gegeben hast. Der Inhalt wird dir sicherlich gefallen!', false);

            Tool_Scripts::place_new_item($items);
        }

        foreach ($game->map($this->uin())->get_locations() as $location) {
            $lobj = $game->location($location);
            if ($lobj) $lobj->grind();
            else $game->uin()->remove($location);
        }

        return true;
    }
}	