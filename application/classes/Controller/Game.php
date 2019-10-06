<?php /** @noinspection NotOptimalIfConditionsInspection */
defined('SYSPATH') or die('No direct script access.');

class Controller_Game extends Controller {

    protected static $force_login = true;
    protected static $menu = 'logout';

    protected static $death_allowed_actions = ['end','logs'];

    /**
     * Hook for AJAX calls using JAPI
     *
     * @throws Kohana_Exception
     */
    public function action_japi(): void {
        if ($this->request->param('jaction') === 'fixlink') {
            parent::action_japi();
            return;
        }

        if (!Globals::hasCurrentPlayer() || !Globals::hasCurrentGame()) {
            $this->render(['redirect' => 'landing/redirect']);
            return;
        }
        
        if (!in_array(
                        $this->request->param('jaction'),
                        static::$death_allowed_actions, true
                    )
            && !Globals::PrimaryPlayerF()->get_status()->alive()
        ) {
            $this->render_notifications();
            $this->render(['redirect' => 'game/redirect']);
            return;
        }

        parent::action_japi();
    }

    /**
     * @param Model_Hid[] $actions
     * @param Model_Items_Abstract_Virtual $v_item
     * @return mixed
     */
    protected function prepare_actionlist($actions, $v_item = null) {
        // Prepare actions
        foreach ($actions as &$action) {
            // Check item
            $action['remaining'] = $v_item ? ($v_item->remaining_actions($action['action']) < PHP_INT_MAX ? $v_item->remaining_actions($action['action']) : -1 ) : -1;

            // Translate
            foreach (['description', 'tooltip'] as $t)
                if ($action[$t]) $action[$t] = __($action[$t]);
        }

        return $actions;
    }

    protected function get_labyrinth() {
        if (Globals::CurrentGameF()->mapF(Globals::PrimaryPlayerF()->location_class())->get_map_type() === Model_Map_Abstract::MMA_TYPE_LABYRINTH) {

            $lid = Globals::PrimaryPlayerF()->location_class();
            $map = Globals::CurrentGameF()->mapF($lid);
            $locations = $map->build_route_array($lid, 2);

            $mp_top = null; $mp_left = null; $mp_bottom = null; $mp_right = null;
            $mp_others = []; $mp_current = null;

            $master = $map->get_position($lid);
            $modifier = $map->movement_modifier();

            $in_corridor = Tool_System::instance_of(Globals::CurrentGameF()->location($lid), 'Interface_Corridor');

            foreach ($locations as $id => $data) {
                if (!($pos = $map->get_position($id)))
                    continue;

                $location = Globals::CurrentGameF()->locationF($id);
                $is_corridor = Tool_System::instance_of($location, 'Interface_Corridor');

                if (!$in_corridor && $id !== $lid && !Tool_System::instance_of($location, 'Interface_Corridor')) continue;

                $tmp = [
                    'id' => $id,
                    'energy' => floor($data['distance'] * Globals::PrimaryPlayerF()->get_status()->get(Model_Status::MS_CHAR_DISTANCING) * $modifier),
                    'weight' => $location->weight_limit(),
                    'name' => __($location->name()),
                    'icon' => $location->icon(),
                ];

                if ($id === $lid) {
                    $mp_current = $tmp;
                    $mp_current['zombies'] = $location->zombie_pop();
                    $mp_current['players'] = max(0,count(Tool_Scripts::at_location($lid, true, false)) - 1);
                    $mp_current['npcs'] = [];
                    foreach (Tool_Scripts::at_location($lid, false, true) as $npc)
                        if ($npc->allow(Interface_Plentity::IC_ALLOW_MOVE))
                            $mp_current['npcs'][] = $npc->id();
                } elseif ((!$in_corridor || !$is_corridor) && $pos['x'] === $master['x'] && $pos['y'] === $master['y'])
                    $mp_others[] = $tmp;
                elseif ($is_corridor && $pos['x'] === $master['x'] && $pos['y'] > $master['y'])
                    $mp_top = $tmp;
                elseif ($is_corridor && $pos['x'] === $master['x'] && $pos['y'] < $master['y'])
                    $mp_bottom = $tmp;
                elseif ($is_corridor && $pos['x'] > $master['x'] && $pos['y'] === $master['y'])
                    $mp_right = $tmp;
                elseif ($is_corridor && $pos['x'] < $master['x'] && $pos['y'] === $master['y'])
                    $mp_left = $tmp;
            }

            return [
                    'current' => $mp_current,
                    'top' => $in_corridor ? $mp_top : null,
                    'left' => $in_corridor ? $mp_left : null,
                    'bottom' => $in_corridor ? $mp_bottom : null,
                    'right' => $in_corridor ? $mp_right : null,
                    'others' => $mp_others,
                    'skin' => $map->get_skin(),
                ];
        } else return null;
    }

