<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Game extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    /**
     * @param Model_Hid[] $actions
     * @param Model_Items_Abstract_Virtual $v_item
     * @return mixed
     */
    private function prepare_actionlist($actions, $v_item = null) {
        // Prepare actions
        foreach ($actions as &$action) {
            // Check vitem
            $action['remaining'] = $v_item ? ($v_item->remaining_actions($action['action']) < PHP_INT_MAX ? $v_item->remaining_actions($action['action']) : -1 ) : -1;

            // Translate
            foreach (['description', 'tooltip'] as $t)
                if ($action[$t]) $action[$t] = __($action[$t]);
            // Clean
            foreach (['action'] as $t)
                unset ($action[$t]);
        }

        return $actions;
    }

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

        // Get local actions
        $a = [];
        foreach (Tool_Scripts::available_items('Model_Items_Abstract_Virtual',false,true,false,$player) as $a_item)
            /** @var  Model_Items_Abstract_Virtual $a_item */
            $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));

        // Add render data
        $this->add_data('location', [
            'meta' => [
                'name' => __($player->location()->name()),
                'desc' => __($player->location()->description()),
                'outside' => $player->location()->is_outside()
            ],
            'actions' => $a,
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

        // iterate over item list
        foreach ($itemlist as $item)
        {
            // Create category array
            if (!isset($grouping[$item->cat()])) $grouping[$item->cat()] = Array('items' => Array(), 'name' => '');

            // Check if our item supports static stacking
            $static = Tool_System::instance_of($item, 'Interface_Static');
            // If we support stacking and a stack for this item exists, simply add to stack
            if ($static && isset($cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()])) {
                $link = &$grouping[$item->cat()]['items'][$cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()]];
                $link['static']++;
                $link['set'][] = $item->uin();
                continue;
            // If we support stacking and a stack for this item does not exists, create one
            } elseif ($static)
                $cache[get_class($item) . "/" . $item->icon() . "/" . $item->name()] = $item->uin();

            $flags = [];

            /** @noinspection PhpUndefinedMethodInspection */
            // Set item flags
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Armor') && $item->is_active())    $flags[] = 'equipped';
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Armor'))                          $flags[] = 'armor';
            if (Tool_System::instance_of($item, 'Model_Battle_Weapon'))                                 $flags[] = 'weapon';
            if (Tool_System::instance_of($item, 'Model_Items_Abstract_Escape'))                         $flags[] = 'escape';
            if (Tool_System::instance_of($item, 'Interface_Tmpitem'))                                   $flags[] = 'temp';
            if (Tool_System::instance_of($item, 'Interface_Event'))                                     $flags[] = 'event';
            if ($item->is_carrier_item())                                                               $flags[] = 'carrier';

            // Build final item object
            $grouping[$item->cat()]['items'][$item->uin()] = Array(
                'name' => __($item->name()),
                'description' => __($item->description()),
                'icon' => $item->icon(),
                'weight' => $item->weight(),
                'actions' => $this->prepare_actionlist($item->auto_actions()),
                'addr' => substr(md5(get_class($item) . '__salt'), 0, 5),
                'flags' => $flags,
                'uin' => $item->uin(),
                'set' => [$item->uin()],
                'static' => 1,
                'count' => (Tool_System::instance_of($item, 'Interface_Countable')) ? $item->count() : null,
                'capacity' => (Tool_System::instance_of($item, 'Interface_Countable')) ? $item->capacity() : null,
                'stack' => __($item->stackname()),
                'label' => $item->label()
            );
        }

        // Sort items based on their address
        foreach (array_keys($grouping) as $gid)
            uasort($grouping[$gid]['items'], function($a, $b) {return strcmp($a['addr'], $b['addr']);});

        // Set category strings
        foreach (array_keys($grouping) as $gid)
            switch ($gid) {
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
         * @global $player Model_Player
         */
        global $player;

        // Get heroic actions
        $a = [];
        foreach (Tool_Scripts::available_items('Model_Items_Abstract_Virtual',true,false,false,$player) as $a_item)
            /** @var  Model_Items_Abstract_Virtual $a_item */
            $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $this->add_data('inventory', [
            'player' => $this->group_itemlist($player->inventory()->get()),
            'weight' => [$player->inventory()->weight(),$player->inventory()->limit()],
            'location' => $this->group_itemlist($player->location()->inventory()->get()),
            'home' => (bool)Tool_Scripts::current_location_hideout(),
            'heroics' => $a
        ]);
    }

    private function condense_buff($bar) {
        /**
         * @global Model_Player $player
         */
        global $player;

        $ret = Array();
        foreach ($player->buff_get() as $buff) {
            $t = ['icon' => $buff->icon(), 'effects' => []];
            $apply = false;
            foreach ([Model_Buffs_Abstract_Buff::MB_RAISE_ACC, Model_Buffs_Abstract_Buff::MB_DROP_ACC, Model_Buffs_Abstract_Buff::MB_RAISE_PRC, Model_Buffs_Abstract_Buff::MB_DROP_PRC] as $id) {
                $effect = $buff->effect($bar, $id);
                $t['effects'][$id] = $effect;
                $apply = $apply || ($effect != 0);
            }
            if ($apply) $ret[] = $t;
        }

        return $ret;
    }

    private function status($type) {
        /**
         * @global $player Model_Player
         */
        global $player;

        return [
            'value' => round($player->stats_get($type),2),
            'buffs' => $this->condense_buff($type)
        ];
    }

    private function render_status() {
        /**
         * @global $player Model_Player
         */
        global $player;

        $cache = [];
        $tmp = 0;
        for ($type = 1; $type <= Model_Player::MP_STATUS_COUNT; $type++)
            if ($type <= 5 || $player->stats_get($type))
                $cache[$type] = $this->status($type);

        $buffs = [];
        foreach ($player->buff_get() as $buff) if ($buff->visible())
            $buffs[] = ['icon' => $buff->icon(), 'name' => __($buff->name()), 'desc' => __($buff->description())];

        $this->add_data('status', [
            'bars' => $cache,
            'buffs' => $buffs
        ]);
    }

    protected function render_notifications() {
        /**
         * @global $player Model_Player
         */
        global $player;

        foreach ($player->log()->get_all() as $message)
            /** @var Interface_Message $message */
            $this->add_note('info',$message->render_body(),$message->render_title());
        $player->log()->clear();
    }

    /**
     * Renderer API
     * @throws Kohana_Exception
     */
    public function japi_data() {


        $this->render_location();
        $this->render_inventory();
        $this->render_status();
        $this->render_notifications();

        $version_data = Kohana::$config->load('build.version');
        $this->add_data('version', "{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['stage']}-{$version_data['maintenance']}-{$version_data['build']}");

        $this->render(false);
        return true;
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