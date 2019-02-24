<?php /** @noinspection NotOptimalIfConditionsInspection */
defined('SYSPATH') OR die('No direct access allowed.');

class Tool_Scripts
{

    /**
     * @param number            $t
     * @param Model_Player|null $p
     *
     * @throws Exception
     */
    public static function rebuild_primary_equipment($t, $p = null): void
    {
        if (!$p)
            foreach (Globals::CurrentGameF()->players() as $player)
                static::rebuild_primary_equipment($t, $player);
        else {
            $items = $p->get_equipment($t);
            if (!count($items) || !$items[0]->allows_primary()) return;

            if (!$p->get_equipment($t, true))
                $items[0]->equip_primary($p);
        }
    }

    /**
     * Counts available items
     *
     * @param string                       $classname Restrict items to a specific class and its descendants
     * @param Struct_ScriptItemSource|null $source Item source
     *
     * @return int
     * @throws Exception
     */
    public static function count_items($classname, ?Struct_ScriptItemSource $source = null): int {
        $source = $source ?: new Struct_ScriptItemSource();

        $d = 0;

        //Add ammo-belt items
        if (Tool_System::instance_of($classname, Model_Items_Abstract_Ammo::cls()))
            foreach (self::get_items(Model_Items_Ammobelt::cls(), $source->copy()->use_decider(null)) as $belt)
                /** @var $belt Model_Items_Ammobelt */
                $d += $belt->has($classname);

        // Add stackable items
        elseif (Tool_System::instance_of($classname, Model_Items_Abstract_Stackable::cls()))
            foreach (self::get_items($classname, $source) as $instance)
                $d += $instance->count();

        // Add non-stackable items
        else return count(self::get_items($classname, $source));

        return $d;
    }

    /**
     * Counts a list of available items.
     * If both $classname and $sources are arrays, they have to be of equal length.
     * @param string|string[]                                        $classnames
     * @param Struct_ScriptItemSource|Struct_ScriptItemSource[]|null $sources
     *
     * @return int[]
     * @throws Exception
     */
    public static function count_item_matrix($classnames, $sources ): array {
        $sources = $sources ?: new Struct_ScriptItemSource();

        if (!is_array($classnames) && !is_array($sources)) return [ self::count_items($classnames,$sources) ];

        if (is_array($classnames) && is_array($sources)) {
            if (count($classnames) !== count($sources)) throw new LogicException(
                'Script error: Matrix item processing with unequal array length.'
            );
            return array_map(function(string $classname, ?Struct_ScriptItemSource $source) {
                return self::count_items($classname,$source);
            }, $classnames, $sources);
        }

        if (is_array($classnames)) return array_map(function(string $classname) use ($sources) {
            return self::count_items($classname,$sources);
        }, $classnames);

        if (is_array($sources)) return array_map(function(?Struct_ScriptItemSource $source) use ($classnames) {
            return self::count_items($classnames,$source);
        }, $sources);

        return [];
    }

    /**
     * @param Struct_ItemEntry[]           $items
     * @param Struct_ScriptItemSource|null $source
     *
     * @return bool
     * @throws Exception
     */
    public static function has_items(array $items, ?Struct_ScriptItemSource $source = null): bool
    {
        foreach ($items as $item)
            if (static::count_items($item->class, $source) < $item->count)
                return false;
        return true;
    }