    /**
     * Renderer Subroutine; Render Location Box
     * @throws Exception
     */
    private function render_location(): void
    {
        Globals::PrimaryPlayerF()->location()->pre_render();

        $radar_scale = Globals::PrimaryPlayerF()->get_status()->retrieve('tr_danger') ? 1 : 4;

        // Get zombie factory;
        $factory = Globals::PrimaryPlayerF()->location()->zombie_factory();
        $radar_prop = $factory->stat_chance();
        $acc_zombies = $factory->get_accumulated_zombie_types();

        // Check if we're at a hideout with active defenses
        $hideout = Tool_Scripts::current_location_hideout();
        $protected_hideout = $hideout && $hideout->get_defense() > 0;

        // Calculate approx. number of ticks between each blockade increase and random attack; set random attack value to zero if we're at a hideout
        $radar_prop = ($radar_prop > 0) ? ceil($radar_prop ** -1) : 0;

        // Calculate danger level
        $normalized_danger = $factory->get_strength() * $factory->stat_chance();

        $danger = 0;
        if ($normalized_danger >  0 && $danger <=  1) $danger = 1;
        if ($normalized_danger >  1 && $danger <=  2) $danger = 2;
        if ($normalized_danger >  2 && $danger <=  5) $danger = 3;
        if ($normalized_danger >  5 && $danger <= 10) $danger = 4;
        if ($normalized_danger > 10)                  $danger = 5;

        // Get local actions
        $a = []; $b = [];
        if (!Globals::PrimaryPlayerF()->get_status()->retrieve('fragile')) {
            foreach (Tool_Scripts::get_items(Model_Items_Abstract_Virtual::cls(),Struct_ScriptItemSource::onlyLocation()->use_perspective(Globals::PrimaryPlayerF())) as $a_item)
                /** @var  Model_Items_Abstract_Virtual $a_item */
                if (!$a_item->use_manual_ui())
                    $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));

