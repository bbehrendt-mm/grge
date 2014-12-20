<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Game extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    /**
     * Renderer Subroutine; Render Location Box
     * @throws Exception
     */
    private function render_location() {
        /** @global Model_Player $player */
        global $player;

        //ToDo: Radar effects
        // Get Radar data
        list($radar_min, $radar_max, $radar_prop, $radar_increase) = $player->location()->zombie_factory()->get_radar_data();

        // Check if we're at a hideout with active defenses
        $hideout = Tool_Scripts::current_location_hideout();
        $protected_hideout = $hideout && $hideout->get_defense() > 0;

        // Calculate approx. number of ticks between each blockade increase and random attack; set random attack value to zero if we're at a hideout
        if ($protected_hideout)
            $radar_prop = 0;
        else $radar_prop = ($radar_prop > 0) ? ceil(pow($radar_prop,-1)) : 0;
        $radar_increase = ($radar_increase > 0) ? ceil(pow($radar_increase,-1)) : 0;

        // Calculate danger level
        $danger = ($radar_prop > 0) ? floor($radar_max/4) : 0;              // Base value: Max attack group size
        if (!$protected_hideout && $radar_prop <= 1.5)     $danger += 2;    // Increase by 2 if we have a very high attack probability
        elseif (!$protected_hideout && $radar_prop <= 3)   $danger += 1;    // Increase by 1 if we have a high attack probability
        elseif ($radar_prop <= 15)  $danger -= 1;                           // Decrease by 1 if we have a very low attack probability
        if ($radar_increase != 0 && $radar_increase <= 3)   $danger += 1;   // Increase by 1 if we have a very high blocking speed
        $danger = min(5,max(($radar_prop > 0) ? 1 : 0,$danger));            // Confine danger to 0-5 range

        // Add render data
        $this->add_data('location', [
            'meta' => [
                'name' => __($player->location()->name()),
                'desc' => __($player->location()->description()),
                'outside' => $player->location()->is_outside()
            ],
            'radar' => [
                'danger' => $danger,
                'min' => 0,
                'max' => ceil($radar_max/8)*8,
                'prop' => $radar_prop * 5,
                'inc' => $radar_increase * 5,
                'hideout' => (bool)$hideout,
                'zombies' => $player->location()->zombie_pop()
            ]
        ]);
    }

    /**
     * @param $itemlist Model_Items_Abstract_Item[]
     * @return array
     */
    private function group_itemlist($itemlist) {
        $grouping = Array();
        $cache = Array();

        foreach ($itemlist as $item)
        {
            if (!isset($grouping[$item->cat()])) $grouping[$item->cat()] = Array('items' => Array(), 'name' => '');

            $static = Tool_System::instance_of($item, 'Interface_Static');

            if ($static && isset($cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()])) {
                $link = &$grouping[$item->cat()]['items'][$cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()]];
                $link['static']++;
                $link['uins'][] = $item->uin();
                continue;
            } elseif ($static)
                $cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()] = $item->uin();

            $flags = [];

            /** @noinspection PhpUndefinedMethodInspection */
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Armor') && $item->is_active())    $flags[] = 'equipped';
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Armor'))                          $flags[] = 'armor';
            if (Tool_System::instance_of($item, 'Model_Battle_Weapon'))                                 $flags[] = 'weapon';
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Escape'))                         $flags[] = 'escape';
            if (Tool_System::instance_of($item, 'Interface_Tmpitem'))                                   $flags[] = 'temp';
            if (Tool_System::instance_of($item, 'Interface_Event'))                                     $flags[] = 'event';
            if ($item->is_carrier_item())                                                               $flags[] = 'carrier';

                $grouping[$item->cat()]['items'][$item->uin()] = Array(
                'name' => __($item->name()),
                'description' => __($item->description()),
                'icon' => $item->icon(),
                'addr' => substr(md5(get_class($item) . '__salt'), 0, 5),
                'flags' => $flags,
                'uin' => $item->uin(),
                'set' => [$item->uin()],
                'static' => 1,
                'count' => (Tool_System::instance_of($item, 'Interface_Countable')) ? $item->count() : null,
            );
        }

        foreach (array_keys($grouping) as $gid)
            uasort($grouping[$gid]['items'], function($a, $b) {return strcmp($a['addr'], $b['addr']);});

        foreach (array_keys($grouping) as $gid)
            switch ($gid)
            {
                case Model_Items_Abstract_Item::MIAI_CAT_GEAR:          $grouping[$gid]['name'] = __('Ausrüstung'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_FIGHT:         $grouping[$gid]['name'] = __('Waffen und Verteidigung'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_FOOD:          $grouping[$gid]['name'] = __('Nahrungsmittel'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_DRUG:          $grouping[$gid]['name'] = __('Drogen und med. Zubehör'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_RES:           $grouping[$gid]['name'] = __('Baumaterialien'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_EVENT:         $grouping[$gid]['name'] = __('Besonderes'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_LITERATURE:    $grouping[$gid]['name'] = __('Lesestoff'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_BOTTLES:       $grouping[$gid]['name'] = __('Wasserbehälter'); break;
                case Model_Items_Abstract_Item::MIAI_CAT_MISC: default: $grouping[$gid]['name'] = __('Sonstiges'); break;
            }

        return $grouping;
    }

    private function render_inventory() {
        /**
         * @global $game Model_Game
         * @global $player Model_Player
         */
        global $game, $player;

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */

        $this->add_data('inventory', [
            'player' => $this->group_itemlist($player->inventory()->get()),
            'weight' => [$player->inventory()->weight(),$player->inventory()->limit()],
            'location' => $this->group_itemlist($player->location()->inventory()->get()),
            'home' => (bool)Tool_Scripts::current_location_hideout(),
        ]);
    }

    /**
     * Renderer API
     * @throws Kohana_Exception
     */
    public function japi_data() {
        $this->render_location();
        $this->render_inventory();

        $version_data = Kohana::$config->load('build.version');
        $this->add_data('version', "{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']}");

        $this->render(false);
    }

    /**
     * Load appropriate game interface (in-game/death/pause/aprils fools)
     */
    public function action_redirect() {
        /**
         * @global Model_Game $game
         * @global Model_Player $player
         */
        global $game, $player;

        //Redirect
        if (!$game)
            $this->redirect(URL::site('gamemaster/lobby', 'http'));
        if (!$player) {
            $this->session->delete('game');
            $this->redirect(URL::site('landing/redirect', 'http'));
        }

        //Check if player is alive
        if ($player->alive()) {
            $player->last_action(true);

            if ($game->paused())
                // Load pause screen
                //ToDo: Pause Page
                $this->add_widget(View::factory('pages/noview')->render());

            elseif (Tool_Events::is_april_fools())
                // Aprils fools
                //ToDo: Aprils Fools Page
                $this->add_widget(View::factory('pages/noview')->render());

            else
                // Ingame View
                $this->add_widget(View::factory('pages/ingame')->render());
        }
        //Show game summary
        else
            //ToDo: Death Page
            $this->add_widget(View::factory('pages/noview')->render());

        $this->render();
    }

}