    /**
     * @param Struct_ItemEntry[]           $items
     * @param Struct_ScriptItemSource|null $source
     * @param bool                         $grind
     *
     * @return bool
     * @throws Exception
     */
    public static function consume_items($items, ?Struct_ScriptItemSource $source = null, bool $grind = false): bool
    {
        foreach ($items as $item)
            if (static::count_items($item->class, $source) < $item->count)
                return false;

        /**
         * @var $belt Model_Items_Ammobelt
         * @var $item Model_Items_Abstract_Item
         */
        foreach ($items as $item) {
            $remaining = $item->count;

            if (Tool_System::instance_of($item->class, Model_Items_Abstract_Ammo::cls()))
                foreach (self::get_items(Model_Items_Ammobelt::cls(), $source !== null ? $source->copy()->use_decider(null) : null) as $belt)
                    if ($belt->get($item->class, $remaining)) break;
                    else {
                        $remaining -= $belt->has($item->class);
                        $belt->get($item->class, $belt->has($item->class));
                    }
            elseif (Tool_System::instance_of($item->class, Model_Items_Abstract_Stackable::cls())) {
                foreach (self::get_items($item->class, $source) as $instance) {
                    if ($instance->count() > $remaining) {
                        for ($i = 0; $i < $item->count; $i++) $instance->consume();
                        break;
                    }

                    if ($instance->count() === $remaining) {
                        $instance->grind();
                        break;
                    }

                    $remaining -= $instance->count();
                    $instance->grind();
                }

            } else
                foreach (static::get_items($item->class, $source) as $instance) {
                    if ($grind)
                        $instance->grind();
                    else $instance->consume();
                    if (--$remaining <= 0) break;
                }
        }

        if ($source === null) $source = Struct_ScriptItemSource::default();
        $player = $source->get_player();

        if ($source->from_player)   $player->inventory()->reset_weight();
        if ($source->from_location) $player->location()->inventory()->reset_weight();
        if ($source->from_others)
            foreach (self::at_location($player->location_class()) as $s_player)
                if ($s_player->uin() !== $player->uin())
                    $s_player->inventory()->reset_weight();

        return true;
    }

    public static function getBrainCoinLikelinessLevel($lid = null) {
        // Get zombie factory;
        $factory = $lid ? Globals::CurrentGameF()->locationF($lid)->zombie_factory() : Globals::CurrentPlayerF()->location()->zombie_factory();
        $radar_prop = $factory->stat_chance_battle();
        $radar_increase = $factory->stat_chance_block();

        // Check if we're at a hideout with active defenses
        $hideout = self::current_location_hideout();
        $protected_hideout = $hideout && $hideout->get_defense() > 0;

        // Calculate approx. number of ticks between each blockade increase and random attack; set random attack value to zero if we're at a hideout
        if ($protected_hideout)
            $radar_prop = 0;
        else $radar_prop = ($radar_prop > 0) ? ceil($radar_prop ** -1) : 0;
        $radar_increase = ($radar_increase > 0) ? ceil($radar_increase ** -1) : 0;

        // Calculate danger level
        $danger = ($radar_prop > 0) ? floor($factory->stat_max_zombie_count() / 4) : 0;              // Base value: Max attack group size
        if (!$protected_hideout && $radar_prop <= 1.5 && $radar_prop > 0)     $danger += 2;    // Increase by 2 if we have a very high attack probability
        elseif (!$protected_hideout && $radar_prop <= 3 && $radar_prop > 0)   ++$danger;    // Increase by 1 if we have a high attack probability
        elseif ($radar_prop <= 15  || $radar_prop === 0)  --$danger;                           // Decrease by 1 if we have a very low attack probability
        if ($radar_increase !== 0 && $radar_increase <= 3)   ++$danger;   // Increase by 1 if we have a very high blocking speed
        $danger = min(5,max(($radar_prop > 0) ? 1 : 0,$danger));            // Confine danger to 0-5 range

        switch ($danger) {
            case 0: return 0;
            case 1: return 0.003;
            case 2: return 0.01;
            case 3: return 0.04;
            case 4: return 0.08;
            case 5: return 0.15;
            default: return 0;
        }
    }