            foreach ( Globals::PrimaryPlayerF()->location()->rooms() as $room )
                foreach ($room->inventory()->get( Model_Items_Abstract_Virtual::cls() ) as $b_item)
                    /** @var  Model_Items_Abstract_Virtual $b_item */
                    if (!$b_item->use_manual_ui())
                        $b = array_merge($b,$this->prepare_actionlist($b_item->auto_actions(), $b_item));
        }


        // Get doorways
        $doorways = array();
        foreach (Globals::PrimaryPlayerF()->location()->get_doorways() as $did) {
            $doorways[$did]['location'] = __(Globals::CurrentGameF()->locationF($did)->name());
            $doorways[$did]['name'] = __(Globals::CurrentGameF()->mapF($did)->get_sublocation_description());
        }
        if (!count($doorways)) $doorways = false;

        $z_list = [];
        foreach ($acc_zombies as $z_class => $z_count)
            /** @var $z_class Model_Combat_Zombies_Zombie */
            if ($z_count > 0) $z_list[] = ['icon' => $z_class::static_sprite(), 'count' => $z_count];

        // Add render data
        $this->add_data('location', [
            'id' => Globals::PrimaryPlayerF()->location_class(),
            'meta' => [
                'name' => __(Globals::PrimaryPlayerF()->location()->name()),
                'desc' => __(Globals::PrimaryPlayerF()->location()->description()),
                'outside' => Globals::PrimaryPlayerF()->location()->is_outside(),
                'css' => Globals::PrimaryPlayerF()->location()->getCustomStyle(),
            ],
            'actions' => [$a,$b],
            'doorways' => $doorways,
            'hideout' => $hideout ? [
                'state' => (int)round((1 - $hideout->get_decay()) * 100),
                'max_defense' => (int)$hideout->get_defense(true),
                'defense' => (int)$hideout->get_defense(false),
                'deco' => $hideout->deco(false),
            ] : false,
            'discovery' => Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), Model_Places_Abstract_Node::cls())
                ? round(100*Globals::CurrentGameF()->mapF(Globals::PrimaryPlayerF()->location_class())->get_discovery_rate(Globals::PrimaryPlayerF()->location_class(), true))
                : false,
            'spawnrate' => !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), Model_Places_Abstract_Hideout::cls()) && !Globals::PrimaryPlayerF()->location()->item_factory()->is_empty()
                ? round(100*Globals::CurrentPlayerActualF()->location()->item_factory()->get_fillrate())
                : false,
            'radar' => [
                'danger' => $danger,
                'max' => max(1,ceil( $factory->stat_max_zombie_count() /$radar_scale)*$radar_scale),

                'prop' => $radar_prop * 5,
                'c' => $factory->stat_chance(),
                'bd' => $protected_hideout ? 100 : round($factory->stat_blocking_factor() * 100),

                'hideout' => (bool)$hideout,
                'zombies' => [
                    'pop' => Globals::PrimaryPlayerF()->location()->zombie_pop(),
                    'list' => $z_list
                ]
            ]
        ]);

        // Get ruin radar
        if ($lomap = $this->get_labyrinth())
            $this->add_data('location', [
                'lomap' => $lomap
            ]);
    }

    /**
     * @param                           $itemlist Model_Items_Abstract_Item[]
     * @param bool                      $short
     * @param Interface_Plentity[]|null $players
     *
     * @return array
     * @throws Exception
     */
    private function group_itemlist($itemlist, $short = false, $players = null): array
    {
        $grouping = Array();
        $cache = Array();

        // iterate over item list
        foreach ($itemlist as $item)
        {
            // Create category array
            if (!isset($grouping[$item->cat()])) $grouping[$item->cat()] = ['items' => [], 'name' => ''];

            // Check if our item supports static stacking
            $static = Tool_System::instance_of($item, 'Interface_Static');
            // If we support stacking and a stack for this item exists, simply add to stack
            $id = get_class($item) . '/' . $item->icon() . '/' . $item->name();
            if ($static && isset($cache[$id])) {
                $link = &$grouping[$item->cat()]['items'][$cache[get_class($item) . '/'
                . $item->icon() . '/' . $item->name()]];
                $link['static']++;
                $link['set'][] = $item->uin();
                continue;
            // If we support stacking and a stack for this item does not exists, create one
            } elseif ($static)
                $cache[get_class($item) . '/' . $item->icon() . '/' . $item->name()] = $item->uin();

            $flags = [];


            // Set item flags
            /** @noinspection PhpUndefinedMethodInspection */
            if (Tool_System::instance_of($item, Model_Items_Abstract_Equipable::cls()) && $item->is_equipped())      $flags[] = 'equipped';
            /** @noinspection PhpUndefinedMethodInspection */
            if (Tool_System::instance_of($item, Model_Items_Leash::cls()) && $item->is_active())                $flags[] = 'equipped';
            /** @noinspection PhpUndefinedMethodInspection */
            if (Tool_System::instance_of($item, Model_Items_Abstract_Equipable::cls()) && $item->allows_primary() && $item->is_equipped_primary()) $flags[] = 'primary';
            if (Tool_System::instance_of($item, Model_Items_Abstract_Armor::cls()))                          $flags[] = 'armor';
            if (Tool_System::instance_of($item, 'Model_Combat_Weapon'))                                 $flags[] = 'weapon';
            if (Tool_System::instance_of($item, Model_Items_Abstract_Escape::cls()))                         $flags[] = 'escape';
            if (Tool_System::instance_of($item, 'Interface_Tmpitem'))                                   $flags[] = 'temp';
            if (Tool_System::instance_of($item, 'Interface_Event'))                                     $flags[] = 'event';
            if ($item->is_carrier_item())                                                               $flags[] = 'carrier';

            $data = [
                'name' => __($item->name()),
                'description' => $short ? '' : __($item->description()),
                'icon' => $item->icon(),
                'weight' => $item->weight(),
                'actions' => $short ? [] : $this->prepare_actionlist($item->auto_actions($players)),
                'addr' => Tool_System::getClassID($item),
                'flags' => $flags,
                'uin' => $item->uin(),
                'set' => [$item->uin()],
                'static' => 1,
                'stack' => __($item->stackname()),
                'label' => $item->label(),
                'deco' => $item->deco()
            ];

            /** @var Model_Items_Abstract_Equipable $item */
            if (Tool_System::instance_of($item, Model_Items_Abstract_Equipable::cls())) {
                $data['rpg'] = [
                    'ini' => $item->get_stats(Model_Items_Abstract_Equipable::MIAE_STAT_INI),
                    'atk' => $item->get_stats(Model_Items_Abstract_Equipable::MIAE_STAT_ATK),
                    'def' => $item->get_stats(Model_Items_Abstract_Equipable::MIAE_STAT_DEF),
                    'acc' => $item->get_stats(Model_Items_Abstract_Equipable::MIAE_STAT_ACC)
                ];
                $data['equipment'] = [
                    'name' => __($item::convertStringType()),
                    'primary_cat' => $item->allows_primary(),
                ];
            }

            /** @var Model_Items_Abstract_Armor $item */
            if (Tool_System::instance_of($item, Model_Items_Abstract_Armor::cls()))
                $data['armor'] = [
                    'condition' => __($item->convertStringProtection()),
                    'hp' => $item->get_hp(),
                    'type' => __($item::convertStringType())
                ];

            /** @var Model_Combat_Weapon $item */
            if (Tool_System::instance_of($item, 'Model_Combat_Weapon')) {

                $ammo = [];
                foreach ($item->get_ammo_icons() as $entry) {
                    $c = 1;
                    if (is_array($entry)) {
                        $tmp = $entry;
                        [$entry,$c] = $tmp;
                    }

                    for ($i = 0; $i < $c; $i++)
                        switch ($entry) {
                            case '::energy': break;
                            default:
                                $ammo[] = $entry;
                                break;
                        }
                }


                /** @var Model_Combat_Weapons_Energy $item */
                $data['weapon'] = [
                    'damage' => $item->potential_damage(),
                    'ammo' => $ammo ?: false,
                    'shots' => Tool_System::instance_of($item, 'Model_Combat_Weapons_Fillable') ? $item->count() : false,
                    'energy' => Tool_System::instance_of($item, 'Model_Combat_Weapons_Energy') ? $item->energy() : 0,
                    'accuracy' => $item->fixed_accuracy() ? $item->accuracy() * 100 : true,
                    'breakable' => ($item->durabillity() < 1) || Tool_System::instance_of($item, 'Model_Combat_Weapons_Throwable'),
                ];
            }

            // No item specifics for short
            if (!$short) {
                if (Tool_System::instance_of($item, 'Interface_Countable') && !Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls())) {
                    $data['count'] = $item->count();
                    $data['capacity'] = $item->capacity();
                }

                if (Tool_System::instance_of($item, Model_Items_Ammobelt::cls())) {
                    $tmp_ammo = [];

                    /** @var Model_Items_Ammobelt $item */
                    foreach ($item->contains() as $class => $value) {
                        /** @var Model_Items_Abstract_Ammo $class */
                        $tmp_ammo[] = [
                            'addr' => Tool_System::getClassID($class),
                            'icon' => $class::static_icon(),
                            'count' => $value
                        ];
                    }

                    $data['ammobelt'] = $tmp_ammo;
                }

                /** @var Interface_Fillable|Model_Items_Abstract_Bottle $item */
                if (Tool_System::instance_of($item, 'Interface_Fillable') || Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls())) {
                    $data['count'] = (int)$item->fillrate();
                    $data['fill'] = [
                        'capacity' => $item->capacity(),
                        'fixed' => !Tool_System::instance_of($item, Model_Items_Abstract_Bottle::cls())
                    ];
                }

                $data['widgets'] = [];

                if (Tool_System::instance_of($item, 'Interface_Label'))
                    $data['widgets'][] = 'label';

                if (Tool_System::instance_of($item, Model_Items_Abstract_Liquid::cls()))
                    $data['widgets'][] = 'water';

                if (Tool_System::instance_of($item, Model_Items_Chem::cls()))
                    $data['widgets'][] = 'chem';

                if (Tool_System::instance_of($item, Model_Items_Abstract_Pillbox::cls()))
                    $data['widgets'][] = 'pillbox';

                $data['widgets'] = implode( " ",  $data['widgets']);
            }


            // Build final item object
            $grouping[$item->cat()]['items'][$item->uin()] = $data;
        }

        // Sort items based on their address
        foreach (array_keys($grouping) as $gid)
            uasort($grouping[$gid]['items'], function($a, $b) {return strcmp($a['addr'], $b['addr']);});

        // Set category strings
        foreach (array_keys($grouping) as $gid)
            $grouping[$gid]['name'] = __(Model_Items_Abstract_Item::translateCatID($gid));

        return $grouping;
    }

    /**
     * @param bool|Interface_Plentity $remote
     * @param bool                    $full_data
     * @return array|null
     * @throws Exception
     */
    private function render_inventory($remote = false, $full_data = false): ?array
    {
        $full_data = !(bool)$remote || $full_data;
        $p = $remote ?: Globals::PrimaryPlayerF();

        // Get heroic actions
        $a = [];
        $action = false;

        /** @var Model_Buffs_Abstract_Fragile $buff */
        if (!($buff = $p->get_status()->retrieve('fragile')) && !$remote)
            foreach (Tool_Scripts::get_items(Model_Items_Abstract_Virtual::cls(), Struct_ScriptItemSource::onlyPlayer()->use_perspective($p)) as $a_item)
                $a = array_merge($a,$this->prepare_actionlist($a_item->auto_actions(), $a_item));
        elseif ($buff) $action = [
            'name' => __($buff->name()),
            'desc' => __($buff->description()),
            'abort' => $buff->abortable(),
            'remaining' => $buff->lifetime() > 0 ? Tool_Numerics::duration_to_split($buff->lifetime()) : false
        ];

        $ap_list = $remote ? [] : array_merge([$p], Tool_Scripts::comrades($p->location_class(), true, true));

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        $tmp = [
            'player' => $this->group_itemlist($p->inventory()->get(), $remote ? !$full_data : false, [$p]),
            'weight' => [$p->inventory()->weight(),$p->inventory()->limit()],
            'location' => $remote ? [] : $this->group_itemlist($p->location()->inventory()->get(), false, $ap_list),
            'home' => $remote ? false : (bool)Tool_Scripts::current_location_hideout(),
            'heroics' => $a,
            'action' => $action
        ];

        if ($remote) return $tmp;
        else {
            $this->add_data('inventory', $tmp);
            return null;
        }
    }

    private function condense_buff($bar): array
    {
        $ret = Array();
        foreach (Globals::PrimaryPlayerF()->get_status()->buffs() as $buff) {
            $t = ['icon' => $buff->icon(), 'effects' => []];
            $apply = false;
            foreach ([Model_Buffs_Abstract_Buff::MB_RAISE_ACC, Model_Buffs_Abstract_Buff::MB_DROP_ACC, Model_Buffs_Abstract_Buff::MB_RAISE_PRC, Model_Buffs_Abstract_Buff::MB_DROP_PRC] as $id) {
                $effect = $buff->effect($bar, $id);
                $t['effects'][$id] = $effect;
                $apply = $apply || ($effect !== 0);
            }
            if ($apply) $ret[] = $t;
        }

        return $ret;
    }

    /**
     * @param int               $type
     * @param bool|Model_Player $remote
     * @return array
     * @throws Exception
     */
    private function status($type, $remote = false): array {
        $p = $remote ?: Globals::PrimaryPlayerF();
        return [
            'value' => round($p->get_status()->get($type),$type >= Model_Status::MS_THRESHOLD ? 4 : 2),
            'buffs' => $remote ? [] : $this->condense_buff($type)
        ];
    }

    /**
     * @param bool|Interface_Plentity $remote
     * @return array|null
     * @throws Exception
     */
    private function render_status($remote = false): ?array
    {
        $p = $remote ?: Globals::PrimaryPlayerF();

        $cache = [];
        for ($type = 1; $type <= Model_Status::MS_STATUS_COUNT; $type++)
            if ($type <= 5 || $p->get_status()->get($type))
                $cache[$type] = $this->status($type, $remote);
        for ($type = Model_Status::MS_THRESHOLD; $type < (Model_Status::MS_THRESHOLD + Model_Status::MS_CHAR_COUNT); $type++)
            if ($p->get_status()->get($type) !== 1.0)
                $cache[$type] = $this->status($type, $remote);

        $buffs = [];
        foreach ($p->get_status()->buffs() as $buff) if ($buff->visible() && (!$remote || $buff->visible(true)))
            $buffs[] = ['icon' => $buff->icon(), 'name' => __($buff->name()), 'desc' => $remote ? '' : __($buff->description()), 'time' => $buff->lifetime() < 0 ? false : Tool_Numerics::duration_to_string($buff->lifetime())];

        $tmp = [
            'bars' => $cache,
            'buffs' => $buffs
        ];

        if ($remote) return $tmp;
        else {
            $this->add_data('status', $tmp);
            return null;
        }
    }

    protected function render_notifications(): void
    {
        foreach (Globals::PrimaryPlayerF()->log()->get_all() as $message) {
            $r = $message->as_notification();
            if ($r) $this->add_note($r[0],$r[1],$r[2]);
        }

        Globals::PrimaryPlayerF()->log()->clear();
    }

    protected function render_log(): void
    {
        $ret = [];
        foreach (Globals::PrimaryPlayerF()->location()->log()->get_all(true) as $message)
            $ret[] = array_merge(['new' => false], $message->render());
        Globals::PrimaryPlayerF()->location()->log()->reset_news_counter();

        $this->add_data('log', $ret);
    }

    private function render_clock(): void
    {
        $this->add_data('clock', [
            'show' => Globals::CurrentGameF()->is_alive() && !Globals::CurrentGameF()->paused() && Globals::PrimaryPlayerF() && Globals::PrimaryPlayerF()->get_status()->alive() && !Tool_Events::is_april_fools(),
            'next_tick' => Globals::CurrentGameF()->next_tick(),
            'last_tick' => Globals::CurrentGameF()->now(),
            'current' => time(),
            'ingame' => Tool_Scripts::get_daytime()->getTimestamp(),
        ]);
    }

    private function render_info(): void
    {
        $this->add_data('game', [
            'mode' => __(Tool_Gamemodes::get_board_by_id(Globals::CurrentGameF()->setting_mode())),
            'job' => __(Tool_Gamemodes::get_job_by_id(Globals::PrimaryPlayerF()->job())),
            'level' => Globals::PrimaryPlayerF()->job(false),
            'gametime' => Tool_Numerics::duration_to_string(Globals::CurrentGameF()->duration()),
            'lifetime' => Tool_Numerics::duration_to_string(min(Globals::CurrentGameF()->duration(),Globals::PrimaryPlayerF()->get_lifetime())),
            'points' => Globals::CurrentGameF()->points(Globals::PrimaryPlayerF()->id()),
            'kills' => Globals::PrimaryPlayerF()->achievements()->get_achievements(Model_Achievement::MA_KILLED_ZOMBIES)
        ]);
    }

    private function render_settings(): void
    {
        $tmp =  (Globals::CurrentGameF()->timeflow() === 0) ? [
            'interval' => (int)Kohana::$config->load('balancing.pause.min_interval'),
            'duration' => (int)Kohana::$config->load('balancing.pause.min_duration'),
        ] : [
            'game' => Globals::CurrentGameF()->tick_length(),
            'player' => Globals::PrimaryPlayerF()->vote_time(),
            'selection' => [15,30,60,120,300,600,900],
            'interval' => (int)Kohana::$config->load('balancing.pause.min_duration'),
        ];

        $lock = ((Globals::CurrentGameF()->timeflow() === 0) ? (Globals::CurrentGameF()->pauselock() + Kohana::$config->load('balancing.pause.min_interval')) : Globals::PrimaryPlayerF()->vote_time(true)) - time();
        if ($lock < 0) $lock = false;


        $this->add_data('settings', [
            'clock' => [
                'time_mode' => Globals::CurrentGameF()->timeflow(),
                'time_settings' => $tmp,
                'locked' => $lock
            ],
            'ai' => Globals::PrimaryPlayerF()->ai()
        ]);
    }

    private function render_rpg(): void
    {
        $char_stats = Globals::PrimaryPlayerF()->battle_stats();
        $tmp_all = [
            Model_Items_Abstract_Equipable::MIAE_STAT_INI => $char_stats[0],
            Model_Items_Abstract_Equipable::MIAE_STAT_ATK => $char_stats[1],
            Model_Items_Abstract_Equipable::MIAE_STAT_DEF => $char_stats[2],
            Model_Items_Abstract_Equipable::MIAE_STAT_ACC => $char_stats[3],
        ];


        $stats = [
            Model_Items_Abstract_Equipable::MIAE_STAT_INI => [['type' => 0, 'value' => $char_stats[0], 'all' => $tmp_all]],
            Model_Items_Abstract_Equipable::MIAE_STAT_ATK => [['type' => 0, 'value' => $char_stats[1], 'all' => $tmp_all]],
            Model_Items_Abstract_Equipable::MIAE_STAT_DEF => [['type' => 0, 'value' => $char_stats[2], 'all' => $tmp_all]],
            Model_Items_Abstract_Equipable::MIAE_STAT_ACC => [['type' => 0, 'value' => $char_stats[3], 'all' => $tmp_all]],
        ];
        foreach (Globals::PrimaryPlayerF()->get_equipment(null, true) as $equipment)
            foreach ([Model_Items_Abstract_Equipable::MIAE_STAT_INI,Model_Items_Abstract_Equipable::MIAE_STAT_ATK,Model_Items_Abstract_Equipable::MIAE_STAT_DEF,Model_Items_Abstract_Equipable::MIAE_STAT_ACC] as $k)
                if ($equipment->get_stats($k) !== 0)
                    $stats[$k][] = [
                        'type' => $equipment->get_equipment_type(),
                        'value' => $equipment->get_stats($k),
                        'name' => __($equipment->name()),
                        'icon' => $equipment->icon(),
                        'all' => $equipment->get_stats(true),
                    ];

        $this->add_data('rpg', [
            'stats' => $stats
        ]);
    }

    private function render_specials(): void
    {
        // Colosseum
        if (Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Colosseum')) {
            /** @var Model_Places_Colosseum $colosseum */
            $colosseum = Globals::PrimaryPlayerF()->location();
            $cfg = $colosseum->get_config();
            if ($cfg['distance'] < 10)		$arena = 0;
            elseif ($cfg['distance'] < 30)	$arena = 1;
            elseif ($cfg['distance'] < 60)	$arena = 2;
            else							$arena = 3;

            $this->add_data('location', ['colosseum' => [
                'level' => $colosseum->level(),
                'rank' => min(4,floor(($colosseum->level() - 1)/5) + 1),
                'arena' => $arena
            ]]);
        }

        //Last Scout
        if (Globals::CurrentGameF()->config('modules.mapping') && Globals::PrimaryPlayerF()->inventory()->get(Model_Items_Maptool::cls()) && !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Abstract_Xmas') && !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Abstract_Hideout') && !Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Abstract_Node')) {
            /** @var Model_Items_Maptool $mapper */
            $mapper = Globals::PrimaryPlayerF()->inventory()->get(Model_Items_Maptool::cls()); $mapper = $mapper[0];

            $level = $mapper->get_map_details();
            if ($level >= 0)
                $this->add_data('location', ['scouting' => [
                    'level' => $level * 33 + ($level === 3 ? 1 : 0),
                    'laser' => Tool_Scripts::count_items(Model_Items_Generic_Lasermapper::cls()),
                ]]);
        }

        // Roadtrip
        if (Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Motorhome')) {
            /** @var Model_Places_Motorhome $motorhome */
            $motorhome = Globals::PrimaryPlayerF()->location();

            $parts = [];
            foreach ($motorhome->get_parts() as $item => $status) {
                /** @var Model_Items_Abstract_Item $item */
                $parts[] = [
                    'icon' => $item::static_icon(),
                    'name' => __($item::static_name()),
                    'addr' => Tool_System::getClassID($item),
                    'count' => $status[0],
                    'max' => $status[1],
                ];
            }

            $this->add_data('location', ['caravan' => [
                'stops' => $motorhome->get_level_progress(),
                'speed' => $motorhome->get_speed(true),
                'distance' => $motorhome->get_distance(),
                'parts' => $parts,
                'weight' => [$motorhome->weight(), $motorhome->weight_max()]
            ]]);
        }

        // XMAS
        if (Tool_System::instance_of(Globals::PrimaryPlayerF()->location(), 'Model_Places_Xmasfair')) {
            /** @var Model_Places_Xmasfair $fair */
            $fair = Globals::PrimaryPlayerF()->location();

            $tree = $fair->get_construction_info();
            foreach ($tree as &$entry) {
                $entry['name'] = __($entry['name']);
                $entry['requires'] = [];
                /** @var Model_Items_Abstract_Item $cls  */
                foreach ($entry['items'] as $cls => $count) {
                    $entry['requires'][] = [
                        'name' => __($cls::static_name()),
                        'count' => $count,
                        'have' => Tool_Scripts::count_items($cls),
                        'icon' => $cls::static_icon()
                    ];
                }
                unset($entry['items']);
            }
            unset($entry);


            $this->add_data('location', ['xmasfair' => [
                'tree' => $tree,
                'deco' => $fair->get_decoration_value()
            ]]);
        }
    }

    private function render_epics(): void
    {
        /** @var Model_Items_Virtual_Epic_Garden $garden */
        if ($garden = Tool_Scripts::first_item(Model_Items_Virtual_Epic_Garden::cls(),Struct_ScriptItemSource::onlyLocation()))
            $this->add_data('location', ['epc_garden' => [
                'planted' => $garden->get_planted_state(),
                'harvest' => $garden->get_harvest_prc(),
                'time' => (!$garden->get_planted_state() || $garden->get_harvest_state()) ? false : Tool_Numerics::duration_to_string($garden->get_time_to_harvest()),
                'time_water' => (!$garden->get_planted_state() || $garden->get_harvest_state() || $garden->get_time_to_water() <= 0) ? false : Tool_Numerics::duration_to_string($garden->get_time_to_water()),
                'time_water2' => (!$garden->get_planted_state() || $garden->get_time_to_water(false) <= 0) ? false : Tool_Numerics::duration_to_string($garden->get_time_to_water(false)),
                'water' => $garden->get_water_prc(),
                'quality' => !$garden->get_planted_state() ? false : $garden->get_quality(),
                'fertilizer' => $garden->get_fertilizer_status(),
                'actions' => $this->prepare_actionlist($garden->auto_actions()),
            ]]);

        /** @var Model_Items_Virtual_Epic_Raven $raven */
        if ($raven = Tool_Scripts::first_item(Model_Items_Virtual_Epic_Raven::cls(),Struct_ScriptItemSource::onlyLocation()))
            $this->add_data('location', ['epc_raven' => [
                'doped' => $raven->is_doped(),
                'time' => !$raven->get_rest() ? false : Tool_Numerics::duration_to_string($raven->get_rest()),
                'size' => $raven->get_inventory_size(),
                'capacity' => $raven->get_inventory_capacity(),
                'actions' => $this->prepare_actionlist($raven->auto_actions()),
            ]]);

        /** @var Model_Items_Virtual_Epic_Fence $fence */
        if ($fence = Tool_Scripts::first_item(Model_Items_Virtual_Epic_Fence::cls(),Struct_ScriptItemSource::onlyLocation()))
            $this->add_data('location', ['epc_fence' => [
                'status' => $fence->get_status(),
                'time' => (!$fence->get_remaining_power() && !$fence->get_status()) ? false : Tool_Numerics::duration_to_string($fence->get_remaining_power()),
                'energy' => Tool_Scripts::count_items(Model_Items_Energy::cls(), Struct_ScriptItemSource::onlyLocation()),
                'actions' => $this->prepare_actionlist($fence->auto_actions()),
            ]]);
    }

    protected function render_mp($slim = false): void
    {
        $speeds = [15,30,60,120,300,600,900];
        $jokes = [
            'Letzter Unterhosenwechsel' => 'Vor 12 Wochen',
            'Höchster Schulabschluss' => 'Kindergarten',
            'Geschlechtskrankheiten' => '[nt]4',
            'Lieblingskünster' => 'Helene Fischer',
            'Lieblings-Item' => 'Massagestab',
            'Spieler-ID' => 'ID10T',
            'Browser' => 'Internet Explorer 6',
            'Betriebssystem' => 'MUTOS 1600',
        ];

        $players = [];
        foreach (Globals::CurrentGameF()->players(false) as $p) {
            if ($p->id() === Globals::PrimaryPlayerF()->id()) continue;

            $joke = array_keys($jokes)[(Globals::CurrentGameF()->id() + $p->id()) % count($jokes)];

            $local = $p->get_status()->alive() && $p->location_class() === Globals::PrimaryPlayerF()->location_class();
            $companion = (bool)Tool_Scripts::check_comrade($p);
            $allow = [];
            if ($p->allow(Interface_Plentity::IC_ALLOW_ANY))
                $allow = true;
            else
                foreach ($p->allow() as $tag)
                    $allow[$tag] = true;

            $id = $p->id();
            $players[$id] = [
                'name' => $p->name(),
                'id' => $id,
                'speed' => $speeds[$p->vote_time()],
                'local' => $local,
                'loner' => (bool)$p->get_status()->retrieve('tr_loner'),
                'stats' => $local ? $this->render_status($p) : false,
                'inventory' => ($local && $p->allow(Interface_Plentity::IC_ALLOW_SHOW_INVENTORY)) ? $this->render_inventory($p) : false,
                'escort' => $companion,
                'allow' => $allow,
                'last_seen' => $p->last_action(),
                'job' => __(Tool_Gamemodes::get_job_by_id($p->job())),
                'icon' => $p->icon(),
                'npc' => false,
                'joke' => [
                    0 => __($joke),
                    1 => __($jokes[$joke])
                ]
            ];
        }

        foreach (Globals::CurrentGameF()->npcs(true) as $n) {
            if (!$n->get_status()->alive() || $n->location_class() !== Globals::PrimaryPlayerF()->location_class()) continue;

            $allow = [];
            if ($n->allow(Interface_Plentity::IC_ALLOW_ANY))
                $allow = true;
            else
                foreach ($n->allow() as $tag)
                    $allow[$tag] = true;

            $id = $n->id();
            $players[$id] = [
                'name' => $n->name(),
                'id' => $id,
                'local' => true,
                'loner' => false,
                'stats' => $this->render_status($n),
                'inventory' => $n->allow(Interface_Plentity::IC_ALLOW_SHOW_INVENTORY)
                    ? $this->render_inventory($n, $n->allow(Interface_Plentity::IC_ALLOW_ITEMS_USE)) : false,
                'escort' => $n->companion(),
                'allow' => $allow,
                'npc' => true,
                'actions' => $this->prepare_actionlist($n->hid()->convert("npc//{$n->id()}", [Globals::PrimaryPlayerF()])),
                'icon' => $n->icon(),
                'info' => [
                    'species' => __($n->entity_species()),
                    'profession' => __($n->entity_profession()),
                    'desc' => __($n->entity_description()),
                    'action' => $n->entity_action() ? __($n->entity_action()) : __('Bereit'),
                ]
            ];
        }

        if (!$slim) $this->add_data('chat', Controller_Chat::tokenize(Globals::PrimaryPlayerF()->id(),Globals::CurrentGameF()->id(),true));

        $this->add_data('players', [
            'multiplayer' => Globals::CurrentGameF()->config('modules.multiplayer'),
            'messages' => count(Globals::PrimaryPlayerF()->get_postbox()->get(false,true)) > 0,
            'others' => $players,
            'self' => [
                'escort' => Globals::PrimaryPlayerF()->companion(),
                'ping' => Globals::PrimaryPlayerF()->get_postbox()->beacon()
            ]
        ]);
    }

    /**
     * Renderer API
     *
     * @return bool
     * @throws Kohana_Exception
     */
    public function japi_data(): bool {
        if (!Globals::PrimaryPlayerF()->get_status()->alive()) {
            $this->render_notifications();
            return $this->render(['redirect' => 'game/redirect']);
        }

        if ($this->is_silent() || Tool_Scripts::is_npc(Globals::PrimaryPlayerF())) return true;

        Globals::PrimaryPlayerF()->get_status()->rebuild();

        $this->render_info();
        $this->render_location();
        $this->render_inventory();
        $this->render_status();
        $this->render_settings();
        $this->render_clock();
        $this->render_rpg();
        $this->render_log();
        $this->render_mp();
        $this->render_specials();
        $this->render_epics();
        $this->render_notifications();

        $version_data = Kohana::$config->load('build.version');
        $this->add_data('version', "{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']}");

        $this->render(false);
        return true;
    }

    public function japi_fixlink(): bool {
        if (!Globals::CurrentGameF() && Globals::CurrentUserF()->get_current_game())
            return $this->render(['success' => DB::delete('xref_game_player')->where('uid','=',Globals::CurrentUserF()->uid())->execute() ? 1 : 0]);
        else return $this->render(['success' => 0]);
    }

    /**
     * Load appropriate game interface (in-game/death/pause/aprils fools)
     */
    public function action_redirect(): bool
    {
        //Redirect
        if (!Globals::hasCurrentGame() && !Globals::CurrentUserF()->get_current_game())
            self::redirect(URL::site('gamemaster/lobby',true));
        elseif (!Globals::CurrentGameF() && Globals::CurrentUserF()->get_current_game()) {
            $this->add_widget(View::factory('pages/game_error')->render());
            return $this->render();
        }
        if (!Globals::hasPrimaryPlayer()) {
            Globals::resetCurrentGame();
            $this->session->delete('game');
            self::redirect(URL::site('landing/redirect',true));
        }

        //Check if player is alive
        if (Globals::PrimaryPlayerF()->get_status()->alive()) {
            Globals::PrimaryPlayerF()->last_action(true);

            if (Globals::CurrentGameF()->paused())
                // Load pause screen
                $this->add_widget(View::factory('pages/pause')->set('remaining', max(0,Globals::CurrentGameF()->pauselock() - (time() - Kohana::$config->load('balancing.pause.min_duration'))))->render());
            else
                // Ingame View
                $this->add_widget(View::factory('pages/ingame')->render());
        }
        //Show game summary
        else {
            $a_points = 0;
            $a_data = [];
            foreach (Globals::PrimaryPlayerF()->achievements()->get_all() as $aid => $value) {
                $a_points += Model_Achievement::points_aid($aid) * $value;
                $a_data[$aid] = [
                    'id' => $aid,
                    'icon' => $aid . '.gif',
                    'name' => Model_Achievement::decode_aid($aid),
                    'class' => Model_Achievement::class_aid($aid),
                    'count' => $value
                ];
            }

            $player_ratings = [];
            if (Globals::CurrentGameF()->is_rankable() && Globals::CurrentGameF()->points(Globals::PrimaryPlayerF()->id()) > 0)
                foreach (Globals::CurrentGameF()->players(false) as $p)
                    if ($p->id() !== Globals::PrimaryPlayerF()->id())
                    $player_ratings[$p->id()] = [
                        'prev_rating' => Model_Euser::get_karma($p->id(), Globals::PrimaryPlayerF()->id()),
                        'name' => $p->name()
                    ];

            $this->add_widget(View::factory('pages/death')
                ->set('soul_points', Globals::CurrentGameF()->points(Globals::PrimaryPlayerF()->id()))
                ->set('ach_points', $a_points)
                ->set('achievements', $a_data)
                ->set('rankable', Globals::CurrentGameF()->is_rankable())
                ->set('time', Tool_Numerics::duration_to_string(Globals::PrimaryPlayerF()->get_lifetime()))
                ->set('split_time', Tool_Numerics::duration_to_split(Globals::PrimaryPlayerF()->get_lifetime()))
                ->set('cause_of_death', Globals::PrimaryPlayerF()->get_status()->get_cause_of_death())
                ->set('braincoins',      Globals::PrimaryPlayerF()->get_braincoins(false) * (Globals::PrimaryPlayerF()->get_lifetime() >= 288 ? 1 : -1))
                ->set('braincoins_real',   Globals::PrimaryPlayerF()->get_braincoins(true) * (Globals::PrimaryPlayerF()->get_lifetime() >= 288 ? 1 : -1))
                ->set('braincoins_factor', Globals::PrimaryPlayerF()->get_braincoin_factor())
                ->set('braincoins_account', Model_User::get_coins(Globals::PrimaryPlayerF()->id()))
                ->set('ratings', $player_ratings ?: null)
                ->render());
        }

        return $this->render();
    }

    public function action_pm(): bool
    {
        //Redirect
        if (!Globals::CurrentGameF() || !Globals::CurrentUserF()->get_current_game() || !Globals::hasPrimaryPlayer() || !Globals::PrimaryPlayerF()->get_status()->alive() || !Globals::CurrentGameF()->config('modules.multiplayer'))
            self::redirect(URL::site('game/redirect',true));

        $players = [];
        foreach (Globals::CurrentGameF()->players(false) as $p)
            if ($p->id() !== Globals::PrimaryPlayerF()->id())
                $players[$p->id()] = $p->name();

        $this->add_widget(View::factory('pages/pm')->set('messages', Globals::PrimaryPlayerF()->get_postbox()->get())->set('players', $players)->render());
        foreach (Globals::PrimaryPlayerF()->get_postbox()->get(false,true) as $msg)
            Globals::PrimaryPlayerF()->get_postbox()->read($msg['mid']);

        return $this->render();
    }

    public function japi_end(): void
    {
        //Check if player is still alive
        if (!Globals::PrimaryPlayerF()->get_status()->alive())
        {
            if (($r = self::post('ratings')) && Globals::CurrentGameF()->is_rankable() && Globals::CurrentGameF()->points(Globals::PrimaryPlayerF()->id()) > 0)
                foreach (Globals::CurrentGameF()->players(false) as $p) if ($p->id() !== Globals::PrimaryPlayerF()->id() && isset($r[$p->id()]) && is_numeric($r[$p->id()]))
                    Model_User::set_karma($p->id(), Globals::PrimaryPlayerF()->id(), min(2,max(-2,(int)$r[$p->id()])));


            //End game and delete game object from session
            if (Globals::CurrentGameF()->retire(Globals::CurrentUserF()->uid())) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->session->delete('game');
                unset($GLOBALS['game']);
            }
        }

        //Redirect
        $this->render([
            'redirect' => 'lobby/main',
        ]);
    }

    public function japi_logs(): bool
    {
        $this->render_log();
        $this->render_notifications();

        $version_data = Kohana::$config->load('build.version');
        $this->add_data('version', "{$version_data['major']}.{$version_data['minor']}.{$version_data['service']}-{$version_data['maintenance']}-{$version_data['stage']}-{$version_data['build']}");

        $this->render(false);
        return true;
    }

}