    /**
     * Returns a list of available items
     *
     * @param string|null                  $classname Restrict items to a specific class and its descendants
     * @param Struct_ScriptItemSource|null $source
     *
     * @return Model_Items_Abstract_Item[]
     * @throws Exception
     */
    public static function get_items(?string $classname, ?Struct_ScriptItemSource $source = null): array {
        $source = $source ?: new Struct_ScriptItemSource();

        $player = $source->get_player();
        $proto = [];

        if ($source->from_player)
            $proto = array_merge($proto, $player->inventory()->get($classname));

        if ($source->from_location && $player->location())
            $proto = array_merge($proto, $player->location()->inventory()->get($classname));

        if ($source->from_others && !self::is_npc($player))
            foreach (self::at_location($player->location_class(), true, true) as $s_player)
                if ($s_player->uin() !== $player->uin())
                    $proto = array_merge($proto, $s_player->inventory()->get($classname));

        return array_values(array_filter($proto, $source->get_decider()));
    }

    /**
     * Returns the first available item from a list
     *
     * @param string                       $classname Restrict items to a specific class and its descendants
     * @param Struct_ScriptItemSource|null $source
     *
     * @return Model_Items_Abstract_Item|null
     * @throws Exception
     */

    public static function first_item(string $classname, ?Struct_ScriptItemSource $source = null): ?Model_Items_Abstract_Item {
        $l = static::get_items($classname, $source);

        return $l ? $l[0] : null;
    }


    /**
     * Returns a list of players, who currently stay at the location specified by $lid
     * @param int    $lid             Location; default is the active players location
     * @param bool   $include_players Include players
     * @param bool   $include_npcs    Include NPCs
     * @return Interface_Plentity[]|Model_Player[]
     * @throws Exception
     */
    public static function at_location($lid = null, $include_players = true, $include_npcs = true): array
    {
        if (!$include_players && !$include_npcs) return [];
        if ($lid === null) $lid = Globals::CurrentPlayerF()->location_class();

        $ret = [];
        if ($include_players)
            foreach (Globals::CurrentGameF()->players(true) as $s_player)
                if ($s_player->get_status()->alive() && $s_player->location_class() === $lid)
                    $ret[] = $s_player;
        if ($include_npcs)
            foreach (Globals::CurrentGameF()->npcs(true) as $s_player)
                if ($s_player->get_status()->alive() && $s_player->location_class() === $lid)
                    $ret[] = $s_player;

        return $ret;
    }

    /**
     * Returns a list of players, who currently stay at the location specified by $lid and can be used as comrades
     * @param int  $lid Location; default is the active players location
     * @param bool $include_players
     * @param bool $include_npcs
     * @return Model_Player[]
     * @throws Exception
     */
    public static function comrades($lid = null, $include_players = true, $include_npcs = false): array
    {
        if ($lid === null) $lid = Globals::CurrentPlayerF()->location_class();

        $ret = [];
        foreach (static::at_location($lid, $include_players, $include_npcs) as $p)
            if ($p->id() !== Globals::CurrentPlayerF()->id() && static::check_comrade($p))
                $ret[] = $p;

        return $ret;
    }

    /**
     * Returns true if the player specified by $pid is a comrade
     * @param number|string|Model_Player|Interface_Plentity $pid Player
     * @return Model_Player|boolean
     * @throws Exception
     */
    public static function check_comrade($pid)
    {
        //Check if PID is valid
        $r = null;
        if (is_numeric($pid))
            $r = Globals::CurrentGameF()->get_player($pid);
        elseif (is_string($pid))
            $r = Globals::CurrentGameF()->get_npc($pid);
        elseif (is_object($pid))
            $r = $pid;

        if (!$r) return false;

        //Check if player is in companion mode
        if (!$r->companion())
            return false;

        //Check if both players share the same location
        return ($r->location_class() === Globals::CurrentPlayerF()->location_class()) ? $r : false;
    }

    /**
     * Places a newly spawned item in the current locations inventory or tries to add it to the finders inventory
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $item         The item (can also be an array of items)
     * @param boolean                                               $log          True if you want a log message to be created
     * @param Model_Places_Abstract_Place                           $use_location The location; if not set, the current location is used
     * @return boolean
     * @throws Exception
     */
    public static function place_new_item($item, $log = true, $use_location = null): bool
    {
        $location = $use_location ?: Globals::CurrentPlayerF()->location();

        //Fix single element arrays
        if (is_array($item) && count($item) === 1)
            $item = $item[0];

        //Recursive call for multiple items
        if (is_array($item)) {
            foreach ($item as $single) static::place_new_item($single, false, $location);
            if ($log) $location->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DIGUP, $item));
            return true;
        }

        if ($log) $location->log()->add(new Model_Log_Types_Item(Model_Log_Types_Item::MLTI_DIGUP, $item));
        $try_to_take = Tool_System::instance_of($item, 'Interface_Autotaker');

        $msg = '';

        if ($try_to_take) {

            if (Tool_System::instance_of($item, Model_Items_Abstract_Ammo::cls())) {
                /** @var $belt Model_Items_Ammobelt */
                $belt = self::first_item(Model_Items_Ammobelt::cls(), Struct_ScriptItemSource::onlyPlayer());
                /** @var Model_Items_Abstract_Ammo $item */
                if ($belt && $item->can_take($msg)) {
                    if (!$item->take()) throw new LogicException('Inconsistent item transfer behaviour detected.');
                    $belt->add($item);
                    return true;
                }

            } else if ($item->can_take($msg)) {
                Globals::CurrentPlayerF()->inventory()->add($item);
                if (!$item->take()) throw new LogicException('Inconsistent item transfer behaviour detected.');
                return true;
            }
        }

        if ($location->inventory()->add($item) === false) return true;

        return false;
    }

    /**
     * Starts a simple zombie battle with a number of shamblers
     * @param number  $num_zmb         Number of attacking shamblers
     * @param number  $distance        Distance of the zombie group
     * @param string  $headline        String to use as headline for the battle log message; default is "Ein Kampf!"
     * @param boolean $limit_to_player Set true, if you want only the active player to become involved in battle; default is false
     * @param boolean $escapeable      Set true if the battle can be escaped from; default is false
     * @throws Kohana_Exception
     */
    public static function simple_battle($num_zmb, $distance, $headline = 'Ein Kampf!', $limit_to_player = false, $escapeable = false): void
    {
        self::combat([$limit_to_player ? [Globals::CurrentPlayerF()] : self::at_location(), [Model_Combat_Zombies_Shambler::factory()->count($num_zmb)]], $escapeable, $distance, Globals::CurrentPlayerF()->location(), $headline);
    }

    /**
     * Returns the primary hideout object
     * @param null|Model_Game $game
     * @return Model_Places_Home
     * @throws Exception
     */
    public static function home($game = null): Model_Places_Home
    {
        if ($game === null) $game = Globals::CurrentGameF();
        /** @var  Model_Places_Home $l */
        $l = $game->location(-2);
        return $l;
    }

    /**
     * Returns items from all hideouts
     * @param null|string $type Item type, or null for all items
     * @return Model_Items_Abstract_Item[]
     * @throws Exception
     */
    public static function get_home_items($type = null): array
    {
        $ret = array();
        foreach (Globals::CurrentGameF()->maps() as $map) foreach ($map->get_locations('Model_Places_Abstract_Hideout') as $id)
            foreach (Globals::CurrentGameF()->locationF($id)->inventory()->get($type) as $item)
                $ret[] = $item;
        return $ret;
    }

    /**
     * Returns the type of location specified by $lid
     * @param $lid int Location ID
     * @return bool|int false, if the location id is invalid; 0 for a standart location; 1 for a location node, 2 for a hideout
     * @throws Exception
     */
    public static function location_type($lid) {
        if (!($location = Globals::CurrentGameF()->location($lid)))
            return false;

        if (Tool_System::instance_of($location, 'Model_Places_Abstract_Hideout'))
            return 2;
        elseif (Tool_System::instance_of($location, 'Model_Places_Abstract_Node'))
            return 1;
        else return 0;
    }

    /**
     * @param Interface_Plentity $p
     * @return Model_Places_Abstract_Hideout|null
     * @throws Exception
     */
    public static function current_location_hideout($p = null): ?Model_Places_Abstract_Hideout {
        if ($p === null)
            $player = Globals::CurrentPlayerF();
        else $player = $p;

        if (static::location_type($player->location_class()) === 2) {
            /** @var Model_Places_Abstract_Hideout $l */
            $l = $player->location();
            return $l;
        }
            
        else return null;
    }

    /**
     * @param Model_Combat_Actor[][] $combatants
     * @param bool                   $escapeable
     * @param int                    $distance
     * @param null                   $location
     * @param string                 $title
     * @param null                   $text
     * @return Model_Combat_Field
     * @throws Kohana_Exception
     */
    public static function combat($combatants, $escapeable, $distance = 10, $location = null, $title = 'Ein Kampf!', $text = null): \Model_Combat_Field
    {
        $battle = Model_Combat_Field::factory();

        $non_combatants = [];
        $actual_combatants = [];
        $no_nc = true;

        foreach ($combatants as $fraction => $group) if (count($group) > 0) {
            $actual_combatants[$fraction] = $non_combatants[$fraction] = [];
            /** @var Model_Combat_Actor|Model_NPC_Nano $member */
            foreach ($group as $member) {
                if (Tool_System::instance_of($member, Model_NPC_Nano::cls()) && ($member->get_status()->retrieve('passout') || !$member->is_fighter())) {
                    $non_combatants[$fraction][] = $member;
                    $no_nc = false;
                }
                    
                else $actual_combatants[$fraction][] = $member;
            }
        }

        foreach ($actual_combatants as $fraction => $group)
            $battle->add_combatant($fraction + 1, $group);

        $battle->init_positions($distance)->begin($escapeable && $no_nc);

        foreach ($actual_combatants as $fraction => $group)
            if ($battle->count_group_members($fraction + 1) === 0)
                foreach ($non_combatants[$fraction] as $member) {
                    $member->get_status()->set_cause_of_death('Im Schlaf zerfetzt');
                    $member->kill();
                }

        if ($location === null)
            $location = Globals::CurrentPlayerF()->location();

        $battle->get_scene()->set_atmosphere($location->battle_location_type());

        //Upload to DB
        $vid = Model_Combat_Handler::upload(Globals::CurrentGameF()->id(), Globals::CurrentGameF()->season(), $battle);
        $location->log()->add(new Model_Log_Types_Battle($title, $text, $vid, $battle->get_scene()->summarize()));

        return $battle;
    }

    /**
     * Returns a DateTime object containing the in-universe time and date
     * @param number $t
     * @return DateTime
     * @throws Kohana_Exception
     */
    public static function get_daytime($t = null): \DateTime
    {
        if ($t === null) $t = Globals::CurrentGameF()->duration();
        $ticks = $t + Globals::CurrentGameF()->getDaytimeOffset();
        $days = floor($ticks/288);
        $d = new DateTime();
        $d->setDate(1998,(int)Kohana::$config->load('server.season'),2);
        $d->setTime(floor(($ticks%288)/12), 5 * ($ticks%12), 0);
        $d->add(new DateInterval("P{$days}D"));

        return $d;
    }

    /**
     * Returns the in-universe time of day
     * @param Model_Player|null $p
     * @return string ("night", "morning", "day", "evening")
     * @throws Kohana_Exception
*/
    public static function get_timeofday($p = null): ?string
    {
        if ($p && $p->location()->getPerpetualDayTime())
            return $p->location()->getPerpetualDayTime();
        switch (static::get_daytime()->format('G')) {
            case 22:case 23:case 0:case 1:case 2:case 3:case 4:case 5:
            return 'night'; break;
            case 6:case 7:case 8:case 9:
            return 'morning'; break;
            case 10:case 11:case 12:case 13:case 14:case 15:case 16:case 17:
            return 'day'; break;
            case 18:case 19:case 20:case 21:
            return 'evening'; break;
            default: return '';
        }
    }

    public static function calculate_find_chances($pid = null) {
        if ($pid === null)
            $player = Globals::CurrentPlayerF();
        else $player = Globals::CurrentGameF()->get_player($pid);

        $c = 1;

        $is_night = in_array(static::get_timeofday(), ['night','snowynight']);
        $is_light = false;

        //Flashlight Effect
        if (!$is_night && !$player->location()->is_outside() && ($fb = $player->get_status()->retrieve('flashlight')) && $fb->active())
            $c *= 1.2;
        elseif (($fb = $player->get_status()->retrieve('flashlight')) && $fb->active())
            $is_light = true;

        // Rudolphs Nose
        if (($rn = $player->get_status()->retrieve('rudolph')) && $rn->active())
            $c *= 1.5;

        //Night Malus
        if ($is_night && !$is_light)
            $c *= 0.25;
        //Fatigue Malus
        if ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY) < 50)
            $c *= ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY)/50);

        //Fatigue Bonus
        if ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY) > 90)
            $c *= ($player->get_status()->get(Model_Status::MS_STAT_SLEEPY)/90);

        //Drunk Malus
        $c *= (1 - ($player->get_status()->get(Model_Status::MS_STAT_DRUNK)/100));

        //Survivalist Boni
        if (!self::is_npc($player) && $player->job(1060)) {
            if ($player->job(1060, 5, false)) $c *= 1.15;
            elseif ($player->job(1060, 2, false)) $c *= 1.05;
        }

        //Child Bonus
        if (!self::is_npc($player) && $player->job(1080)) $c *= 1.5;

        //Item Spawnrate Stat
        $c *= $player->get_status()->get(Model_Status::MS_CHAR_ITEM_SPAWNRATE);

        return $c;
    }

    /**
     * @param string|null                                           $message Message
     * @param number                                                $cv      Chem Value
     * @param Model_Items_Abstract_Item                             $item    Target item
     * @param Model_Items_Abstract_Item|Model_Items_Abstract_Item[] $results Resulting items
     * @param number|null                                           $p       Player ID
     * @throws Exception
     */
    public static function chem_reaction($message, $cv, $item, $results = array(), $p = null): void
    {
        $player = Globals::CurrentGameF()->get_player($p);
        if ($player === null) return;

        if ($message)
            $player->log()->add($message);

        $player->location()->log()->add(new Model_Log_Types_Chem($cv, $item, $results, $p));
        static::place_new_item($results, false, $player->location());
    }

    /**
     * @param null|Interface_Plentity $player
     * @return Model_Items_Abstract_Transport|null
     * @throws Exception
     */
    public static function get_active_transport(?Interface_Plentity $player = null): ?Model_Items_Abstract_Transport {
        if ($player === null)
            $player = Globals::CurrentPlayerF();

        $selected = null;
        $items = $player->inventory()->get(Model_Items_Abstract_Transport::cls());
        foreach ($items as $item) {
            /** @var Model_Items_Abstract_Transport $item */
            /** @var Model_Items_Abstract_Transport $selected */
            if ($item->active() && ($selected === null || $item->speedup() > $selected->speedup()))
                $selected = $item;
        }

        return $selected;
    }

    /**
     * @param null|Model_Player|Interface_Plentity $player
     * @return bool
     * @throws Exception
     */
    public static function is_npc(?Interface_Plentity $player = null): bool {
        if (!($player = $player ?? Globals::CurrentPlayerF())) return false;
        return $player->type() !== Interface_Plentity::IC_NPC_NONPC;
    }
}
